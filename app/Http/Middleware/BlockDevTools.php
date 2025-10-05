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
            // Chặn F12 - KHÔNG CHO NHẤN
            document.addEventListener("keydown", function(e) {
                if (e.keyCode === 123) { // F12
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
            }, true);
            
            // Chặn chuột phải - KHÔNG CHO NHẤN
            document.addEventListener("contextmenu", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            // Chặn các phím tắt DevTools - REDIRECT
            document.addEventListener("keydown", function(e) {
                // Ctrl+Shift+I (DevTools)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+Shift+J (Console)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+Shift+C (Element Inspector)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 67) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+U (View Source)
                if (e.ctrlKey && e.keyCode === 85) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+A (Select All) - CHẶN COPY
                if (e.ctrlKey && e.keyCode === 65) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+C (Copy) - CHẶN COPY
                if (e.ctrlKey && e.keyCode === 67) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+X (Cut) - CHẶN CUT
                if (e.ctrlKey && e.keyCode === 88) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
                
                // Ctrl+V (Paste) - CHẶN PASTE
                if (e.ctrlKey && e.keyCode === 86) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.location.replace("/devtools-blocked");
                    return false;
                }
            }, true);
            
            // Chặn copy event - CHỐNG COPY
            document.addEventListener("copy", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                window.location.replace("/devtools-blocked");
                return false;
            }, true);
            
            // Chặn cut event - CHỐNG CUT
            document.addEventListener("cut", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                window.location.replace("/devtools-blocked");
                return false;
            }, true);
            
            // Chặn paste event - CHỐNG PASTE
            document.addEventListener("paste", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                window.location.replace("/devtools-blocked");
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
            
            // Detect DevTools mở - CHỈ CHECK TAB HIỆN TẠI
            let devtools = {
                open: false,
                checkCount: 0
            };
            
            const threshold = 200;
            
            setInterval(function() {
                devtools.checkCount++;
                
                // Chỉ check sau khi trang đã load ổn định
                if (devtools.checkCount < 3) {
                    return;
                }
                
                const heightDiff = window.outerHeight - window.innerHeight;
                const widthDiff = window.outerWidth - window.innerWidth;
                
                // Chỉ redirect nếu DevTools thực sự mở trong tab này
                if (heightDiff > threshold || widthDiff > threshold) {
                    if (!devtools.open) {
                        devtools.open = true;
                        window.location.replace("/devtools-blocked");
                    }
                } else {
                    devtools.open = false;
                }
            }, 1000); // Check nhanh hơn để detect chính xác
            
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
