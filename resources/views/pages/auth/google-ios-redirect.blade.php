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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .redirect-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        text-align: center;
    }
    .loading-icon {
        font-size: 48px;
        color: #4285F4;
        margin-bottom: 20px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .redirect-message {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content-main')
<div class="redirect-container">
    <div class="redirect-card">
        <div class="loading-icon">
            <i class="fab fa-google"></i>
        </div>
        <h2 class="mb-3">Đang mở Safari...</h2>
        <p class="redirect-message">
            Vui lòng đợi trong giây lát. Hệ thống đang tự động mở Safari để đăng nhập Google.
        </p>
    </div>
</div>

<script>
(function() {
    const googleLoginUrl = '{{ $googleLoginUrl }}';
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    if (isIOS) {
        // Cách 4: Thử dùng popup
        try {
            const popup = window.open(googleLoginUrl, '_blank', 'noopener,noreferrer');
            if (popup && !popup.closed) {
                // Nếu popup mở được, focus vào nó
                popup.focus();
                return;
            }
        } catch(e) {
            console.log('Popup blocked');
        }
        
        // Fallback: Thử redirect trực tiếp
        setTimeout(function() {
            window.location.href = googleLoginUrl;
        }, 500);
    } else {
        // Android hoặc desktop: redirect trực tiếp
        window.location.href = googleLoginUrl;
    }
})();
</script>
@endsection

