<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo yêu cầu nạp tiền</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0;">🔔 Thông báo yêu cầu nạp tiền mới</h1>
        <p style="margin: 5px 0 0;">{{ config('app.name') }}</p>
    </div>

    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <strong>⚠️ Có yêu cầu nạp tiền mới cần được xem xét!</strong>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <h3 style="margin-top: 0;">📋 Thông tin giao dịch</h3>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Mã giao dịch:</span>
                <span style="color: #333;"><strong>{{ $deposit->transaction_code }}</strong></span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Số tiền:</span>
                <span style="font-size: 24px; font-weight: bold; color: #28a745;">{{ number_format($deposit->amount) }} VNĐ</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Số xu nhận được:</span>
                <span style="color: #333;"><strong>{{ number_format($deposit->coins) }} xu</strong></span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Phí giao dịch:</span>
                <span style="color: #333;">{{ number_format($deposit->fee) }} VNĐ</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Ngân hàng:</span>
                <span style="color: #333;">{{ $bank->name }} ({{ $bank->code }})</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0;">
                <span style="font-weight: bold; color: #555;">Trạng thái:</span>
                <span>
                    <span style="background: #ffc107; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                        ⏳ Đang chờ duyệt
                    </span>
                </span>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <h3 style="margin-top: 0;">👤 Thông tin người dùng</h3>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Tên người dùng:</span>
                <span style="color: #333;">{{ $user->name }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">Email:</span>
                <span style="color: #333;">{{ $user->email }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee;">
                <span style="font-weight: bold; color: #555;">ID người dùng:</span>
                <span style="color: #333;">#{{ $user->id }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0;">
                <span style="font-weight: bold; color: #555;">Thời gian tạo:</span>
                <span style="color: #333;">{{ $deposit->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/admin/deposits" style="background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 20px 0;">
                🔗 Xem chi tiết trong Admin Panel
            </a>
        </div>

        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px;">
            <h4 style="margin-top: 0;">📝 Lưu ý quan trọng:</h4>
            <ul style="padding-left: 20px; margin: 0;">
                <li>Vui lòng kiểm tra ảnh chứng minh chuyển khoản</li>
                <li>Xác minh thông tin ngân hàng và số tiền</li>
                <li>Phê duyệt hoặc từ chối yêu cầu trong thời gian sớm nhất</li>
                <li>Liên hệ người dùng nếu có vấn đề</li>
            </ul>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
        <p style="margin: 0;">Email này được gửi tự động từ hệ thống {{ config('app.name') }}</p>
        <p style="margin: 5px 0 0;">{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
