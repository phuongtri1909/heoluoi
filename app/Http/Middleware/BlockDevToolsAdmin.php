<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDevToolsAdmin
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
            }, true);
            
        })();
        </script>';
    }
}

