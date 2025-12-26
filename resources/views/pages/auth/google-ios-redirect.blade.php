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
        <h2 class="mb-3">Cần mở bằng Safari</h2>
        <p class="redirect-message">
            Google không cho phép đăng nhập từ trình duyệt trong ứng dụng (Messenger, Facebook).
            <br><br>
            Vui lòng mở liên kết bằng Safari để tiếp tục.
        </p>
        
        <div style="margin-top: 30px;">
            <a href="{{ $googleLoginUrl }}" 
               class="btn btn-primary btn-lg" 
               style="background: #4285F4; color: white; padding: 15px 30px; border-radius: 10px; text-decoration: none; display: inline-block; margin-bottom: 15px;"
               id="openLink">
                <i class="fab fa-safari me-2"></i>
                Mở bằng Safari
            </a>
            <br>
            <button type="button" 
                    class="btn btn-outline-secondary" 
                    onclick="copyLink()"
                    style="padding: 10px 20px; border-radius: 10px;">
                <i class="fas fa-copy me-2"></i>
                Sao chép liên kết
            </button>
            <div id="copySuccess" style="display: none; color: #4caf50; margin-top: 10px;">
                <i class="fas fa-check-circle"></i> Đã sao chép!
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    const url = '{{ $googleLoginUrl }}';
    navigator.clipboard.writeText(url).then(function() {
        document.getElementById('copySuccess').style.display = 'block';
        setTimeout(function() {
            document.getElementById('copySuccess').style.display = 'none';
        }, 3000);
    }).catch(function(err) {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        document.getElementById('copySuccess').style.display = 'block';
        setTimeout(function() {
            document.getElementById('copySuccess').style.display = 'none';
        }, 3000);
    });
}

// Thử tự động redirect sau 2 giây
setTimeout(function() {
    const googleLoginUrl = '{{ $googleLoginUrl }}';
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    if (!isIOS) {
        // Android hoặc desktop: redirect trực tiếp
        window.location.href = googleLoginUrl;
    }
    // iOS: để user click nút hoặc copy link
}, 2000);
</script>
@endsection

