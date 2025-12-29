<?php

namespace App\Services;

use App\Models\Config;
use App\Models\User;
use App\Models\RateLimitViolation;
use App\Models\UserBan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RateLimitService
{
    /**
     * Check if user is rate limited
     * Luồng mới:
     * 1. User chuyển trang 7 lần trong 30 giây → Ghi nhận 1 lần vi phạm
     * 2. Chia vi phạm: Lần cuối = ban vĩnh viễn, các lần còn lại chia đều cho delay và ban tạm thời
     * 3. Trong thời gian delay: User request → Delay (sleep) → Sau đó mới cho load data và vào trang
     *    Nếu trong thời gian delay, user vẫn cố request 7 lần vào trang chapter (tính từ lúc load được vào trang) 
     *    → Reset thời gian delay lại và ghi nhận vi phạm lần 2
     * 4. Sau khi hết delay: Trở lại bình thường
     * 5. Khi đến lần vi phạm được chia cho ban tạm thời: Chặn không cho truy cập vào trang chapter
     * 6. Lần vi phạm cuối cùng: Ban acc vĩnh viễn
     */
    public function checkRateLimit(User $user, string $ipAddress): array
    {
        // Check if rate limit is enabled
        if ((int) Config::getConfig('rate_limit_enabled', 1) === 0) {
            return ['allowed' => true];
        }

        $maxPages = (int) Config::getConfig('rate_limit_max_pages', 7);
        $timeWindow = (int) Config::getConfig('rate_limit_time_window', 30);
        $delaySeconds = (int) Config::getConfig('rate_limit_delay_seconds', 5);
        $delayCooldownMinutes = (int) Config::getConfig('rate_limit_delay_cooldown_minutes', 1);
        $delayCooldown = $delayCooldownMinutes * 60; // Convert phút sang giây
        $tempBanMinutes = (int) Config::getConfig('rate_limit_temp_ban_minutes', 30);
        $banThreshold = (int) Config::getConfig('rate_limit_ban_threshold', 3);

        // Get cache key for user's chapter access history (chỉ tính từ lúc load được vào trang)
        $cacheKey = "rate_limit_{$user->id}";
        $accessHistory = Cache::get($cacheKey, []);
        
        // Get last delay start time (thời gian bắt đầu delay gần nhất)
        $lastDelayKey = "rate_limit_delay_{$user->id}";
        $lastDelayTime = Cache::get($lastDelayKey);
        
        // Get last access time (thời gian load được vào trang gần nhất, sau delay)
        $lastAccessKey = "rate_limit_access_{$user->id}";
        $lastAccessTime = Cache::get($lastAccessKey);

        $now = now()->timestamp;
        
        // Nếu đang trong thời gian delay, chưa cho vào trang
        if ($lastDelayTime) {
            $delayElapsed = $now - $lastDelayTime;
            
            // Nếu chưa hết thời gian delay → Delay request này
            if ($delayElapsed < $delaySeconds) {
                // Delay request này
                sleep($delaySeconds - $delayElapsed);
                
                // Sau khi delay xong, lưu thời gian access (lúc này mới được vào trang)
                $accessTime = now()->timestamp;
                Cache::put($lastAccessKey, $accessTime, $delayCooldown);
                
                // Thêm access vào history (chỉ tính từ lúc load được vào trang)
                $accessHistory[] = $accessTime;
                Cache::put($cacheKey, $accessHistory, $timeWindow);
                
                return [
                    'allowed' => true,
                    'message' => 'Bạn đang chuyển trang quá nhanh. Vui lòng chờ một chút.',
                    'delayed' => true,
                    'delay_seconds' => $delaySeconds
                ];
            }
            
            // Nếu đã hết delay nhưng chưa hết cooldown
            if ($delayElapsed < ($delaySeconds + $delayCooldown)) {
                // Trong thời gian cooldown, user có thể request bình thường
                // Nhưng nếu vi phạm tiếp (7 lần trong 30 giây) → Reset delay và ghi nhận vi phạm mới
            } else {
                // Hết cooldown → Clear delay time
                Cache::forget($lastDelayKey);
            }
        }
        
        // Remove old entries outside time window
        $accessHistory = array_filter($accessHistory, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });

        // Count pages accessed in time window (chỉ tính từ lúc load được vào trang)
        // Nếu có lastAccessTime, chỉ tính các access sau lastAccessTime
        // Nhưng nếu đã hết cooldown, tính tất cả
        if ($lastAccessTime && $lastDelayTime) {
            $delayElapsed = $now - $lastDelayTime;
            if ($delayElapsed < ($delaySeconds + $delayCooldown)) {
                // Vẫn trong cooldown, chỉ tính access sau lastAccessTime
                $accessHistory = array_filter($accessHistory, function($timestamp) use ($lastAccessTime) {
                    return $timestamp >= $lastAccessTime;
                });
            }
        }

        // Count pages accessed in time window
        $pagesInWindow = count($accessHistory);

        if ($pagesInWindow >= $maxPages) {
            // Rate limit exceeded - 7 lần request trong 30 giây = 1 lần vi phạm
            $this->recordViolation($user, $ipAddress);
            
            // Clear access history sau khi vi phạm
            $accessHistory = [];
            Cache::put($cacheKey, $accessHistory, $timeWindow);
            
            // Get violation count today
            $violationCount = $this->getViolationCountToday($user);
            
            // Calculate stages
            // (threshold - 1) violations are split into delay and temp ban
            // Last violation is permanent ban
            // Priority: delay gets more if odd number (ceil), temp ban gets less (floor)
            $preBanViolations = $banThreshold - 1;
            $delayCount = ceil($preBanViolations / 2);
            $tempBanCount = floor($preBanViolations / 2);
            
            // Determine action based on violation count
            if ($violationCount >= $banThreshold) {
                // Permanent ban (last violation)
                $this->banUser($user);
                return [
                    'allowed' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa do vi phạm rate limit nhiều lần.',
                    'banned' => true,
                    'action' => 'permanent_ban'
                ];
            } elseif ($violationCount > $delayCount) {
                // Temporary ban
                $this->tempBanUser($user);
                return [
                    'allowed' => false,
                    'message' => "Bạn đã bị chặn tạm thời {$tempBanMinutes} phút do vi phạm rate limit.",
                    'banned' => false,
                    'action' => 'temp_ban',
                    'temp_ban_until' => now()->addMinutes($tempBanMinutes)->toIso8601String()
                ];
            } else {
                // Delay response (first stage)
                // Lưu thời gian delay để track
                Cache::put($lastDelayKey, $now, $delaySeconds + $delayCooldown);
                
                // Delay request này
                sleep($delaySeconds);
                
                // Sau khi delay xong, lưu thời gian access (lúc này mới được vào trang)
                $accessTime = now()->timestamp;
                Cache::put($lastAccessKey, $accessTime, $delayCooldown);
                
                // Thêm access vào history (chỉ tính từ lúc load được vào trang)
                $accessHistory[] = $accessTime;
                Cache::put($cacheKey, $accessHistory, $timeWindow);
                
                return [
                    'allowed' => true,
                    'message' => 'Bạn đang chuyển trang quá nhanh. Vui lòng chờ một chút.',
                    'delayed' => true,
                    'delay_seconds' => $delaySeconds
                ];
            }
        }

        // Add current access to history (khi chưa vi phạm)
        // Chỉ thêm vào history nếu đã load được vào trang (không trong delay)
        if (!$lastDelayTime || (now()->timestamp - $lastDelayTime) >= $delaySeconds) {
            $accessHistory[] = $now;
            Cache::put($cacheKey, $accessHistory, $timeWindow);
            
            // Cập nhật lastAccessTime
            Cache::put($lastAccessKey, $now, $delayCooldown);
        }

        return ['allowed' => true];
    }

    /**
     * Record a rate limit violation
     * Chỉ ghi nhận 1 lần vi phạm cho mỗi violation event (7 lần request trong 30 giây)
     */
    protected function recordViolation(User $user, string $ipAddress): void
    {
        // Cache key dựa trên time window để chỉ record 1 lần cho mỗi violation event
        $timeWindow = (int) Config::getConfig('rate_limit_time_window', 30);
        $windowStart = floor(now()->timestamp / $timeWindow) * $timeWindow;
        $cacheKey = "violation_recorded_{$user->id}_{$windowStart}";
        
        if (!Cache::has($cacheKey)) {
            RateLimitViolation::create([
                'user_id' => $user->id,
                'ip_address' => $ipAddress,
                'violated_at' => now(),
            ]);

            // Cache trong thời gian time window để prevent duplicate records
            Cache::put($cacheKey, true, $timeWindow);
        }
    }

    /**
     * Temporarily ban user for reading
     */
    protected function tempBanUser(User $user): void
    {
        // Skip if already permanently banned or is admin
        if (in_array($user->role, ['admin_main', 'admin_sub']) || ($user->userBan->read ?? false)) {
            return;
        }

        $tempBanMinutes = (int) Config::getConfig('rate_limit_temp_ban_minutes', 30);
        $userBan = $user->userBan()->firstOrCreate(['user_id' => $user->id]);
        $userBan->read_banned_until = now()->addMinutes($tempBanMinutes);
        $userBan->save();
        
        // Clear rate limit cache
        Cache::forget("rate_limit_{$user->id}");
        Cache::forget("rate_limit_delay_{$user->id}");
        Cache::forget("rate_limit_access_{$user->id}");
    }

    /**
     * Ban user permanently (ban both read and login)
     */
    protected function banUser(User $user): void
    {
        // Skip if already banned or is admin
        if (in_array($user->role, ['admin_main', 'admin_sub']) || ($user->userBan->read ?? false)) {
            return;
        }

        $userBan = $user->userBan()->firstOrCreate(['user_id' => $user->id]);
        $userBan->read = true; // Ban đọc nội dung
        $userBan->login = true; // Ban đăng nhập
        $userBan->read_banned_until = null; // Clear temp ban if exists
        $userBan->save();
        
        // Clear rate limit cache
        Cache::forget("rate_limit_{$user->id}");
        Cache::forget("rate_limit_delay_{$user->id}");
        Cache::forget("rate_limit_access_{$user->id}");
    }

    /**
     * Check if user is temporarily banned
     */
    public function isTemporarilyBanned(User $user): bool
    {
        $userBan = $user->userBan;
        if (!$userBan || !$userBan->read_banned_until) {
            return false;
        }

        // Check if temp ban has expired
        if ($userBan->read_banned_until->isPast()) {
            // Clear expired temp ban
            $userBan->read_banned_until = null;
            $userBan->save();
            return false;
        }

        return true;
    }

    /**
     * Unban user (admin only) - clears both permanent and temporary ban
     */
    public function unbanUser(User $user): bool
    {
        $userBan = $user->userBan;
        
        if ($userBan && ($userBan->read || $userBan->read_banned_until)) {
            $userBan->read = false;
            $userBan->login = false; // Unban login nếu bị ban do rate limit
            $userBan->read_banned_until = null;
            $userBan->save();

            // Clear rate limit cache for this user
            Cache::forget("rate_limit_{$user->id}");
            Cache::forget("rate_limit_delay_{$user->id}");
            Cache::forget("rate_limit_access_{$user->id}");

            return true;
        }

        return false;
    }

    /**
     * Get violation count for user today (từ 00:00:00 của ngày hiện tại)
     */
    public function getViolationCountToday(User $user): int
    {
        $startOfToday = now()->startOfDay();
        
        return RateLimitViolation::where('user_id', $user->id)
            ->where('violated_at', '>=', $startOfToday)
            ->count();
    }

    /**
     * Clear rate limit cache for user (useful for testing or manual reset)
     */
    public function clearRateLimitCache(User $user): void
    {
        Cache::forget("rate_limit_{$user->id}");
        Cache::forget("rate_limit_delay_{$user->id}");
        Cache::forget("rate_limit_access_{$user->id}");
    }
}
