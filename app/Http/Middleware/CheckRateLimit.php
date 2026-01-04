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
                    $timeMessage = "{$minutesRemaining} phút {$secondsRemaining} giây";
                } else {
                    $timeMessage = "{$minutesRemaining} phút";
                }
            } else {
                $timeMessage = "{$secondsRemaining} giây";
            }
            
            $message = "Bạn đang bị chặn tạm thời {$timeMessage} vì chuyển trang liên tục nhiều lần.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'banned' => false,
                    'temp_ban' => true,
                    'banned_until' => $bannedUntil->toIso8601String()
                ], 403);
            }

            return response()->view('pages.rate-limit-ban', ['message' => $message], 403);
        }

        $result = $this->rateLimitService->checkRateLimit($user, $request->ip());

        if (!$result['allowed']) {
            $message = $result['message'];
            
            // Update message for permanent ban
            if (isset($result['action']) && $result['action'] === 'permanent_ban') {
                $message = "Bạn đã bị khóa tài khoản vĩnh viễn vì chuyển trang liên tục nhiều lần.";
            } elseif (isset($result['action']) && $result['action'] === 'temp_ban') {
                // Message already formatted in service
                $message = $result['message'] ?? "Bạn đang bị chặn tạm thời vì chuyển trang liên tục nhiều lần.";
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'banned' => $result['banned'] ?? false,
                    'action' => $result['action'] ?? null
                ], 403);
            }

            return response()->view('pages.rate-limit-ban', ['message' => $message], 403);
        }

        return $next($request);
    }
}
