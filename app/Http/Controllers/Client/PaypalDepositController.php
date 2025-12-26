<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Config;
use App\Models\PaypalDeposit;
use App\Models\RequestPaymentPaypal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class PaypalDepositController extends Controller
{
    public $coinExchangeRate;
    public $coinPaypalRate;
    public $coinPaypalPercent;
    public $paypalMeLink;

    // Bonus config
    public $bonusBaseAmount;
    public $bonusBaseCam;
    public $bonusDoubleAmount;
    public $bonusDoubleCam;

    public function __construct()
    {
        $this->coinExchangeRate = Config::getConfig('coin_exchange_rate', 100);
        $this->coinPaypalRate = Config::getConfig('coin_paypal_rate', 20000);
        $this->coinPaypalPercent = Config::getConfig('coin_paypal_percentage', 0);
        $this->paypalMeLink = Config::getConfig('paypal_me_link', 'https://www.paypal.com/paypalme/minhnguyen231');

        // Bonus config
        $this->bonusBaseAmount = Config::getConfig('bonus_base_amount', 100000);
        $this->bonusBaseCam = Config::getConfig('bonus_base_cam', 300);
        $this->bonusDoubleAmount = Config::getConfig('bonus_double_amount', 200000);
        $this->bonusDoubleCam = Config::getConfig('bonus_double_cam', 1000);
    }

    /**
     * Display PayPal deposit page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $paypalDeposits = PaypalDeposit::where('user_id', $user->id)
            ->with('requestPaymentPaypal')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $latestConfirmedRequest = RequestPaymentPaypal::where('user_id', $user->id)
            ->where('payment_type', RequestPaymentPaypal::TYPE_PAYPAL)
            ->where('status', RequestPaymentPaypal::STATUS_CONFIRMED)
            ->orderBy('created_at', 'desc')
            ->first();

        $pendingRequestsQuery = RequestPaymentPaypal::where('user_id', $user->id)
            ->where('payment_type', RequestPaymentPaypal::TYPE_PAYPAL)
            ->where('status', RequestPaymentPaypal::STATUS_PENDING);

        if ($latestConfirmedRequest) {
            $pendingRequestsQuery->where('created_at', '>', $latestConfirmedRequest->created_at);
        }

        $pendingRequests = $pendingRequestsQuery
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        // Bonus config
        $bonusBaseAmount = Config::getConfig('bonus_base_amount', 100000);
        $bonusBaseCam = Config::getConfig('bonus_base_cam', 300);
        $bonusDoubleAmount = Config::getConfig('bonus_double_amount', 200000);
        $bonusDoubleCam = Config::getConfig('bonus_double_cam', 1000);

        return view('pages.information.deposit.paypal_deposit', compact(
            'user',
            'paypalDeposits',
            'pendingRequests',
            'bonusBaseAmount',
            'bonusBaseCam',
            'bonusDoubleAmount',
            'bonusDoubleCam'
        ))->with([
            'coinExchangeRate' => $this->coinExchangeRate,
            'coinPaypalRate' => $this->coinPaypalRate,
            'coinPaypalPercent' => $this->coinPaypalPercent,
            'paypalMeLink' => $this->paypalMeLink
        ]);
    }

    /**
     * Create PayPal payment request
     */
    public function store(Request $request)
    {
        $request->validate([
            'base_usd_amount' => [
                'required',
                'numeric',
                'min:5',
                function ($attribute, $value, $fail) {
                    if ($value % 5 !== 0) {
                        $fail('Số tiền phải là bội số của $5 (ví dụ: $5, $10, $15, $20...)');
                    }
                },
            ],
            'usd_amount' => [
                'required',
                'numeric',
                'min:5',
                function ($attribute, $value, $fail) {
                    if ($value % 5 !== 0) {
                        $fail('Số tiền phải là bội số của $5 (ví dụ: $5, $10, $15, $20...)');
                    }
                },
            ],
            'payment_method' => 'required|in:friends_family,goods_services',
            'paypal_email' => 'required|email|max:255'
        ], [
            'base_usd_amount.required' => 'Vui lòng nhập số tiền USD',
            'base_usd_amount.numeric' => 'Số tiền phải là số',
            'base_usd_amount.min' => 'Số tiền tối thiểu là $5',
            'usd_amount.required' => 'Vui lòng nhập số tiền USD',
            'usd_amount.numeric' => 'Số tiền phải là số',
            'usd_amount.min' => 'Số tiền tối thiểu là $5',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'paypal_email.required' => 'Vui lòng nhập email PayPal',
            'paypal_email.email' => 'Email PayPal không hợp lệ'
        ]);

        $baseUsdAmount = $request->base_usd_amount;
        $totalUsdAmount = $request->usd_amount;
        $paymentMethod = $request->payment_method;

        // Rate limiting
        $recentAttempts = RequestPaymentPaypal::where('user_id', Auth::id())
            ->where('payment_type', RequestPaymentPaypal::TYPE_PAYPAL)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentAttempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã tạo quá nhiều yêu cầu trong 1 giờ qua. Vui lòng thử lại sau.'
            ], 429);
        }

        $expectedTotal = $paymentMethod === 'goods_services' ? $baseUsdAmount * 1.2 : $baseUsdAmount;
        if (abs($totalUsdAmount - $expectedTotal) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền không khớp với số tiền dự kiến. Vui lòng kiểm tra lại.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $vndAmount = $baseUsdAmount * $this->coinPaypalRate;
            $feeAmount = ($vndAmount * $this->coinPaypalPercent) / 100;
            $amountAfterFee = $vndAmount - $feeAmount;
            $baseCoins = floor($amountAfterFee / $this->coinExchangeRate);

            // Calculate bonus coins
            $bonusCoins = calculateBonusCoins($amountAfterFee, $this->bonusBaseAmount, $this->bonusBaseCam, $this->bonusDoubleAmount, $this->bonusDoubleCam);
            $totalCoins = $baseCoins + $bonusCoins;

            // Generate transaction code
            $transactionCode = RequestPaymentPaypal::generateTransactionCode('PP');

            // Generate PayPal.me URL
            $paypalUrl = $this->paypalMeLink . '/' . $totalUsdAmount . 'USD';

            // Short payment content
            $paymentContent = $transactionCode;

            // Create payment request
            $requestPayment = RequestPaymentPaypal::create([
                'user_id' => Auth::id(),
                'payment_type' => RequestPaymentPaypal::TYPE_PAYPAL,
                'usd_amount' => $totalUsdAmount,
                'base_usd_amount' => $baseUsdAmount,
                'payment_method' => $paymentMethod,
                'vnd_amount' => $vndAmount,
                'coins' => $totalCoins,
                'base_coins' => $baseCoins,
                'bonus_coins' => $bonusCoins,
                'total_coins' => $totalCoins,
                'exchange_rate' => $this->coinPaypalRate,
                'fee_percent' => $this->coinPaypalPercent,
                'fee_amount' => $feeAmount,
                'transaction_code' => $transactionCode,
                'paypal_email' => $request->paypal_email,
                'paypal_me_link' => $paypalUrl,
                'content' => $paymentContent,
                'status' => RequestPaymentPaypal::STATUS_PENDING,
                'expired_at' => now()->addHours(24), // 24 hours to complete
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu thanh toán PayPal đã được tạo thành công',
                'request_payment_id' => $requestPayment->id,
                'transaction_code' => $transactionCode,
                'paypal_url' => $paypalUrl,
                'payment_content' => $paymentContent,
                'usd_amount' => $totalUsdAmount,
                'usd_amount_formatted' => '$' . number_format($totalUsdAmount, 2),
                'base_usd_amount' => $baseUsdAmount,
                'base_usd_amount_formatted' => '$' . number_format($baseUsdAmount, 2),
                'payment_method' => $paymentMethod,
                'vnd_amount' => $vndAmount,
                'coins' => $totalCoins,
                'base_coins' => $baseCoins,
                'bonus_coins' => $bonusCoins,
                'total_coins' => $totalCoins,
                'fee_amount' => $feeAmount,
                'paypal_email' => $request->paypal_email,
                'expired_at' => $requestPayment->expired_at->toISOString()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo yêu cầu thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm PayPal payment with evidence
     */
    public function confirm(Request $request)
    {

        $request->validate([
            'transaction_code' => 'required|string|exists:request_payment_paypals,transaction_code',
            'evidence_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ], [
            'transaction_code.required' => 'Mã giao dịch không hợp lệ',
            'transaction_code.exists' => 'Không tìm thấy yêu cầu thanh toán',
            'evidence_image.required' => 'Vui lòng tải lên ảnh chứng minh',
            'evidence_image.image' => 'File phải là hình ảnh',
            'evidence_image.mimes' => 'Chỉ hỗ trợ định dạng: jpeg, png, jpg, gif',
            'evidence_image.max' => 'Kích thước file không được vượt quá 10MB'
        ]);

        $requestPayment = RequestPaymentPaypal::where('transaction_code', $request->transaction_code)
            ->where('user_id', Auth::id())
            ->where('payment_type', RequestPaymentPaypal::TYPE_PAYPAL)
            ->first();

        if (!$requestPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu thanh toán hoặc bạn không có quyền truy cập'
            ], 404);
        }

        if ($requestPayment->status !== RequestPaymentPaypal::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu thanh toán này đã được xử lý hoặc không thể cập nhật'
            ], 400);
        }

        if ($requestPayment->is_expired) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu thanh toán đã hết hạn. Vui lòng tạo yêu cầu mới.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Upload evidence image
            $imagePath = $request->file('evidence_image')->store('paypal_deposits', 'public');

            // Confirm payment and create PayPal deposit
            $paypalDeposit = $requestPayment->confirmPayment($imagePath);

            $requestPayment->status = RequestPaymentPaypal::STATUS_CONFIRMED;
            $requestPayment->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi chứng từ thanh toán thành công! Giao dịch sẽ được xử lý trong vòng 24 giờ.',
                'paypal_deposit_id' => $paypalDeposit->id,
                'status' => 'processing'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý yêu cầu: ' . $e->getMessage()
            ], 500);
        }
    }
}
