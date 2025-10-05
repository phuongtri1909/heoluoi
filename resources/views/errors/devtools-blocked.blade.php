<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: white;
            color: #333;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 20px;
        }
        
        .error-icon {
            font-size: 48px;
            margin-bottom: 20px;
            color: #666;
        }
        
        .error-title {
            font-size: 32px;
            margin-bottom: 16px;
            color: #333;
            font-weight: 400;
        }
        
        .error-description {
            font-size: 16px;
            margin-bottom: 8px;
            color: #666;
        }
        
        .error-code {
            font-size: 14px;
            margin-bottom: 20px;
            color: #999;
        }
        
        .error-link {
            color: #666;
            text-decoration: underline;
            font-size: 14px;
            cursor: pointer;
        }
        
        .error-link:hover {
            color: #333;
        }
        
        .reload-button {
            position: absolute;
            bottom: 40px;
            right: 40px;
            background: #6B7C32;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.2s;
        }
        
        .reload-button:hover {
            background: #5A6B2A;
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 40px 20px;
            }
            
            .error-title {
                font-size: 28px;
            }
            
            .reload-button {
                position: relative;
                bottom: auto;
                right: auto;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">📄😞</div>
        <h1 class="error-title">Ôi, hỏng!</h1>
        <p class="error-description">Đã xảy ra lỗi khi hiển thị trang web này.</p>
        <p class="error-code">Mã lỗi: STATUS_ACCESS_VIOLATION</p>
        <a href="#" onclick="location.reload()" class="error-link">Tìm hiểu thêm</a>
    </div>
    
    <button onclick="location.reload()" class="reload-button">Tải lại</button>
    
    <script>
        // Auto redirect về trang chủ sau 5 giây
        setTimeout(function() {
            window.location.replace("{{ route('home') }}");
        }, 5000);
        
        // Chặn HOÀN TOÀN tất cả phím tắt DevTools và Copy
        document.addEventListener("keydown", function(e) {
            // F12
            if (e.keyCode === 123) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+Shift+I (DevTools)
            if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+Shift+J (Console)
            if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+Shift+C (Element Inspector)
            if (e.ctrlKey && e.shiftKey && e.keyCode === 67) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+U (View Source)
            if (e.ctrlKey && e.keyCode === 85) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+A (Select All) - CHẶN COPY
            if (e.ctrlKey && e.keyCode === 65) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+C (Copy) - CHẶN COPY
            if (e.ctrlKey && e.keyCode === 67) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+X (Cut) - CHẶN CUT
            if (e.ctrlKey && e.keyCode === 88) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Ctrl+V (Paste) - CHẶN PASTE
            if (e.ctrlKey && e.keyCode === 86) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }, true); // Use capture phase
        
        // Chặn chuột phải HOÀN TOÀN
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
        
        // Chặn select text HOÀN TOÀN
        document.addEventListener("selectstart", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
        
        // Chặn copy event - CHỐNG COPY
        document.addEventListener("copy", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
        
        // Chặn cut event - CHỐNG CUT
        document.addEventListener("cut", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
        
        // Chặn paste event - CHỐNG PASTE
        document.addEventListener("paste", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
        
        // Chặn drag và drop - CHỐNG DRAG
        document.addEventListener("dragstart", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }, true);
        
        // Disable all forms of text selection
        document.body.style.userSelect = "none";
        document.body.style.webkitUserSelect = "none";
        document.body.style.mozUserSelect = "none";
        document.body.style.msUserSelect = "none";
        
        // Disable drag and drop
        document.body.style.webkitUserDrag = "none";
        document.body.style.userDrag = "none";
        
        // Disable image dragging
        const images = document.querySelectorAll("img");
        images.forEach(img => {
            img.draggable = false;
            img.ondragstart = function() { return false; };
        });
        
        // Disable link dragging
        const links = document.querySelectorAll("a");
        links.forEach(link => {
            link.draggable = false;
            link.ondragstart = function() { return false; };
        });
    </script>
</body>
</html>
