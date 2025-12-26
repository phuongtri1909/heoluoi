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
        align-items: flex-end;
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
        margin-right: 10px;
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
        <div class="loading-icon">
            <i class="fab fa-google"></i>
        </div>
        <h2 class="mb-3">Cần mở bằng Safari</h2>
        <p class="redirect-message">
            Google không cho phép đăng nhập từ trình duyệt trong ứng dụng (Messenger, Facebook).
            <br><br>
            <strong>Hướng dẫn:</strong> Nhấn vào nút <strong>"..."</strong> ở góc dưới bên phải màn hình, sau đó chọn <strong>"Mở trong Safari"</strong> hoặc <strong>"Mở trong trình duyệt"</strong>.
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

<script>
const googleOAuthUrl = '{{ $googleOAuthUrl }}';

function copyLink() {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(googleOAuthUrl).then(function() {
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
    textArea.value = googleOAuthUrl;
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
        alert('Không thể sao chép. Vui lòng ghi nhớ liên kết: ' + googleOAuthUrl);
    }
    document.body.removeChild(textArea);
}

function openInSafari() {
    // Copy link trước (link Google OAuth trực tiếp)
    copyLink();
    
    // Thử mở bằng window.open (sẽ không hoạt động trong in-app browser)
    try {
        const newWindow = window.open(googleOAuthUrl, '_blank', 'noopener,noreferrer');
        if (newWindow && !newWindow.closed) {
            newWindow.focus();
            return;
        }
    } catch(e) {
        console.log('Cannot open popup');
    }
    
    // Hiển thị hướng dẫn
    alert('✅ Đã sao chép liên kết Google OAuth!\n\n📱 Hướng dẫn:\n1. Nhấn nút Home để thoát khỏi ứng dụng này\n2. Mở Safari\n3. Nhấn vào thanh địa chỉ\n4. Nhấn giữ và chọn "Dán"\n5. Nhấn Enter để đăng nhập Google');
}
</script>
@endsection

