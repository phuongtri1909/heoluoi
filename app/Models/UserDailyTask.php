<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserDailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daily_task_id',
        'task_date',
        'completed_count',
        'coin_reward',
        'last_completed_at',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'task_date' => 'date',
        'last_completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relationship với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship với DailyTask
     */
    public function dailyTask()
    {
        return $this->belongsTo(DailyTask::class);
    }

    /**
     * Hoàn thành nhiệm vụ với bảo mật chống buff
     */
    public static function completeTask($userId, $taskType, $metadata = [], $request = null)
    {
        try {
            return DB::transaction(function () use ($userId, $taskType, $metadata, $request) {
                $task = DailyTask::where('type', $taskType)->where('active', true)->first();
                
                if (!$task) {
                    return ['success' => false, 'message' => 'Nhiệm vụ không tồn tại'];
                }

                $today = now()->format('Y-m-d');
                $ipAddress = $request ? $request->ip() : request()->ip();
                $userAgent = $request ? $request->userAgent() : request()->userAgent();

                $securityCheck = self::checkSecurity($userId, $taskType, $ipAddress, $userAgent);
                if (!$securityCheck['allowed']) {
                    return ['success' => false, 'message' => $securityCheck['message']];
                }

                $userTask = self::firstOrCreate([
                    'user_id' => $userId,
                    'daily_task_id' => $task->id,
                    'task_date' => $today,
                ], [
                    'completed_count' => 0,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'metadata' => $metadata,
                ]);

                if ($userTask->completed_count >= $task->max_per_day) {
                    return ['success' => false, 'message' => 'Đã hoàn thành tối đa nhiệm vụ này trong ngày'];
                }

                // Lấy coin_reward từ config tại thời điểm hiện tại
                $coinReward = \App\Models\Config::getConfig("daily_task_{$task->type}_reward", 0);
                
                $userTask->increment('completed_count');
                $userTask->update([
                    'coin_reward' => $coinReward, // Lưu coin_reward vào user_daily_tasks
                    'last_completed_at' => now(),
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'metadata' => array_merge($userTask->metadata ?? [], $metadata),
                ]);

                $user = User::find($userId);
                
                // Sử dụng CoinService để ghi lịch sử
                $coinService = new \App\Services\CoinService();
                $coinService->addCoins(
                    $user,
                    $coinReward,
                    \App\Models\CoinHistory::TYPE_DAILY_TASK,
                    "Hoàn thành nhiệm vụ: {$task->name}",
                    $userTask
                );

                return [
                    'success' => true,
                    'message' => "Hoàn thành nhiệm vụ '{$task->name}' và nhận được {$coinReward} xu!",
                    'coins_earned' => $coinReward,
                    'completed_count' => $userTask->completed_count,
                    'max_per_day' => $task->max_per_day,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Daily task completion error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại'];
        }
    }

    /**
     * Kiểm tra bảo mật chống buff
     */
    protected static function checkSecurity($userId, $taskType, $ipAddress, $userAgent)
    {
        $now = now();
        $today = $now->format('Y-m-d');

        $recentAttempts = self::whereHas('dailyTask', function ($query) use ($taskType) {
                $query->where('type', $taskType);
            })
            ->where('user_id', $userId)
            ->where('last_completed_at', '>', $now->subMinutes(1))
            ->count();

        if ($recentAttempts >= 3) {
            return ['allowed' => false, 'message' => 'Thực hiện quá nhanh, vui lòng chờ 1 phút'];
        }

        if ($taskType !== DailyTask::TYPE_LOGIN) {
            $ipAttempts = self::where('ip_address', $ipAddress)
                ->where('task_date', $today)
                ->whereHas('dailyTask', function ($query) use ($taskType) {
                    $query->where('type', $taskType);
                })
                ->distinct('user_id')
                ->count();

            if ($ipAttempts >= 10) {
                return ['allowed' => false, 'message' => 'IP này đã có quá nhiều hoạt động nghi vấn'];
            }
        }

        $recentUserAgent = self::where('user_id', $userId)
            ->where('task_date', $today)
            ->whereNotNull('user_agent')
            ->where('user_agent', '!=', $userAgent)
            ->exists();

        if ($recentUserAgent && $taskType !== DailyTask::TYPE_LOGIN) {
            return ['allowed' => false, 'message' => 'Phát hiện thay đổi trình duyệt bất thường'];
        }

        return ['allowed' => true, 'message' => 'OK'];
    }

    /**
     * Lấy lịch sử nhiệm vụ của user
     */
    public static function getUserTaskHistory($userId, $limit = 20)
    {
        return self::with(['dailyTask'])
            ->where('user_id', $userId)
            ->where('completed_count', '>', 0)
            ->orderBy('last_completed_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Lấy thống kê nhiệm vụ của user
     */
    public static function getUserTaskStats($userId)
    {
        $today = now()->format('Y-m-d');
        $thisWeek = now()->startOfWeek()->format('Y-m-d');
        $thisMonth = now()->startOfMonth()->format('Y-m-d');

        return [
            'today' => self::where('user_id', $userId)
                ->where('task_date', $today)
                ->sum('completed_count'),
            'this_week' => self::where('user_id', $userId)
                ->where('task_date', '>=', $thisWeek)
                ->sum('completed_count'),
            'this_month' => self::where('user_id', $userId)
                ->where('task_date', '>=', $thisMonth)
                ->sum('completed_count'),
            'total' => self::where('user_id', $userId)
                ->sum('completed_count'),
        ];
    }

    /**
     * Reset nhiệm vụ hàng ngày (chạy cron job)
     */
    public static function resetDailyTasks()
    {
        return true;
    }
}