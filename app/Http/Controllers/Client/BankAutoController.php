<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Config;
use App\Models\User;
use App\Models\BankAutoDeposit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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
        $banks = Bank::where('status', true)->get();
        
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
     * Tính toán bonus dựa trên số tiền nạp
     */
    private function calculateBonus($amount)
    {
        $bonus = 0;
        
        // Nếu nạp >= bonus_base_amount thì có bonus cơ bản
        if ($amount >= $this->bonusBaseAmount) {
            $bonus += $this->bonusBaseCam;
        }
        
        // Nếu nạp >= bonus_double_amount thì có bonus gấp đôi
        if ($amount >= $this->bonusDoubleAmount) {
            $bonus += $this->bonusDoubleCam;
        }
        
        // Tính bonus theo tỷ lệ tăng dần
        if ($amount > $this->bonusDoubleAmount) {
            $excessAmount = $amount - $this->bonusDoubleAmount;
            $excessBonus = floor($excessAmount / 100000) * 50; // Mỗi 100k thêm 50 cám
            $bonus += $excessBonus;
        }
        
        return $bonus;
    }

    /**
     * API endpoint để tính toán preview coins
     */
    public function calculatePreview(Request $request)
    {
        $amount = $request->input('amount', 0);
        
        if ($amount < 50000) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền tối thiểu là 50.000 VNĐ'
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
            'amount' => 'required|integer|min:50000',
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

            // Tạo URL thanh toán Casso
            $paymentUrl = $this->createCassoPaymentUrl($amount, $transactionCode, $bankId);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'transaction_code' => $transactionCode,
                'amount' => $amount,
                'coins' => $calculation['total_coins']
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
     * Tạo URL thanh toán Casso
     */
    private function createCassoPaymentUrl($amount, $transactionCode, $bankId)
    {
        // TODO: Thay thế bằng API thực tế của Casso
        // Casso API endpoint để tạo payment
        $cassoApiUrl = 'https://api.casso.vn/v1/payments';
        
        $data = [
            'amount' => $amount,
            'description' => "Nạp cám tự động - {$transactionCode}",
            'order_id' => $transactionCode,
            'return_url' => route('user.bank.auto.success'),
            'cancel_url' => route('user.bank.auto.cancel'),
            'webhook_url' => route('user.bank.auto.deposit.callback'),
        ];
        
        // Headers cho Casso API
        $headers = [
            'Authorization' => 'Bearer ' . config('services.casso.api_key'),
            'Content-Type' => 'application/json',
        ];
        
        try {
            // Gọi API Casso để tạo payment URL
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($cassoApiUrl, $data);
                
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['payment_url'] ?? null;
            } else {
                Log::error('Casso API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error calling Casso API: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Callback từ Casso v2 khi thanh toán thành công
     */
    public function callback(Request $request)
    {
        // Verify signature từ Casso để đảm bảo tính bảo mật
        $signature = $request->header('X-Casso-Signature');
        $payload = $request->getContent();
        
        if (!$signature) {
            Log::warning('Missing Casso signature header');
            return response()->json(['success' => false, 'message' => 'Missing signature'], 401);
        }
        
        if (!$this->verifyCassoSignature($payload, $signature)) {
            Log::warning('Invalid Casso signature', ['signature' => $signature]);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }
        
        $data = $request->all();
        
        // Casso v2 webhook format
        $transactionCode = $data['order_id'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount'] ?? 0;
        
        if (!$transactionCode) {
            Log::warning('Missing order_id in Casso webhook', ['data' => $data]);
            return response()->json(['success' => false, 'message' => 'Missing order_id']);
        }
        
        // Chỉ xử lý khi status là success
        if ($status !== 'success') {
            Log::info('Casso webhook received with non-success status', [
                'transaction_code' => $transactionCode,
                'status' => $status
            ]);
            return response()->json(['success' => true, 'message' => 'Status not success, ignoring']);
        }
        
        DB::beginTransaction();
        try {
            $deposit = BankAutoDeposit::where('transaction_code', $transactionCode)
                ->where('status', BankAutoDeposit::STATUS_PENDING)
                ->first();
                
            if (!$deposit) {
                Log::warning('Bank auto deposit not found', ['transaction_code' => $transactionCode]);
                return response()->json(['success' => false, 'message' => 'Giao dịch không tồn tại']);
            }
            
            // Kiểm tra số tiền nhận được
            if ($amount < $deposit->amount) {
                Log::warning('Insufficient amount received', [
                    'expected' => $deposit->amount,
                    'received' => $amount,
                    'transaction_code' => $transactionCode
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
                    "Nạp bank auto thành công - Số tiền: " . number_format($deposit->amount) . " VND",
                    $deposit
                );
                
                Log::info('Bank auto deposit successful', [
                    'user_id' => $user->id,
                    'transaction_code' => $transactionCode,
                    'coins_added' => $deposit->total_coins,
                    'amount_received' => $amount
                ]);
            }
            
            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing bank auto callback: ' . $e->getMessage(), [
                'transaction_code' => $transactionCode,
                'data' => $data
            ]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }

    /**
     * Verify signature từ Casso v2
     */
    private function verifyCassoSignature($payload, $signature)
    {
        $secret = config('services.casso.webhook_secret');
        
        if (!$secret) {
            Log::error('Casso webhook secret not configured');
            return false;
        }
        
        // Casso v2 sử dụng HMAC-SHA256
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
