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
            <button type="button" 
                    class="btn btn-primary btn-lg" 
                    onclick="openInSafari()"
                    style="background: #4285F4; color: white; padding: 15px 30px; border-radius: 10px; border: none; margin-bottom: 15px; width: 100%;">
                <i class="fab fa-safari me-2"></i>
                Mở bằng Safari
            </button>
            <br>
            <button type="button" 
                    class="btn btn-outline-secondary" 
                    onclick="copyLink()"
                    style="padding: 10px 20px; border-radius: 10px; width: 100%;">
                <i class="fas fa-copy me-2"></i>
                Sao chép liên kết
            </button>
            <div id="copySuccess" style="display: none; color: #4caf50; margin-top: 10px;">
                <i class="fas fa-check-circle"></i> Đã sao chép! Mở Safari và dán vào thanh địa chỉ.
            </div>
        </div>
    </div>
</div>

<script>
const googleLoginUrl = '{{ $googleLoginUrl }}';

function copyLink() {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(googleLoginUrl).then(function() {
            document.getElementById('copySuccess').style.display = 'block';
            setTimeout(function() {
                document.getElementById('copySuccess').style.display = 'none';
            }, 5000);
        }).catch(function(err) {
            fallbackCopy();
        });
    } else {
        fallbackCopy();
    }
}

function fallbackCopy() {
    const textArea = document.createElement('textarea');
    textArea.value = googleLoginUrl;
    textArea.style.position = 'fixed';
    textArea.style.opacity = '0';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        document.getElementById('copySuccess').style.display = 'block';
        setTimeout(function() {
            document.getElementById('copySuccess').style.display = 'none';
        }, 5000);
    } catch(err) {
        alert('Không thể sao chép. Vui lòng ghi nhớ liên kết: ' + googleLoginUrl);
    }
    document.body.removeChild(textArea);
}

function openInSafari() {
    // Copy link trước
    copyLink();
    
    // Thử mở bằng window.open
    try {
        const newWindow = window.open(googleLoginUrl, '_blank', 'noopener,noreferrer');
        if (newWindow && !newWindow.closed) {
            newWindow.focus();
            return;
        }
    } catch(e) {
        console.log('Cannot open popup');
    }
    
    // Nếu không mở được, hiển thị hướng dẫn
    alert('Đã sao chép liên kết!\n\n📱 Hướng dẫn:\n1. Nhấn nút Home để thoát\n2. Mở Safari\n3. Nhấn vào thanh địa chỉ\n4. Nhấn giữ và chọn "Dán"\n5. Nhấn Enter để đăng nhập');
}
</script>
@endsection

