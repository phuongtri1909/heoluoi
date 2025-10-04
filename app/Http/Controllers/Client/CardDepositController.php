<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Config;
use App\Models\CardDeposit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use App\Http\Controllers\Controller;
use App\Services\CoinService;

class CardDepositController extends Controller
{
    public $coinExchangeRate;
    public $coinCardPercent;
    public $cardWrongAmountPenalty;
    public $tsrPartnerKey;
    public $tsrPartnerId;

    // Bonus config
    public $bonusBaseAmount;
    public $bonusBaseCam;
    public $bonusDoubleAmount;
    public $bonusDoubleCam;

    public function __construct()
    {
        $this->coinExchangeRate = Config::getConfig('coin_exchange_rate', 100);
        $this->coinCardPercent = Config::getConfig('coin_card_percentage', 20);
        $this->cardWrongAmountPenalty = Config::getConfig('card_wrong_amount_penalty', 50);
        $this->tsrPartnerKey = env('TSR_PARTNER_KEY', '');
        $this->tsrPartnerId = env('TSR_PARTNER_ID', '');

        // Bonus config
        $this->bonusBaseAmount = Config::getConfig('bonus_base_amount', 100000);
        $this->bonusBaseCam = Config::getConfig('bonus_base_cam', 300);
        $this->bonusDoubleAmount = Config::getConfig('bonus_double_amount', 200000);
        $this->bonusDoubleCam = Config::getConfig('bonus_double_cam', 1000);
    }

    public function index()
    {
        $user = Auth::user();

        $cardDeposits = CardDeposit::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $coinCardPercent = $this->coinCardPercent;
        $coinExchangeRate = $this->coinExchangeRate;
        
        // Bonus config
        $bonusBaseAmount = Config::getConfig('bonus_base_amount', 100000);
        $bonusBaseCam = Config::getConfig('bonus_base_cam', 300);
        $bonusDoubleAmount = Config::getConfig('bonus_double_amount', 200000);
        $bonusDoubleCam = Config::getConfig('bonus_double_cam', 1000);

        return view('pages.information.deposit.card_deposit', compact(
            'user',
            'cardDeposits',
            'coinCardPercent',
            'coinExchangeRate',
            'bonusBaseAmount',
            'bonusBaseCam',
            'bonusDoubleAmount',
            'bonusDoubleCam'
        ));
    }

    public function store(Request $request)
    {
        $allowedCardTypes = array_keys(CardDeposit::CARD_TYPES);
        $allowedAmounts = array_keys(CardDeposit::CARD_VALUES);

        $request->validate([
            'telco' => 'required|in:' . implode(',', $allowedCardTypes),
            'serial' => 'required|string|min:10|max:20',
            'code' => 'required|string|min:10|max:20',
            'amount' => 'required|integer|in:' . implode(',', $allowedAmounts)
        ], [
            'telco.required' => 'Vui lòng chọn loại thẻ',
            'telco.in' => 'Loại thẻ không hợp lệ',
            'serial.required' => 'Vui lòng nhập số serial',
            'serial.min' => 'Số serial phải có ít nhất 10 ký tự',
            'serial.max' => 'Số serial không được quá 20 ký tự',
            'code.required' => 'Vui lòng nhập mã thẻ',
            'code.min' => 'Mã thẻ phải có ít nhất 10 ký tự',
            'code.max' => 'Mã thẻ không được quá 20 ký tự',
            'amount.required' => 'Vui lòng chọn mệnh giá',
            'amount.in' => 'Mệnh giá không hợp lệ'
        ]);

        $existingSuccessCard = CardDeposit::where('serial', $request->serial)
            ->where('pin', $request->code)
            ->where('status', CardDeposit::STATUS_SUCCESS)
            ->first();

        if ($existingSuccessCard) {

            return response()->json([
                'success' => false,
                'message' => 'Thẻ này đã được nạp thành công trước đó vào lúc ' .
                    $existingSuccessCard->processed_at->format('d/m/Y H:i:s') .
                    '. Không thể sử dụng lại thẻ đã nạp.',
                'existing_status' => 'success',
                'processed_at' => $existingSuccessCard->processed_at->format('d/m/Y H:i:s')
            ], 400);
        }

        $existingProcessingCard = CardDeposit::where('serial', $request->serial)
            ->where('pin', $request->code)
            ->whereIn('status', [CardDeposit::STATUS_PENDING, CardDeposit::STATUS_PROCESSING])
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        if ($existingProcessingCard) {
            if ($existingProcessingCard->user_id == Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thẻ này đang được xử lý. Vui lòng chờ kết quả.',
                    'existing_status' => $existingProcessingCard->status,
                    'card_deposit_id' => $existingProcessingCard->id,
                    'created_at' => $existingProcessingCard->created_at->format('d/m/Y H:i:s')
                ], 400);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Thẻ này đang được xử lý bởi người dùng khác.',
                    'existing_status' => 'processing'
                ], 400);
            }
        }

        $recentAttempts = CardDeposit::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentAttempts >= 30) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nạp quá nhiều lần trong 1 giờ qua. Vui lòng thử lại sau.',
                'rate_limit' => true,
                'attempts' => $recentAttempts,
                'max_attempts' => 5
            ], 429);
        }

        try {
            DB::beginTransaction();

            $amount = $request->amount;
            $feePercent = $this->coinCardPercent;
            $feeAmount = ($amount * $feePercent) / 100;
            $amountAfterFee = $amount - $feeAmount;
            $baseCoins = floor($amountAfterFee / $this->coinExchangeRate);

            // Calculate bonus coins
            $bonusCoins = calculateBonusCoins($amountAfterFee, $this->bonusBaseAmount, $this->bonusBaseCam, $this->bonusDoubleAmount, $this->bonusDoubleCam);
            $totalCoins = $baseCoins + $bonusCoins;

            $requestId = $this->generateUniqueRequestId();

            $cardDeposit = CardDeposit::create([
                'user_id' => Auth::id(),
                'type' => $request->telco,
                'serial' => $request->serial,
                'pin' => $request->code,
                'amount' => $amount,
                'coins' => $totalCoins,
                'base_coins' => $baseCoins,
                'bonus_coins' => $bonusCoins,
                'total_coins' => $totalCoins,
                'fee_percent' => $feePercent,
                'fee_amount' => $feeAmount,
                'request_id' => $requestId,
                'status' => CardDeposit::STATUS_PENDING,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $apiResponse = $this->callTSRApi($cardDeposit);

            if ($apiResponse['success']) {
                $cardDeposit->markAsProcessing(
                    $apiResponse['transaction_id'] ?? null,
                    $apiResponse['response_data']
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Thẻ đã được gửi xử lý. Kết quả sẽ được cập nhật trong vài phút.',
                    'card_deposit_id' => $cardDeposit->id,
                    'request_id' => $requestId,
                    'status' => $apiResponse['status'],
                    'response_message' => $apiResponse['message'],
                    'expected_coins' => $totalCoins,
                    'estimated_time' => '1-5 phút'
                ]);
            } else {
                $cardDeposit->markAsFailed(
                    $apiResponse['message'],
                    $apiResponse['response_data']
                );

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => $apiResponse['message'],
                    'card_deposit_id' => $cardDeposit->id,
                    'api_status' => $apiResponse['status'] ?? null
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Card deposit error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'serial' => substr($request->serial, 0, 4) . '****',
                'code' => substr($request->code, 0, 4) . '****',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý thẻ. Vui lòng thử lại sau.',
                'error_code' => 'SYSTEM_ERROR'
            ], 500);
        }
    }

    /**
     * Tạo request ID unique
     */
    private function generateUniqueRequestId()
    {
        do {
            $requestId = rand(100000000, 999999999);
        } while (CardDeposit::where('request_id', $requestId)->exists());

        return $requestId;
    }

    /**
     * Gọi API TheSieuRe bằng Guzzle HTTP Client
     */
    private function callTSRApi(CardDeposit $cardDeposit)
    {
        try {
            $url = 'https://thesieure88.com/chargingws/v2';

            $signature = md5($this->tsrPartnerKey . $cardDeposit->pin . $cardDeposit->serial);

            $multipart = [
                [
                    'name' => 'telco',
                    'contents' => $cardDeposit->type
                ],
                [
                    'name' => 'code',
                    'contents' => $cardDeposit->pin
                ],
                [
                    'name' => 'serial',
                    'contents' => $cardDeposit->serial
                ],
                [
                    'name' => 'amount',
                    'contents' => (string)$cardDeposit->amount
                ],
                [
                    'name' => 'request_id',
                    'contents' => (string)$cardDeposit->request_id
                ],
                [
                    'name' => 'partner_id',
                    'contents' => $this->tsrPartnerId
                ],
                [
                    'name' => 'sign',
                    'contents' => $signature
                ],
                [
                    'name' => 'command',
                    'contents' => 'charging'
                ]
            ];

            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'vi-VN,vi;q=0.9,en;q=0.8',
                    'Referer' => request()->url()
                ]
            ]);

            $response = $client->post($url, [
                'multipart' => $multipart,
                'timeout' => 30,
                'connect_timeout' => 10
            ]);

            $responseBody = $response->getBody()->getContents();
            $httpCode = $response->getStatusCode();
            $responseData = json_decode($responseBody, true);

            if ($responseData && isset($responseData['status'])) {
                switch ((int)$responseData['status']) {
                    case 99:
                        return [
                            'success' => true,
                            'status' => 99,
                            'message' => 'Thẻ đang được xử lý, vui lòng chờ kết quả',
                            'transaction_id' => $responseData['trans_id'] ?? null,
                            'response_data' => $responseData
                        ];

                    case 1:
                        return [
                            'success' => true,
                            'status' => 1,
                            'message' => 'Nạp thẻ thành công',
                            'transaction_id' => $responseData['trans_id'] ?? null,
                            'response_data' => $responseData
                        ];

                    case 2:
                        return [
                            'success' => true,
                            'status' => 2,
                            'message' => 'Thẻ đúng nhưng sai mệnh giá',
                            'transaction_id' => $responseData['trans_id'] ?? null,
                            'response_data' => $responseData
                        ];

                    case 3:
                        return [
                            'success' => false,
                            'status' => 3,
                            'message' => $responseData['message'] ?? 'Thẻ không hợp lệ hoặc đã được sử dụng',
                            'response_data' => $responseData
                        ];

                    case 4:
                        return [
                            'success' => false,
                            'status' => 4,
                            'message' => 'Hệ thống đang bảo trì, vui lòng thử lại sau',
                            'response_data' => $responseData
                        ];

                    default:
                        return [
                            'success' => false,
                            'message' => $responseData['message'] ?? 'Lỗi không xác định từ hệ thống xử lý thẻ',
                            'response_data' => $responseData
                        ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không nhận được phản hồi hợp lệ từ hệ thống xử lý thẻ',
                'response_data' => $responseData
            ];
        } catch (ConnectException $e) {

            return [
                'success' => false,
                'message' => 'Không thể kết nối đến hệ thống xử lý thẻ. Vui lòng thử lại sau.',
                'response_data' => null
            ];
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'unknown';
            $responseBody = $response ? $response->getBody()->getContents() : 'no response';


            return [
                'success' => false,
                'message' => 'Lỗi khi gửi yêu cầu đến hệ thống xử lý thẻ. Vui lòng thử lại sau.',
                'response_data' => null
            ];
        } catch (\Exception $e) {
            Log::error('TSR API General Error: ' . $e->getMessage(), [
                'request_id' => $cardDeposit->request_id ?? 'unknown',
                'error_type' => 'general',
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý thẻ. Vui lòng thử lại sau.',
                'response_data' => null
            ];
        }
    }

    /**
     * Webhook callback từ TheSieuRe
     */
    public function callback(Request $request)
    {
        try {
            $contentType = $request->header('content-type', '');

            if (str_contains($contentType, 'application/json')) {
                $callbackData = $request->json()->all();
                Log::info('TSR Callback received (JSON)', $callbackData);
            } else {
                $callbackData = $request->all();
                Log::info('TSR Callback received (Form)', $callbackData);
            }

            if (!isset($callbackData['callback_sign']) || !isset($callbackData['request_id'])) {
                Log::error('Callback missing required fields', $callbackData);
                return response('Missing required fields', 400);
            }

            $status = $callbackData['status'] ?? null;
            $message = $callbackData['message'] ?? null;
            $requestId = $callbackData['request_id'];
            $transId = $callbackData['trans_id'] ?? null;
            $declaredValue = $callbackData['declared_value'] ?? null;
            $value = $callbackData['value'] ?? null;
            $amount = $callbackData['amount'] ?? null;
            $code = $callbackData['code'] ?? null;
            $serial = $callbackData['serial'] ?? null;
            $telco = $callbackData['telco'] ?? null;
            $callbackSign = $callbackData['callback_sign'];

            if (!$code || !$serial) {
                Log::error('Callback missing code or serial', $callbackData);
                return response('Missing code or serial', 400);
            }

            $expectedSign = md5($this->tsrPartnerKey . $code . $serial);
            if ($callbackSign !== $expectedSign) {
                Log::error('Invalid callback signature', [
                    'expected' => $expectedSign,
                    'received' => $callbackSign,
                    'code' => substr($code, 0, 4) . '****',
                    'serial' => substr($serial, 0, 4) . '****'
                ]);
                return response('Invalid signature', 400);
            }

            $cardDeposit = CardDeposit::where('request_id', $requestId)->first();

            if (!$cardDeposit) {
                Log::error('Card deposit not found', [
                    'request_id' => $requestId,
                    'callback_data' => $callbackData
                ]);
                return response('Card deposit not found', 404);
            }

            if ($cardDeposit->status === CardDeposit::STATUS_SUCCESS) {
                Log::warning('Attempted duplicate callback processing for successful transaction', [
                    'request_id' => $requestId,
                    'current_status' => $cardDeposit->status,
                    'processed_at' => $cardDeposit->processed_at,
                    'user_id' => $cardDeposit->user_id,
                    'coins' => $cardDeposit->coins
                ]);
                return response('Transaction already completed successfully', 200);
            }

            $cardDeposit = CardDeposit::where('request_id', $requestId)
                ->lockForUpdate()
                ->first();

            if (!$cardDeposit) {
                Log::error('Card deposit not found after lock', ['request_id' => $requestId]);
                return response('Card deposit not found', 404);
            }

            if ($cardDeposit->status === CardDeposit::STATUS_SUCCESS) {
                Log::warning('Transaction completed during lock acquisition', [
                    'request_id' => $requestId,
                    'status' => $cardDeposit->status
                ]);
                return response('Transaction already completed', 200);
            }

            DB::beginTransaction();

            $callbackDataToSave = [
                'status' => $status,
                'message' => $message,
                'request_id' => $requestId,
                'trans_id' => $transId,
                'declared_value' => $declaredValue,
                'value' => $value,
                'amount' => $amount,
                'code' => $code,
                'serial' => $serial,
                'telco' => $telco,
                'callback_sign' => $callbackSign,
                'callback_time' => now()->toDateTimeString(),
                'content_type' => $contentType
            ];

            switch ((int)$status) {
                case 1:
                    $this->processSuccessfulCard($cardDeposit, $callbackDataToSave, $value ?? $amount, 'Nạp thẻ thành công', false);
                    break;

                case 2:
                    $note = "Thẻ đúng nhưng sai mệnh giá. Mệnh giá khai báo: " . number_format($declaredValue) . "đ, Mệnh giá thực: " . number_format($value) . "đ";
                    $this->processSuccessfulCard($cardDeposit, $callbackDataToSave, $value ?? $amount, $note, true); // NEW: pass true for wrong amount
                    break;

                case 3:
                    $cardDeposit->update([
                        'status' => CardDeposit::STATUS_FAILED,
                        'response_data' => $callbackDataToSave,
                        'processed_at' => now(),
                        'note' => $message ?? 'Thẻ không hợp lệ hoặc đã được sử dụng'
                    ]);
                    break;

                case 99:
                    $cardDeposit->update([
                        'status' => CardDeposit::STATUS_PROCESSING,
                        'transaction_id' => $transId,
                        'response_data' => $callbackDataToSave,
                        'note' => 'Thẻ đang được xử lý'
                    ]);
                    break;

                default:
                    $cardDeposit->update([
                        'response_data' => $callbackDataToSave,
                        'note' => $message ?? 'Trạng thái không xác định: ' . $status
                    ]);
                    break;
            }

            DB::commit();

            $logEntry = [
                'time' => date('Y-m-d H:i:s'),
                'status' => $status,
                'message' => $message,
                'request_id' => $requestId,
                'trans_id' => $transId,
                'user_id' => $cardDeposit->user_id,
                'final_status' => $cardDeposit->fresh()->status
            ];

            file_put_contents(
                storage_path('logs/tsr_callback.log'),
                json_encode($logEntry) . "\n",
                FILE_APPEND | LOCK_EX
            );

            return response('OK', 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Callback processing error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process successful card with duplicate protection
     */
    private function processSuccessfulCard($cardDeposit, $callbackData, $realAmount, $note, $isWrongAmount = false)
    {
        if ($isWrongAmount) {
            $penaltyPercent = $this->cardWrongAmountPenalty;
            $penaltyAmount = ($realAmount * $penaltyPercent) / 100;
            $amountAfterPenalty = $realAmount - $penaltyAmount;

            $feeAmount = ($amountAfterPenalty * $cardDeposit->fee_percent) / 100;
            $amountAfterFee = $amountAfterPenalty - $feeAmount;
            $baseCoins = floor($amountAfterFee / $this->coinExchangeRate);

            // Calculate bonus coins
            $bonusCoins = calculateBonusCoins($amountAfterFee, $this->bonusBaseAmount, $this->bonusBaseCam, $this->bonusDoubleAmount, $this->bonusDoubleCam);
            $totalCoins = $baseCoins + $bonusCoins;

            $note .= ". Phí phạt sai mệnh giá: " . number_format($penaltyAmount) . "đ (-{$penaltyPercent}%)";

        } else {
            $feeAmount = ($realAmount * $cardDeposit->fee_percent) / 100;
            $amountAfterFee = $realAmount - $feeAmount;
            $baseCoins = floor($amountAfterFee / $this->coinExchangeRate);

            // Calculate bonus coins
            $bonusCoins = calculateBonusCoins($amountAfterFee, $this->bonusBaseAmount, $this->bonusBaseCam, $this->bonusDoubleAmount, $this->bonusDoubleCam);
            $totalCoins = $baseCoins + $bonusCoins;
            $penaltyAmount = 0;
        }

        $updateData = [
            'status' => CardDeposit::STATUS_SUCCESS,
            'transaction_id' => $callbackData['trans_id'],
            'amount' => $realAmount,
            'coins' => $totalCoins,
            'base_coins' => $baseCoins,
            'bonus_coins' => $bonusCoins,
            'total_coins' => $totalCoins,
            'fee_amount' => $feeAmount,
            'response_data' => $callbackData,
            'processed_at' => now(),
            'note' => $note
        ];

        if ($isWrongAmount) {
            $updateData['penalty_amount'] = $penaltyAmount;
            $updateData['penalty_percent'] = $this->cardWrongAmountPenalty;
        }

        $cardDeposit->update($updateData);


        $user = User::find($cardDeposit->user_id);
        if ($user) {
            $coinService = new CoinService();
            $description = "Nạp thẻ thành công - Mệnh giá: {$realAmount} VND";
            if ($isWrongAmount) {
                $description .= " (Sai mệnh giá, phạt: {$penaltyAmount} VND)";
            }
            
            $coinService->addCoins(
                $user,
                $totalCoins,
                \App\Models\CoinHistory::TYPE_CARD_DEPOSIT,
                $description,
                $cardDeposit
            );
        } else {
            Log::error('User not found when adding coins', [
                'user_id' => $cardDeposit->user_id,
                'request_id' => $callbackData['request_id']
            ]);
        }
    }

    /**
     * Check transaction status
     */
    public function checkStatus($id)
    {
        $cardDeposit = CardDeposit::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$cardDeposit) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $cardDeposit->status,
            'status_text' => $cardDeposit->status_text,
            'coins' => $cardDeposit->coins,
            'note' => $cardDeposit->note,
            'processed_at' => $cardDeposit->processed_at?->format('d/m/Y H:i:s')
        ]);
    }

    public function checkCardForm()
    {
        return view('pages.information.deposit.check_card');
    }

    public function checkCard(Request $request)
    {
        $request->validate([
            'telco' => 'required|string',
            'code' => 'required|string',
            'serial' => 'required|string',
            'amount' => 'required|integer',
            'partner_id' => 'required|string',
            'domain' => 'required|string'
        ]);

        try {
            $partnerKey = $request->partner_key ?? $this->tsrPartnerKey;
            $signature = md5($partnerKey . $request->code . $request->serial);

            Log::info('Check Card Signature', [
                'partner_key' => $partnerKey,
                'code' => $request->code,
                'serial' => $request->serial,
                'signature' => $signature
            ]);

            $requestId = rand(100000, 999999);

            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                ]
            ]);

            $url = 'http://' . $request->domain . '/chargingws/v2';

            $multipart = [
                [
                    'name' => 'telco',
                    'contents' => $request->telco
                ],
                [
                    'name' => 'code',
                    'contents' => $request->code
                ],
                [
                    'name' => 'serial',
                    'contents' => $request->serial
                ],
                [
                    'name' => 'amount',
                    'contents' => (string)$request->amount
                ],
                [
                    'name' => 'request_id',
                    'contents' => (string)$requestId
                ],
                [
                    'name' => 'partner_id',
                    'contents' => $request->partner_id
                ],
                [
                    'name' => 'sign',
                    'contents' => $signature
                ],
                [
                    'name' => 'command',
                    'contents' => 'check'
                ]
            ];

            Log::info('Check Card Request', [
                'url' => $url,
                'partner_id' => $request->partner_id,
                'request_id' => $requestId,
                'telco' => $request->telco,
                'amount' => $request->amount,
                'code' => substr($request->code, 0, 4) . '****',
                'serial' => substr($request->serial, 0, 4) . '****',
                'sign' => $signature
            ]);

            $response = $client->post($url, [
                'multipart' => $multipart,
                'timeout' => 30,
                'connect_timeout' => 10
            ]);

            $responseBody = $response->getBody()->getContents();
            $httpCode = $response->getStatusCode();
            $responseData = json_decode($responseBody, true);

            Log::info('Check Card Response', [
                'request_id' => $requestId,
                'http_code' => $httpCode,
                'response_body' => $responseBody,
                'response_data' => $responseData
            ]);

            return response()->json([
                'success' => true,
                'request_data' => [
                    'url' => $url,
                    'telco' => $request->telco,
                    'code' => substr($request->code, 0, 4) . '****',
                    'serial' => substr($request->serial, 0, 4) . '****',
                    'amount' => $request->amount,
                    'request_id' => $requestId,
                    'partner_id' => $request->partner_id,
                    'signature' => $signature,
                    'command' => 'check'
                ],
                'response_data' => [
                    'http_code' => $httpCode,
                    'raw_response' => $responseBody,
                    'parsed_response' => $responseData,
                    'status' => $responseData['status'] ?? 'unknown',
                    'message' => $responseData['message'] ?? 'No message',
                    'trans_id' => $responseData['trans_id'] ?? null
                ]
            ]);
        } catch (ConnectException $e) {
            Log::error('Check Card Connection Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error_type' => 'connection',
                'message' => 'Không thể kết nối đến server: ' . $e->getMessage()
            ], 500);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'unknown';
            $responseBody = $response ? $response->getBody()->getContents() : 'no response';

            Log::error('Check Card Request Error: ' . $e->getMessage(), [
                'status_code' => $statusCode,
                'response_body' => $responseBody
            ]);

            return response()->json([
                'success' => false,
                'error_type' => 'request',
                'message' => 'Lỗi request: ' . $e->getMessage(),
                'status_code' => $statusCode,
                'response_body' => $responseBody
            ], 500);
        } catch (\Exception $e) {
            Log::error('Check Card General Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error_type' => 'general',
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }
}
