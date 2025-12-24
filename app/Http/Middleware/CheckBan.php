<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBan
{
    /**
     * Request-level cache để tránh duplicate query
     * Key: IP address, Value: boolean (is banned)
     */
    private static array $ipBanCache = [];

    public function handle(Request $request, Closure $next, $action = null): Response
    {
        $user = Auth::user();

        if ($user && $action) {
            $userBan = $user->userBan;
            if ($userBan) {
                if ($action === 'login' && $userBan->login) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Tài khoản của bạn đã bị cấm đăng nhập.'], 403);
                    }
                    abort(403, 'Tài khoản của bạn đã bị cấm đăng nhập.');
                }
                if ($action === 'comment' && $userBan->comment) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Bạn đã bị cấm bình luận.'], 403);
                    }
                    abort(403, 'Bạn đã bị cấm bình luận.');
                }
                if ($action === 'rate' && $userBan->rate) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Bạn đã bị cấm đánh giá.'], 403);
                    }
                    abort(403, 'Bạn đã bị cấm đánh giá.');
                }
                if ($action === 'read' && $userBan->read) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Bạn đã bị cấm đọc nội dung.'], 403);
                    }
                    abort(403, 'Bạn đã bị cấm đọc nội dung.');
                }
            }
        }

        // Cache kết quả check IP trong request để tránh duplicate query
        $ip = $request->ip();
        
        // Kiểm tra cache trước
        if (!isset(self::$ipBanCache[$ip])) {
            // Check IP ban và cache kết quả
            self::$ipBanCache[$ip] = \App\Models\BanIp::where('ip_address', $ip)->exists();
        }
        
        $isBanned = self::$ipBanCache[$ip];

        if ($isBanned) {
            sleep(10);
            if($request->ajax()){
                return response()->json(['message' => 'IP của bạn đã bị cấm.'], 403);
            }
            abort(403);
        }

        return $next($request);
    }
}
