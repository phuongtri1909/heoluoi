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
        width: 3px;
        height: 80px;
        background: linear-gradient(to bottom, #ff6b6b, #ee5a6f);
        margin-bottom: 5px;
        border-radius: 2px;
    }
    .arrow-head {
        width: 0;
        height: 0;
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 20px solid #ff6b6b;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }
    .instruction-text {
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        margin-bottom: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
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

