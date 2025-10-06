<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDevTools
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.debug')) {
            $response = $next($request);
            $script = $this->getBlockScript();
            $content = $response->getContent();
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
            
            document.addEventListener("keydown", function(e) {
                if (e.key === "F12" || e.keyCode === 123) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && e.shiftKey && (e.key === "I" || e.keyCode === 73)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && e.shiftKey && (e.key === "C" || e.keyCode === 67)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && e.shiftKey && (e.key === "J" || e.keyCode === 74)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && (e.key === "u" || e.keyCode === 85)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && (e.key === "a" || e.keyCode === 65)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && (e.key === "c" || e.keyCode === 67)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && (e.key === "x" || e.keyCode === 88)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                
                if (e.ctrlKey && (e.key === "v" || e.keyCode === 86)) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
            }, true);
            
            document.addEventListener("contextmenu", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            document.addEventListener("copy", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            document.addEventListener("cut", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            document.addEventListener("paste", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            document.addEventListener("selectstart", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            document.addEventListener("dragstart", function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            document.body.style.userSelect = "none";
            document.body.style.webkitUserSelect = "none";
            document.body.style.mozUserSelect = "none";
            document.body.style.msUserSelect = "none";
            document.body.style.webkitUserDrag = "none";
            document.body.style.userDrag = "none";
            
            const images = document.querySelectorAll("img");
            images.forEach(img => {
                img.draggable = false;
                img.ondragstart = function() { return false; };
            });
            
            const links = document.querySelectorAll("a");
            links.forEach(link => {
                link.draggable = false;
                link.ondragstart = function() { return false; };
            });
            
        })();
        </script>';
    }
}
