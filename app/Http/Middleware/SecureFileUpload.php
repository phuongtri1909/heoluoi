<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra nếu có file upload
        if ($request->hasFile('image') || $request->hasFile('avatar') || $request->hasFile('file')) {
            $files = $request->allFiles();
            
            foreach ($files as $file) {
                if (is_array($file)) {
                    foreach ($file as $singleFile) {
                        if (!$this->isFileSafe($singleFile)) {
                            $extension = strtolower($singleFile->getClientOriginalExtension());
                            $mimeType = $singleFile->getMimeType();
                            $errorMessage = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP). File của bạn có định dạng: $extension ($mimeType)";
                            
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json([
                                    'success' => false,
                                    'error' => 'File không được phép upload.',
                                    'message' => $errorMessage
                                ], 422);
                            } else {
                                return redirect()->back()->withErrors(['image' => $errorMessage])->withInput();
                            }
                        }
                    }
                } else {
                    if (!$this->isFileSafe($file)) {
                        $extension = strtolower($file->getClientOriginalExtension());
                        $mimeType = $file->getMimeType();
                        $errorMessage = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP). File của bạn có định dạng: $extension ($mimeType)";
                        
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'error' => 'File không được phép upload.',
                                'message' => $errorMessage
                            ], 422);
                        } else {
                            return redirect()->back()->withErrors(['image' => $errorMessage])->withInput();
                        }
                    }
                }
            }
        }

        return $next($request);
    }

    private function isFileSafe($file): bool
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        $dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
            'asp', 'aspx', 'ashx', 'asmx',
            'jsp', 'jspx',
            'pl', 'py', 'rb', 'sh', 'bash',
            'exe', 'bat', 'cmd', 'com',
            'js', 'vbs', 'wsf',
            'htaccess', 'htpasswd',
            'ini', 'log', 'sql',
            'dll', 'so', 'dylib'
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }

        $dangerousMimes = [
            'text/x-php',
            'text/html',
            'text/plain',
            'application/x-php',
            'application/x-executable',
            'application/x-dosexec',
            'application/x-msdownload',
            'application/x-msi',
            'application/x-msdos-program',
            'application/x-executable',
            'application/x-shockwave-flash',
            'application/x-javascript',
            'text/javascript',
            'application/javascript'
        ];

        $mimeType = $file->getMimeType();
        if (in_array($mimeType, $dangerousMimes)) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg' || $extension === 'gif' || $extension === 'webp') {
        } else {
            $content = file_get_contents($file->getRealPath());
            if ($this->containsPhpCode($content)) {
                return false;
            }
        }

        $allowedMimes = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ];

        return in_array($mimeType, $allowedMimes);
    }

    private function containsPhpCode($content): bool
    {
        $phpPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?/i',
            '/phpinfo\s*\(/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
            '/base64_decode\s*\(/i',
            '/gzinflate\s*\(/i',
            '/str_rot13\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/file_put_contents\s*\(/i',
            '/fopen\s*\(/i',
            '/fwrite\s*\(/i',
            '/include\s*\(/i',
            '/require\s*\(/i',
            '/include_once\s*\(/i',
            '/require_once\s*\(/i'
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
} 