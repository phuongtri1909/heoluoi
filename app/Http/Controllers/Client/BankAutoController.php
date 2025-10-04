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
        
        if ($amount < 2000) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền tối thiểu là 2.000 VNĐ'
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
            'amount' => 'required|integer|min:2000',
            'bank_id' => 'required|exists:bank_autos,id'
        ]);

        $amount = $request->input('amount');
        $bankId = $request->input('bank_id');
        
        // Tính toán coins
        $calculation = $this->calculateCoins($amount);
        
        $transactionCode = 'HEOLUOI' . time() . strtoupper(Str::random(6)) . Auth::id();
        
        DB::beginTransaction();
        try {
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
     * Callback từ Casso Webhook v1 khi có giao dịch mới
     * Format: t=timestamp,v1=signature
     */
    public function callback(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Casso-Signature');
        
        Log::info('Casso webhook received', [
            'signature' => $signature,
            'payload_length' => strlen($payload),
            'headers' => $request->headers->all()
        ]);
        
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
        
        // Casso Webhook v2 format
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
            'bank_name' => $bankName,
            'description' => $description
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
            $transactionCode = null;
            
            if (preg_match_all('/(HEOLUOI[a-zA-Z0-9]{14,})/', $description, $matches)) {
                $transactionCode = $matches[1][0];
                
                Log::info('Found transaction codes in description', [
                    'description' => $description,
                    'all_codes' => $matches[1],
                    'selected_code' => $transactionCode
                ]);
            }
            
            Log::info('Extracted transaction code', [
                'description' => $description,
                'extracted_code' => $transactionCode
            ]);
            
            $deposit = null;
            if ($transactionCode) {
                $deposit = BankAutoDeposit::where('transaction_code', $transactionCode)
                    ->where('status', BankAutoDeposit::STATUS_PENDING)
                    ->first();
            }
                
            if (!$deposit) {
                Log::warning('Bank auto deposit not found', [
                    'reference' => $reference,
                    'transaction_id' => $transactionId,
                    'description' => $description,
                    'extracted_code' => $transactionCode
                ]);
                return response()->json(['success' => false, 'message' => 'Giao dịch không tồn tại']);
            }
            
            $toleranceAmount = $deposit->amount * 0.99;
            if ($amount < $toleranceAmount) {
                Log::warning('Insufficient amount received', [
                    'expected' => $deposit->amount,
                    'tolerance' => $toleranceAmount,
                    'received' => $amount,
                    'reference' => $reference,
                    'description' => $description
                ]);
                
                $deposit->update([
                    'status' => BankAutoDeposit::STATUS_FAILED,
                    'note' => 'Số tiền nhận được không đủ',
                    'casso_response' => $data
                ]);
                
                DB::commit();
                return response()->json(['success' => false, 'message' => 'Số tiền không đủ']);
            }
            
            $deposit->update([
                'status' => BankAutoDeposit::STATUS_SUCCESS,
                'processed_at' => now(),
                'casso_transaction_id' => $transactionId,
                'casso_response' => $data
            ]);
            
            // Broadcast SSE event để notify frontend
            $this->broadcastTransactionUpdate($transactionCode, 'success', $deposit);
            
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
                    'bank_name' => $bankName,
                    'description' => $description
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
                'trace' => $e->getTraceAsString(),
                'description' => $description
            ]);
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }


    /**
     * Verify signature từ Casso Webhook v1
     * Format: t=timestamp,v1=signature
     * Algorithm: SHA512 với sorted data
     */
    private function verifyCassoSignature($payload, $signature)
    {
        $secret = config('services.casso.webhook_secret');
        
        if (!$secret) {
            Log::error('Casso webhook secret not configured');
            return false;
        }
        
        // Parse signature format: t=timestamp,v1=signature
        if (!preg_match('/t=(\d+),v1=(.+)/', $signature, $matches)) {
            Log::warning('Invalid signature format', ['signature' => $signature]);
            return false;
        }
        
        $timestamp = $matches[1];
        $receivedSignature = $matches[2];
        
        // Check timestamp (within 5 minutes)
        $currentTime = time() * 1000; // Convert to milliseconds
        $signatureTime = (int)$timestamp;
        $timeDiff = abs($currentTime - $signatureTime);
        
        if ($timeDiff > 300000) { // 5 minutes in milliseconds
            Log::warning('Signature timestamp too old', [
                'current_time' => $currentTime,
                'signature_time' => $signatureTime,
                'time_diff' => $timeDiff
            ]);
            return false;
        }
        
        // Parse JSON payload
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON payload for signature verification', ['error' => json_last_error_msg()]);
            return false;
        }
        
        // Sort data by key recursively
        $sortedData = $this->sortDataByKey($data);
        
        // Create message to sign: timestamp.json_encode(sortedData)
        $messageToSign = $timestamp . '.' . json_encode($sortedData, JSON_UNESCAPED_SLASHES);
        
        // Calculate expected signature using SHA512
        $expectedSignature = hash_hmac('sha512', $messageToSign, $secret);
        
        // Use hash_equals to prevent timing attacks
        return hash_equals($expectedSignature, $receivedSignature);
    }
    
    /**
     * Sort data by key recursively
     */
    private function sortDataByKey($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        
        $sortedData = [];
        $keys = array_keys($data);
        sort($keys);
        
        foreach ($keys as $key) {
            if (is_array($data[$key])) {
                $sortedData[$key] = $this->sortDataByKey($data[$key]);
            } else {
                $sortedData[$key] = $data[$key];
            }
        }
        
        return $sortedData;
    }
    
    /**
     * Broadcast transaction update via SSE
     */
    private function broadcastTransactionUpdate($transactionCode, $status, $deposit)
    {
        // Tạo file để store SSE data
        $sseData = [
            'transaction_code' => $transactionCode,
            'status' => $status,
            'deposit_id' => $deposit->id,
            'amount' => $deposit->amount,
            'total_coins' => $deposit->total_coins,
            'timestamp' => now()->toISOString(),
        ];
        
        // Lưu vào file để SSE endpoint có thể đọc
        $filename = storage_path('app/sse_transaction_' . $transactionCode . '.json');
        file_put_contents($filename, json_encode($sseData));
        
        Log::info('SSE transaction update broadcasted', [
            'transaction_code' => $transactionCode,
            'status' => $status,
            'filename' => $filename
        ]);
    }
    
    /**
     * SSE endpoint để listen transaction updates
     */
    public function sseTransactionUpdates(Request $request)
    {
        $transactionCode = $request->get('transaction_code');
        
        if (!$transactionCode) {
            return response('Missing transaction_code', 400);
        }
        
        // Set headers cho SSE
        return response()->stream(function () use ($transactionCode) {
            $filename = storage_path('app/sse_transaction_' . $transactionCode . '.json');
            $lastModified = 0;
            
            while (true) {
                // Kiểm tra file có thay đổi không
                if (file_exists($filename)) {
                    $currentModified = filemtime($filename);
                    
                    if ($currentModified > $lastModified) {
                        $data = json_decode(file_get_contents($filename), true);
                        
                        // Gửi SSE event
                        echo "data: " . json_encode($data) . "\n\n";
                        
                        $lastModified = $currentModified;
                        
                        // Nếu status là success, close connection
                        if ($data['status'] === 'success') {
                            echo "data: " . json_encode(['type' => 'close']) . "\n\n";
                            break;
                        }
                    }
                }
                
                // Sleep 1 giây trước khi check lại
                sleep(1);
                
                // Check connection
                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Cache-Control',
        ]);
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
