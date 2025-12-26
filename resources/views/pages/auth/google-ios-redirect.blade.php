@extends('layouts.main')
@section('title', 'Đăng nhập Google')

@push('styles-main')
<style>
    .redirect-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .redirect-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        text-align: center;
    }
    .google-logo {
        width: 60px;
        height: 60px;
        margin-bottom: 20px;
    }
    .redirect-message {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .instruction-arrow {
        position: fixed;
        bottom: 60px;
        right: 20px;
        z-index: 9999;
        animation: bounce 2s infinite;
    }
    .arrow-container {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .arrow-line {
        width: 5px;
        height: 90px;
        background: linear-gradient(to bottom, #ff4757, #ff6b6b, #ff8c94);
        margin-bottom: 0;
        border-radius: 3px;
        box-shadow: 0 0 10px rgba(255, 71, 87, 0.5),
                    0 0 20px rgba(255, 71, 87, 0.3),
                    inset 0 0 10px rgba(255, 255, 255, 0.2);
        position: relative;
        animation: pulse-line 1.5s ease-in-out infinite;
    }
    .arrow-line::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(255, 71, 87, 0.8);
        animation: pulse-dot 1.5s ease-in-out infinite;
    }
    .arrow-head {
        width: 0;
        height: 0;
        border-left: 18px solid transparent;
        border-right: 18px solid transparent;
        border-top: 25px solid #ff4757;
        filter: drop-shadow(0 4px 8px rgba(255, 71, 87, 0.6));
        position: relative;
        animation: pulse-arrow 1.5s ease-in-out infinite;
    }
    .arrow-head::after {
        content: '';
        position: absolute;
        top: -25px;
        left: -12px;
        width: 0;
        height: 0;
        border-left: 12px solid transparent;
        border-right: 12px solid transparent;
        border-top: 18px solid #fff;
        opacity: 0.3;
    }
    .instruction-text {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        white-space: nowrap;
        margin-bottom: 8px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4),
                    0 0 15px rgba(255, 71, 87, 0.3);
        position: relative;
        border: 2px solid rgba(255, 255, 255, 0.2);
        animation: pulse-text 2s ease-in-out infinite;
    }
    .instruction-text::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid #2c3e50;
    }
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-12px);
        }
    }
    @keyframes pulse-line {
        0%, 100% {
            box-shadow: 0 0 10px rgba(255, 71, 87, 0.5),
                        0 0 20px rgba(255, 71, 87, 0.3),
                        inset 0 0 10px rgba(255, 255, 255, 0.2);
        }
        50% {
            box-shadow: 0 0 15px rgba(255, 71, 87, 0.8),
                        0 0 30px rgba(255, 71, 87, 0.5),
                        inset 0 0 15px rgba(255, 255, 255, 0.3);
        }
    }
    @keyframes pulse-dot {
        0%, 100% {
            opacity: 1;
            transform: translateX(-50%) scale(1);
        }
        50% {
            opacity: 0.7;
            transform: translateX(-50%) scale(1.2);
        }
    }
    @keyframes pulse-arrow {
        0%, 100% {
            filter: drop-shadow(0 4px 8px rgba(255, 71, 87, 0.6));
        }
        50% {
            filter: drop-shadow(0 6px 12px rgba(255, 71, 87, 0.9));
        }
    }
    @keyframes pulse-text {
        0%, 100% {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4),
                        0 0 15px rgba(255, 71, 87, 0.3);
        }
        50% {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5),
                        0 0 20px rgba(255, 71, 87, 0.5);
        }
    }
    @media (max-width: 768px) {
        .instruction-arrow {
            bottom: 50px;
            right: 15px;
        }
        .instruction-text {
            font-size: 12px;
            padding: 8px 12px;
        }
        .arrow-line {
            height: 60px;
        }
    }
</style>
@endpush

@section('content-main')
<div class="redirect-container">
    <div class="redirect-card">
        <div>
            <img src="{{ asset('images/svg/google_2025.svg') }}" alt="Google" class="google-logo">
        </div>
        <h2 class="mb-3">Cần mở bằng Safari</h2>
        <p class="redirect-message">
            Google không cho phép đăng nhập từ trình duyệt trong ứng dụng (Messenger, Facebook).
            <br><br>
            <strong>Hướng dẫn:</strong> Nhấn vào nút <strong>"..."</strong> ở góc dưới bên phải màn hình, sau đó chọn <strong>"Mở bằng trình duyệt bên ngoài"</strong>.
        </p>
    </div>
</div>

<!-- Mũi tên chỉ vào nút "..." ở góc dưới bên phải -->
<div class="instruction-arrow">
    <div class="arrow-container">
        <div class="instruction-text">
            👆 Nhấn vào đây
        </div>
        <div class="arrow-line"></div>
        <div class="arrow-head"></div>
    </div>
</div>
@endsection

