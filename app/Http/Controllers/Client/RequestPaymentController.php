<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Config;
use App\Models\Deposit;
use App\Models\RequestPayment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DepositNotificationMail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
class RequestPaymentController extends Controller
{

    public $coinBankPercent;
    public $coinPayPalPercent;
    public $coinCardPercent;

    public $coinExchangeRate;
    public $coinPayPalRate;

    // Bonus config
    public $bonusBaseAmount;
    public $bonusBaseCam;
    public $bonusDoubleAmount;
    public $bonusDoubleCam;

    public function __construct()
    {
        $this->coinBankPercent = Config::getConfig('coin_bank_percentage', 15);
        $this->coinPayPalPercent = Config::getConfig('coin_paypal_percentage', 0);
        $this->coinCardPercent = Config::getConfig('coin_card_percentage', 30);

        $this->coinExchangeRate = Config::getConfig('coin_exchange_rate', 100);
        $this->coinPayPalRate = Config::getConfig('coin_paypal_rate', 20000);

        // Bonus config
        $this->bonusBaseAmount = Config::getConfig('bonus_base_amount', 100000);
        $this->bonusBaseCam = Config::getConfig('bonus_base_cam', 300);
        $this->bonusDoubleAmount = Config::getConfig('bonus_double_amount', 200000);
        $this->bonusDoubleCam = Config::getConfig('bonus_double_cam', 1000);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'amount' => [
                'required',
                'numeric',
                'min:50000',
                function ($attribute, $value, $fail) {
                    if ($value % 10000 !== 0) {
                        $fail('Số tiền phải là bội số của 10.000 VNĐ (ví dụ: 50.000, 60.000, 70.000...)');
                    }
                },
            ],
        ], [
            'bank_id.required' => 'Vui lòng chọn ngân hàng',
            'bank_id.exists' => 'Ngân hàng không tồn tại',
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền tối thiểu là 50.000 VNĐ',
        ]);

        try {
            $amount = $request->amount;

            $feeAmount = ($amount * $this->coinBankPercent) / 100;
            $amountAfterFee = $amount - $feeAmount;
            $baseCoins = floor($amountAfterFee / $this->coinExchangeRate);

            // Calculate bonus coins
            $bonusCoins = calculateBonusCoins($amountAfterFee, $this->bonusBaseAmount, $this->bonusBaseCam, $this->bonusDoubleAmount, $this->bonusDoubleCam);
            $totalCoins = $baseCoins + $bonusCoins;

            $transactionCode = $this->generateUniqueTransactionCode();

            $expiredAt = Carbon::now()->addHour();

            $requestPayment = RequestPayment::create([
                'user_id' => Auth::id(),
                'bank_id' => $request->bank_id,
                'transaction_code' => $transactionCode,
                'amount' => $amount,
                'coins' => $totalCoins,
                'base_coins' => $baseCoins,
                'bonus_coins' => $bonusCoins,
                'total_coins' => $totalCoins,
                'fee' => $feeAmount,
                'expired_at' => $expiredAt
            ]);

            $bank = Bank::findOrFail($request->bank_id);

            return response()->json([
                'success' => true,
                'request_payment_id' => $requestPayment->id,
                'bank' => [
                    'id' => $bank->id,
                    'name' => $bank->name,
                    'code' => $bank->code,
                    'account_number' => $bank->account_number,
                    'account_name' => $bank->account_name,
                    'qr_code' => $bank->qr_code ? Storage::url($bank->qr_code) : null,
                ],
                'payment' => [
                    'amount' => $amount,
                    'coins' => $totalCoins,
                    'base_coins' => $baseCoins,
                    'bonus_coins' => $bonusCoins,
                    'total_coins' => $totalCoins,
                    'fee' => $feeAmount,
                    'transaction_code' => $transactionCode,
                    'expired_at' => $expiredAt->toIso8601String()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating request payment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo yêu cầu thanh toán'
            ], 500);
        }
    }

    private function generateUniqueTransactionCode()
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $attempt++;
            $userId = Auth::id();

            $shortTimestamp = (time() % 100000);

            $timestampBase36 = strtoupper(base_convert($shortTimestamp, 10, 36));
            $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2));

            $transactionCode = "P{$userId}{$timestampBase36}{$random}";

            $exists = RequestPayment::where('transaction_code', $transactionCode)->exists() ||
                Deposit::where('transaction_code', $transactionCode)->exists();
        } while ($exists && $attempt < $maxAttempts);

        if ($exists) {
            $shortUuid = strtoupper(substr(str_replace('-', '', Str::uuid()), 0, 8));
            $transactionCode = "P{$userId}{$shortUuid}";
        }

        return $transactionCode;
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'request_payment_id' => 'required|exists:request_payments,id',
            'transaction_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            'request_payment_id.required' => 'Mã yêu cầu thanh toán không hợp lệ',
            'request_payment_id.exists' => 'Yêu cầu thanh toán không tồn tại',
            'transaction_image.required' => 'Vui lòng tải lên ảnh chứng minh chuyển khoản',
            'transaction_image.image' => 'File tải lên phải là hình ảnh',
            'transaction_image.mimes' => 'Định dạng hình ảnh phải là jpeg, png, jpg hoặc gif',
            'transaction_image.max' => 'Kích thước hình ảnh không được vượt quá 10MB',
        ]);

        $requestPayment = RequestPayment::findOrFail($request->request_payment_id);

        if ($requestPayment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xác nhận yêu cầu thanh toán này.'
            ], 403);
        }

        if ($requestPayment->is_completed && $requestPayment->deposit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu thanh toán đã được xử lý.'
            ], 400);
        }

        if ($requestPayment->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu thanh toán đã hết hạn. Vui lòng tạo yêu cầu mới.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $imagePath = $this->processAndSaveDepositImage($request->file('transaction_image'));

            $deposit = Deposit::create([
                'user_id' => Auth::id(),
                'bank_id' => $requestPayment->bank_id,
                'transaction_code' => $requestPayment->transaction_code,
                'amount' => $requestPayment->amount,
                'coins' => $requestPayment->total_coins,
                'base_coins' => $requestPayment->base_coins,
                'bonus_coins' => $requestPayment->bonus_coins,
                'total_coins' => $requestPayment->total_coins,
                'fee' => $requestPayment->fee,
                'image' => $imagePath,
                'status' => 'pending',
            ]);

            $deposit->load(['user', 'bank']);

            $requestPayment->markAsCompleted($deposit->id);

            $this->sendDepositNotificationToAdmin($deposit);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu nạp cám đã được gửi. Chúng tôi sẽ kiểm tra và xử lý trong thời gian sớm nhất.',
                'deposit_id' => $deposit->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('Error in deposit confirmation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận yêu cầu nạp cám. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Gửi email thông báo yêu cầu nạp tiền cho super admin
     *
     * @param Deposit $deposit
     * @return void
     */
    private function sendDepositNotificationToAdmin(Deposit $deposit)
    {
        try {
            $superAdminEmails = env('SUPER_ADMIN_EMAILS');

            if (empty($superAdminEmails)) {
                Log::warning('SUPER_ADMIN_EMAILS not configured in .env file');
                return;
            }

            $emailArray = explode(',', $superAdminEmails);
            $emailArray = array_map('trim', $emailArray);
            $emailArray = array_filter($emailArray);

            foreach ($emailArray as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new DepositNotificationMail($deposit));
                } else {
                    Log::warning("Invalid email address in SUPER_ADMIN_EMAILS: {$email}");
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send deposit notification email: ' . $e->getMessage());
        }
    }

    private function processAndSaveDepositImage($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "deposit_{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("deposits/{$yearMonth}");

        $image = Image::make($imageFile);
        $image->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->encode('webp', 85);

        Storage::disk('public')->put(
            "deposits/{$yearMonth}/{$fileName}.webp",
            $image->stream()
        );

        return "deposits/{$yearMonth}/{$fileName}.webp";
    }
}
