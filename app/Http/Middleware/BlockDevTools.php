<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDevTools
{
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ chặn khi APP_DEBUG = false
        if (!config('app.debug')) {
            $response = $next($request);
            
            // Thêm JavaScript để chặn DevTools
            $script = $this->getBlockScript();
            $content = $response->getContent();
            
            // Chèn script trước thẻ </body>
            $content = str_replace('</body>', $script . '</body>', $content);
            
            $response->setContent($content);
            return $response;
        }
        
        return $next($request);
    }
    
    private function getBlockScript(): string
    {
        return '
        <script>
        (function() {
            "use strict";
            
            let devtoolsOpen = false;
            const threshold = 160;
            
            // Redirect ngay lập tức nếu detect DevTools
            if (window.outerHeight - window.innerHeight > threshold || 
                window.outerWidth - window.innerWidth > threshold ||
                window.opener || 
                window.parent !== window || 
                document.referrer === "" ||
                window.location.href.includes("about:blank") ||
                window.location.href.includes("data:text/html") ||
                window.location.href.includes("javascript:") ||
                window.location.href.includes("blob:")) {
                handleDevTools();
            }
            
            // Kiểm tra kích thước cửa sổ
            // function checkWindowSize() {
            //     if (window.outerWidth - window.innerWidth > threshold || 
            //         window.outerHeight - window.innerHeight > threshold) {
            //         if (!devtoolsOpen) {
            //             devtoolsOpen = true;
            //             handleDevTools();
            //         }
            //     } else {
            //         devtoolsOpen = false;
            //     }
            // }
            
            // Kiểm tra bằng console
            function checkConsole() {
                const element = new Image();
                Object.defineProperty(element, "id", {
                    get: function() {
                        devtoolsOpen = true;
                        handleDevTools();
                    }
                });
                console.log(element);
            }
            
            // Kiểm tra bằng debugger
            function checkDebugger() {
                const start = new Date();
                debugger; // Sẽ tạm dừng nếu DevTools mở
                const end = new Date();
                
                if (end - start > 100) {
                    devtoolsOpen = true;
                    handleDevTools();
                }
            }
            
            // Xử lý khi phát hiện DevTools
            function handleDevTools() {
                // Redirect ngay lập tức về about:blank
                window.location.replace("about:blank");
            }
            
            // Chạy kiểm tra
            // setInterval(checkWindowSize, 500);
            setInterval(checkConsole, 1000);
            setInterval(checkDebugger, 2000);
            
            // Chặn phím tắt
            document.addEventListener("keydown", function(e) {
                // F12
                if (e.key === "F12" || e.keyCode === 123) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                // Ctrl+Shift+I
                if (e.ctrlKey && e.shiftKey && (e.key === "I" || e.keyCode === 73)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+Shift+C
                if (e.ctrlKey && e.shiftKey && (e.key === "C" || e.keyCode === 67)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+Shift+J (Console)
                if (e.ctrlKey && e.shiftKey && (e.key === "J" || e.keyCode === 74)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+U (View Source)
                if (e.ctrlKey && (e.key === "u" || e.keyCode === 85)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+A (Select All) - CHẶN COPY
                if (e.ctrlKey && (e.key === "a" || e.keyCode === 65)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+C (Copy) - CHẶN COPY
                if (e.ctrlKey && (e.key === "c" || e.keyCode === 67)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+X (Cut) - CHẶN CUT
                if (e.ctrlKey && (e.key === "x" || e.keyCode === 88)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
                
                // Ctrl+V (Paste) - CHẶN PASTE
                if (e.ctrlKey && (e.key === "v" || e.keyCode === 86)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    handleDevTools();
                    return false;
                }
            }, true);
            
            // Chặn chuột phải
            document.addEventListener("contextmenu", function(e) {
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
                handleDevTools();
                return false;
            }, true);
            
            // Chặn cut event - CHỐNG CUT
            document.addEventListener("cut", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                handleDevTools();
                return false;
            }, true);
            
            // Chặn paste event - CHỐNG PASTE VÀ COPY LINK VÀO DEVTOOLS
            document.addEventListener("paste", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Detect nếu paste URL của website hiện tại
                if (e.clipboardData && e.clipboardData.getData) {
                    const pastedData = e.clipboardData.getData("text/plain");
                    if (pastedData) {
                        const currentUrl = window.location.href;
                        const currentDomain = window.location.hostname;
                        
                        // Nếu paste URL của website này thì redirect ngay
                        if (pastedData.includes(currentDomain) || 
                            pastedData.includes(currentUrl) ||
                            pastedData.includes(window.location.origin)) {
                            handleDevTools();
                            return false;
                        }
                    }
                }
                
                // Mọi paste khác cũng redirect
                handleDevTools();
                return false;
            }, true);
            
            // Chặn select text - CHỐNG SELECT
            document.addEventListener("selectstart", function(e) {
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
            
        })();
        </script>';
    }
}
