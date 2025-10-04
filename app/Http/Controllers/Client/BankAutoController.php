<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\BankAuto;
use App\Models\Config;
use App\Models\User;
use App\Models\BankAutoDeposit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\CoinService;

class BankAutoController extends Controller
{
    public $coinBankAutoPercent;
    public $coinExchangeRate;
    public $bonusBaseAmount;
    public $bonusBaseCam;
    public $bonusDoubleAmount;
    public $bonusDoubleCam;

    public function __construct()
    {
        $this->coinBankAutoPercent = Config::getConfig('coin_bank_auto_percentage', 0);
        $this->coinExchangeRate = Config::getConfig('coin_exchange_rate', 10);
        $this->bonusBaseAmount = Config::getConfig('bonus_base_amount', 100000);
        $this->bonusBaseCam = Config::getConfig('bonus_base_cam', 300);
        $this->bonusDoubleAmount = Config::getConfig('bonus_double_amount', 200000);
        $this->bonusDoubleCam = Config::getConfig('bonus_double_cam', 1000);
    }

    public function index()
    {
        $user = Auth::user();
        $banks = BankAuto::where('status', true)->get();
        
        $coinExchangeRate = $this->coinExchangeRate;
        $coinBankAutoPercent = $this->coinBankAutoPercent;
        $bonusBaseAmount = $this->bonusBaseAmount;
        $bonusBaseCam = $this->bonusBaseCam;
        $bonusDoubleAmount = $this->bonusDoubleAmount;
        $bonusDoubleCam = $this->bonusDoubleCam;

        return view('pages.information.deposit.bank_auto', compact(
            'banks', 
            'coinExchangeRate',
            'coinBankAutoPercent',
            'bonusBaseAmount',
            'bonusBaseCam',
            'bonusDoubleAmount',
            'bonusDoubleCam'
        ));
    }

    /**
     * Tính toán số cám nhận được bao gồm bonus
     */
    public function calculateCoins($amount)
    {
        // Tính phí giao dịch
        $feeAmount = ($amount * $this->coinBankAutoPercent) / 100;
        $amountAfterFee = $amount - $feeAmount;
        
        // Tính cám cơ bản
        $baseCoins = floor($amountAfterFee / $this->coinExchangeRate);
        
        // Tính bonus
        $bonusCoins = $this->calculateBonus($amount);
        
        return [
            'base_coins' => $baseCoins,
            'bonus_coins' => $bonusCoins,
            'total_coins' => $baseCoins + $bonusCoins,
            'fee_amount' => $feeAmount,
            'amount_after_fee' => $amountAfterFee
        ];
    }

    /**
     * Tính toán bonus theo công thức hàm mũ
     * Công thức: bonus = a * (amount)^b
     * Trong đó:
     * - b = log(300000/100000)(1000/300) = log3(3.333...) ≈ 1.096
     * - a = 300/(100000)^b
     */
    private function calculateBonus($amount)
    {
        // Không có bonus dưới mốc cơ bản
        if ($amount < $this->bonusBaseAmount) {
            return 0;
        }
        
        // Tính số mũ b
        // b = log(300000/100000)(1000/300) = log3(3.333...) ≈ 1.096
        $ratioAmount = $this->bonusDoubleAmount / $this->bonusBaseAmount; // 300000/100000 = 3
        $ratioBonus = $this->bonusDoubleCam / $this->bonusBaseCam; // 1000/300 = 3.333...
        $b = log($ratioBonus) / log($ratioAmount); // ≈ 1.096
        
        // Tính hệ số a
        // a = 300/(100000)^b
        $a = $this->bonusBaseCam / pow($this->bonusBaseAmount, $b);
        
        // Tính bonus theo công thức: bonus = a * (amount)^b
        $bonus = $a * pow($amount, $b);
        
        return floor($bonus);
    }

    /**
     * API endpoint để tính toán preview coins
     */
    public function calculatePreview(Request $request)
    {
        $amount = $request->input('amount', 0);
        
        if ($amount < 5000) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền tối thiểu là 5.000 VNĐ'
            ]);
        }
        
        $calculation = $this->calculateCoins($amount);
        
        return response()->json([
            'success' => true,
            'data' => $calculation
        ]);
    }

    /**
     * Tạo giao dịch bank auto
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:5000',
            'bank_id' => 'required|exists:banks,id'
        ]);

        $amount = $request->input('amount');
        $bankId = $request->input('bank_id');
        
        // Tính toán coins
        $calculation = $this->calculateCoins($amount);
        
        // Tạo transaction code
        $transactionCode = 'BA' . time() . Str::random(6);
        
        DB::beginTransaction();
        try {
            // Tạo giao dịch bank auto
            $bankAutoDeposit = BankAutoDeposit::create([
                'user_id' => Auth::id(),
                'bank_id' => $bankId,
                'transaction_code' => $transactionCode,
                'amount' => $amount,
                'base_coins' => $calculation['base_coins'],
                'bonus_coins' => $calculation['bonus_coins'],
                'total_coins' => $calculation['total_coins'],
                'fee_amount' => $calculation['fee_amount'],
                'status' => BankAutoDeposit::STATUS_PENDING
            ]);

            DB::commit();

            // Trả về thông tin chuyển khoản cho user
            return response()->json([
                'success' => true,
                'transaction_code' => $transactionCode,
                'amount' => $amount,
                'coins' => $calculation['total_coins'],
                'bank_info' => $this->getBankInfo($bankId),
                'message' => 'Vui lòng chuyển khoản theo thông tin bên dưới'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating bank auto deposit: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo giao dịch'
            ]);
        }
    }

    /**
     * Lấy thông tin ngân hàng để hiển thị cho user
     */
    private function getBankInfo($bankId)
    {
        $bank = BankAuto::find($bankId);
        
        if (!$bank) {
            return null;
        }
        
        return [
            'name' => $bank->name,
            'code' => $bank->code,
            'account_number' => $bank->account_number ?? 'Chưa cấu hình',
            'account_name' => $bank->account_name ?? 'Chưa cấu hình',
            'logo' => $bank->logo ? Storage::url($bank->logo) : null,
            'qr_code' => $bank->qr_code ? Storage::url($bank->qr_code) : null,
        ];
    }

    /**
     * Callback từ Casso Webhook v2 khi có giao dịch mới
     * Reference: https://github.com/CassoHQ/casso-webhook-handler-sample/blob/main/webhook_handler.php
     */
    public function callback(Request $request)
    {
        // Lấy raw payload để verify signature
        $payload = $request->getContent();
        $signature = $request->header('X-Casso-Signature');
        
        Log::info('Casso webhook received', [
            'signature' => $signature,
            'payload_length' => strlen($payload),
            'headers' => $request->headers->all()
        ]);
        
        // Verify signature từ Casso Webhook v2
        if (!$signature) {
            Log::warning('Missing Casso signature header');
            return response()->json(['success' => false, 'message' => 'Missing signature'], 401);
        }
        
        if (!$this->verifyCassoSignature($payload, $signature)) {
            Log::warning('Invalid Casso signature', [
                'signature' => $signature,
                'payload_preview' => substr($payload, 0, 100)
            ]);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }
        
        // Parse JSON payload
        $data = json_decode($payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON payload', ['error' => json_last_error_msg()]);
            return response()->json(['success' => false, 'message' => 'Invalid JSON payload'], 400);
        }
        
        // Casso Webhook v2 format theo tài liệu chính thức
        $transactionId = $data['data']['id'] ?? null;
        $reference = $data['data']['reference'] ?? null;
        $description = $data['data']['description'] ?? '';
        $amount = $data['data']['amount'] ?? 0;
        $accountNumber = $data['data']['accountNumber'] ?? '';
        $bankName = $data['data']['bankName'] ?? '';
        $transactionDateTime = $data['data']['transactionDateTime'] ?? null;
        
        Log::info('Casso webhook data parsed', [
            'transaction_id' => $transactionId,
            'reference' => $reference,
            'amount' => $amount,
            'account_number' => $accountNumber,
            'bank_name' => $bankName
        ]);
        
        if (!$transactionId) {
            Log::warning('Missing transaction id in Casso webhook', ['data' => $data]);
            return response()->json(['success' => false, 'message' => 'Missing transaction id']);
        }
        
        // Kiểm tra xem giao dịch đã được xử lý chưa (chống trùng lặp)
        $existingDeposit = BankAutoDeposit::where('casso_transaction_id', $transactionId)
            ->where('status', BankAutoDeposit::STATUS_SUCCESS)
            ->first();
            
        if ($existingDeposit) {
            Log::info('Transaction already processed', ['transaction_id' => $transactionId]);
            return response()->json(['success' => true, 'message' => 'Transaction already processed']);
        }
        
        DB::beginTransaction();
        try {
            // Tìm deposit bằng reference (transaction code)
            $deposit = BankAutoDeposit::where('transaction_code', $reference)
                ->where('status', BankAutoDeposit::STATUS_PENDING)
                ->first();
                
            if (!$deposit) {
                Log::warning('Bank auto deposit not found', [
                    'reference' => $reference,
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['success' => false, 'message' => 'Giao dịch không tồn tại']);
            }
            
            // Kiểm tra số tiền nhận được (cho phép sai lệch nhỏ do phí chuyển khoản)
            $toleranceAmount = $deposit->amount * 0.99; // Cho phép sai lệch 1%
            if ($amount < $toleranceAmount) {
                Log::warning('Insufficient amount received', [
                    'expected' => $deposit->amount,
                    'tolerance' => $toleranceAmount,
                    'received' => $amount,
                    'reference' => $reference
                ]);
                
                $deposit->update([
                    'status' => BankAutoDeposit::STATUS_FAILED,
                    'note' => 'Số tiền nhận được không đủ',
                    'casso_response' => $data
                ]);
                
                DB::commit();
                return response()->json(['success' => false, 'message' => 'Số tiền không đủ']);
            }
            
            // Cập nhật trạng thái thành công
            $deposit->update([
                'status' => BankAutoDeposit::STATUS_SUCCESS,
                'processed_at' => now(),
                'casso_transaction_id' => $transactionId,
                'casso_response' => $data
            ]);
            
            // Cộng coins cho user
            $user = $deposit->user;
            if ($user) {
                $coinService = new CoinService();
                $coinService->addCoins(
                    $user,
                    $deposit->total_coins,
                    \App\Models\CoinHistory::TYPE_BANK_AUTO_DEPOSIT,
                    "Nạp bank auto thành công - Số tiền: " . number_format($deposit->amount) . " VND - Casso ID: {$transactionId}",
                    $deposit
                );
                
                Log::info('Bank auto deposit successful', [
                    'user_id' => $user->id,
                    'transaction_code' => $reference,
                    'casso_transaction_id' => $transactionId,
                    'coins_added' => $deposit->total_coins,
                    'amount_received' => $amount,
                    'bank_name' => $bankName
                ]);
            }
            
            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing bank auto callback: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }


    /**
     * Verify signature từ Casso Webhook v2 theo tài liệu chính thức
     * Reference: https://github.com/CassoHQ/casso-webhook-v2-verify-signature/blob/main/php.php
     */
    private function verifyCassoSignature($payload, $signature)
    {
        $secret = config('services.casso.webhook_secret');
        
        if (!$secret) {
            Log::error('Casso webhook secret not configured');
            return false;
        }
        
        // Casso Webhook v2 sử dụng HMAC-SHA256
        // Signature format: sha256=calculated_signature
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        
        // Sử dụng hash_equals để tránh timing attack
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Trang thành công
     */
    public function success()
    {
        return view('pages.information.deposit.bank_auto_success');
    }

    /**
     * Trang hủy
     */
    public function cancel()
    {
        return view('pages.information.deposit.bank_auto_cancel');
    }
}
