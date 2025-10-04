# Casso Webhook v2 Integration Guide

## Tổng quan

Hệ thống đã được cập nhật để sử dụng **Casso Webhook v2** theo tài liệu chính thức từ:
- [developer.casso.vn](https://developer.casso.vn/webhook/thiet-lap-webhook-thu-cong) - Hướng dẫn thiết lập webhook
- [CassoHQ/casso-webhook-v2-verify-signature](https://github.com/CassoHQ/casso-webhook-v2-verify-signature/blob/main/php.php) - Xác thực chữ ký số
- [CassoHQ/casso-webhook-handler-sample](https://github.com/CassoHQ/casso-webhook-handler-sample/blob/main/webhook_handler.php) - Sample webhook handler

Casso sử dụng webhook để thông báo khi có giao dịch mới thay vì tạo payment URL trực tiếp.

## Cấu hình

### 1. Environment Variables

```env
# Casso Configuration
CASSO_API_KEY=your_casso_api_key_here
CASSO_WEBHOOK_SECRET=your_webhook_secret_from_casso_dashboard
CASSO_API_URL=https://api.casso.vn
```

### 2. Config Services

```php
// config/services.php
'casso' => [
    'api_key' => env('CASSO_API_KEY'),
    'webhook_secret' => env('CASSO_WEBHOOK_SECRET'),
    'api_url' => env('CASSO_API_URL', 'https://api.casso.vn'),
],
```

## Casso Webhook v2 Integration

### 1. Tạo Payment URL (Mock)

```php
private function createCassoPaymentUrl($amount, $transactionCode, $bankId)
{
    // Casso không có API tạo payment URL trực tiếp
    // Thay vào đó, Casso sử dụng webhook để thông báo khi có giao dịch
    // Đây là mock URL để demo
    
    $mockPaymentUrl = 'https://sandbox.casso.vn/payment/' . $transactionCode;
    
    Log::info('Casso mock payment URL created:', [
        'payment_url' => $mockPaymentUrl,
        'transaction_code' => $transactionCode,
        'amount' => $amount,
        'note' => 'Casso sử dụng webhook để xử lý giao dịch, không có payment URL trực tiếp'
    ]);
    
    return $mockPaymentUrl;
}
```

### 2. Webhook Processing

```php
public function callback(Request $request)
{
    // Verify signature từ Casso Webhook v2
    $signature = $request->header('X-Casso-Signature');
    $payload = $request->getContent();
    
    if (!$this->verifyCassoSignature($payload, $signature)) {
        return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
    }
    
    $data = $request->all();
    
    // Casso Webhook v2 format
    $transactionId = $data['data']['id'] ?? null;
    $reference = $data['data']['reference'] ?? null;
    $description = $data['data']['description'] ?? '';
    $amount = $data['data']['amount'] ?? 0;
    $accountNumber = $data['data']['accountNumber'] ?? '';
    
    // Kiểm tra xem giao dịch đã được xử lý chưa (chống trùng lặp)
    $existingDeposit = BankAutoDeposit::where('casso_transaction_id', $transactionId)
        ->where('status', BankAutoDeposit::STATUS_SUCCESS)
        ->first();
        
    if ($existingDeposit) {
        return response()->json(['success' => true, 'message' => 'Transaction already processed']);
    }
    
    // Xử lý giao dịch...
}
```

### 3. Signature Verification

Theo tài liệu chính thức từ [CassoHQ/casso-webhook-v2-verify-signature](https://github.com/CassoHQ/casso-webhook-v2-verify-signature/blob/main/php.php):

```php
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
```

**Lưu ý quan trọng:**
- Sử dụng raw payload content để verify signature
- Signature header: `X-Casso-Signature`
- Algorithm: HMAC-SHA256
- Format: `sha256=calculated_signature`

## Webhook Format

### Headers
```
X-Casso-Signature: sha256=calculated_signature
Content-Type: application/json
```

### Payload (Casso Webhook v2)
```json
{
    "error": 0,
    "data": {
        "id": 12345,
        "reference": "BA1234567890ABC",
        "description": "Nạp cám tự động - BA1234567890ABC",
        "amount": 100000,
        "runningBalance": 25000000,
        "transactionDateTime": "2025-01-01T10:00:00",
        "accountNumber": "1234567890",
        "bankName": "Vietcombank",
        "bankAbbreviation": "VCB",
        "virtualAccountNumber": "",
        "virtualAccountName": "",
        "counterAccountName": "Nguyen Van A",
        "counterAccountNumber": "9876543210",
        "counterAccountBankId": "VCB",
        "counterAccountBankName": "Vietcombank"
    }
}
```

## Luồng hoạt động

### 1. User tạo giao dịch
```
User chọn ngân hàng và nhập số tiền
↓
POST /user/bank-auto-deposit
↓
Tạo BankAutoDeposit record với status = 'pending'
↓
Trả về thông tin chuyển khoản cho user
↓
Hiển thị thông tin ngân hàng và transaction code
```

### 2. User thanh toán
```
User thấy thông tin chuyển khoản:
- Ngân hàng: [Tên ngân hàng]
- Số tài khoản: [Số tài khoản Casso]
- Tên chủ tài khoản: [Tên chủ tài khoản]
- Số tiền: [Số tiền đã nhập]
- Nội dung: [Transaction code]
↓
User chuyển khoản với nội dung chính xác
```

### 3. Casso phát hiện giao dịch
```
Casso tự động phát hiện giao dịch chuyển khoản
↓
Casso verify:
- Số tiền đúng ✓
- Nội dung chuyển khoản đúng ✓
- Tài khoản đích đúng ✓
↓
Casso gửi webhook đến hệ thống
```

### 4. Xử lý webhook
```
Casso POST /bank-auto-deposit/callback
↓
Verify signature với webhook secret
↓
Kiểm tra transaction_id chưa được xử lý (chống trùng lặp)
↓
Tìm BankAutoDeposit bằng reference (transaction_code)
↓
Cập nhật status = 'success' và casso_transaction_id
↓
Cộng coins cho user
↓
Trả về HTTP 200 OK
```

## Database Schema

### BankAutoDeposits Table
```sql
CREATE TABLE bank_auto_deposits (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    bank_id BIGINT NOT NULL,
    transaction_code VARCHAR(255) NOT NULL,
    casso_transaction_id VARCHAR(255) NULL, -- ID từ Casso webhook
    amount DECIMAL(10,2) NOT NULL,
    base_coins INT NOT NULL,
    bonus_coins INT NOT NULL,
    total_coins INT NOT NULL,
    fee_amount INT NOT NULL,
    status ENUM('pending', 'success', 'failed', 'cancelled') DEFAULT 'pending',
    note TEXT NULL,
    processed_at TIMESTAMP NULL,
    casso_response JSON NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Cấu hình Casso Dashboard

### 1. Thiết lập Webhook trong Casso
1. Đăng nhập vào [casso.vn](https://casso.vn)
2. Truy cập **Kết nối** > **Tích hợp**
3. Chọn **Webhook V2**
4. Cấu hình:
   - **Webhook URL**: `https://yourdomain.com/bank-auto-deposit/callback`
   - **Key bảo mật**: Lưu lại để cấu hình `CASSO_WEBHOOK_SECRET`
   - **Chọn ngân hàng**: Chọn tài khoản ngân hàng để theo dõi
   - **Cấu hình dữ liệu**: Bật các tùy chọn cần thiết

### 2. Test Webhook
1. Click **Gọi thử** để test webhook
2. Kiểm tra log để đảm bảo webhook hoạt động
3. Click **Lưu** để lưu cấu hình

## Ưu điểm của Casso Webhook v2

### 1. **Tự động hoàn toàn**
- Không cần admin duyệt
- Casso tự động phát hiện giao dịch
- Xử lý ngay lập tức

### 2. **Bảo mật cao**
- Signature verification với HMAC-SHA256
- Webhook secret key
- Chống replay attack

### 3. **Chống trùng lặp**
- Sử dụng `casso_transaction_id` để kiểm tra
- Mỗi giao dịch chỉ được xử lý một lần
- Tránh double spending

### 4. **Thông tin chi tiết**
- Đầy đủ thông tin giao dịch
- Thông tin tài khoản đối ứng
- Thời gian giao dịch chính xác

## Error Handling và Logging

### 1. Comprehensive Logging
Theo tài liệu từ [CassoHQ/casso-webhook-handler-sample](https://github.com/CassoHQ/casso-webhook-handler-sample/blob/main/webhook_handler.php):

```php
// Log webhook received
Log::info('Casso webhook received', [
    'signature' => $signature,
    'payload_length' => strlen($payload),
    'headers' => $request->headers->all()
]);

// Log parsed data
Log::info('Casso webhook data parsed', [
    'transaction_id' => $transactionId,
    'reference' => $reference,
    'amount' => $amount,
    'account_number' => $accountNumber,
    'bank_name' => $bankName
]);

// Log successful processing
Log::info('Bank auto deposit successful', [
    'user_id' => $user->id,
    'transaction_code' => $reference,
    'casso_transaction_id' => $transactionId,
    'coins_added' => $deposit->total_coins,
    'amount_received' => $amount,
    'bank_name' => $bankName
]);
```

### 2. Error Handling
```php
// JSON parsing error
if (json_last_error() !== JSON_ERROR_NONE) {
    Log::error('Invalid JSON payload', ['error' => json_last_error_msg()]);
    return response()->json(['success' => false, 'message' => 'Invalid JSON payload'], 400);
}

// Signature verification error
if (!$this->verifyCassoSignature($payload, $signature)) {
    Log::warning('Invalid Casso signature', [
        'signature' => $signature,
        'payload_preview' => substr($payload, 0, 100)
    ]);
    return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
}

// Exception handling
catch (\Exception $e) {
    DB::rollBack();
    Log::error('Error processing bank auto callback: ' . $e->getMessage(), [
        'transaction_id' => $transactionId,
        'reference' => $reference,
        'data' => $data,
        'trace' => $e->getTraceAsString()
    ]);
    return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra']);
}
```

## Testing

### 1. Test Webhook với Postman
```bash
curl -X POST https://yourdomain.com/bank-auto-deposit/callback \
  -H "X-Casso-Signature: sha256=calculated_signature" \
  -H "Content-Type: application/json" \
  -d '{
    "error": 0,
    "data": {
      "id": 12345,
      "reference": "BA1234567890ABC",
      "description": "Nạp cám tự động - BA1234567890ABC",
      "amount": 100000,
      "accountNumber": "1234567890",
      "bankName": "Vietcombank"
    }
  }'
```

### 2. Debug Commands
```bash
# Xem log webhook
tail -f storage/logs/laravel.log | grep "Casso"

# Test webhook endpoint
curl -X POST https://yourdomain.com/bank-auto-deposit/callback \
  -H "X-Casso-Signature: test" \
  -H "Content-Type: application/json" \
  -d '{"data":{"id":12345,"reference":"test","amount":100000}}'
```

## Troubleshooting

### Common Issues

1. **Invalid signature**
   - Kiểm tra `CASSO_WEBHOOK_SECRET` trong `.env`
   - Verify algorithm là HMAC-SHA256
   - Đảm bảo sử dụng raw payload content

2. **Webhook not received**
   - Kiểm tra webhook URL có accessible không
   - Verify SSL certificate
   - Check firewall settings

3. **Transaction not found**
   - Kiểm tra `reference` có đúng format không
   - Verify transaction chưa được xử lý
   - Check database connection

4. **Duplicate processing**
   - Kiểm tra `casso_transaction_id` đã được lưu chưa
   - Verify logic chống trùng lặp
   - Check database constraints

### Debug Commands
```bash
# Xem log webhook
tail -f storage/logs/laravel.log | grep "Casso"

# Test webhook endpoint
curl -X POST https://yourdomain.com/bank-auto-deposit/callback \
  -H "X-Casso-Signature: test" \
  -H "Content-Type: application/json" \
  -d '{"data":{"id":12345,"reference":"test","amount":100000}}'
```

## Kết luận

Casso Webhook v2 cung cấp một giải pháp thanh toán tự động hoàn chỉnh và bảo mật cao. Với webhook, hệ thống có thể xử lý giao dịch ngay lập tức mà không cần can thiệp thủ công, đảm bảo trải nghiệm người dùng tốt nhất.
