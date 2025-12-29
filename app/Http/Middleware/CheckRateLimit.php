<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RateLimitService;
use Symfony\Component\HttpFoundation\Response;

class CheckRateLimit
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (in_array($user->role, ['admin_main'])) {
            return $next($request);
        }

        // Check if user is temporarily banned
        if ($this->rateLimitService->isTemporarilyBanned($user)) {
            $userBan = $user->userBan;
            $bannedUntil = $userBan->read_banned_until;
            
            // Tính số phút và giây còn lại
            $totalSeconds = now()->diffInSeconds($bannedUntil);
            $minutesRemaining = floor($totalSeconds / 60);
            $secondsRemaining = $totalSeconds % 60;
            
            // Hiển thị đẹp hơn
            if ($minutesRemaining > 0) {
                if ($secondsRemaining > 0) {
                    $message = "Bạn đang bị chặn tạm thời. Vui lòng thử lại sau {$minutesRemaining} phút {$secondsRemaining} giây.";
                } else {
                    $message = "Bạn đang bị chặn tạm thời. Vui lòng thử lại sau {$minutesRemaining} phút.";
                }
            } else {
                $message = "Bạn đang bị chặn tạm thời. Vui lòng thử lại sau {$secondsRemaining} giây.";
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'banned' => false,
                    'temp_ban' => true,
                    'banned_until' => $bannedUntil->toIso8601String()
                ], 403);
            }

            // Redirect về trang chủ thay vì back() để tránh vòng lặp redirect
            return redirect()->route('home')->with('error', $message);
        }

        $result = $this->rateLimitService->checkRateLimit($user, $request->ip());

        if (!$result['allowed']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $result['message'],
                    'banned' => $result['banned'] ?? false,
                    'action' => $result['action'] ?? null
                ], 429);
            }

            // Redirect về trang chủ thay vì back() để tránh vòng lặp redirect
            return redirect()->route('home')->with('error', $result['message']);
        }

        return $next($request);
    }
}
