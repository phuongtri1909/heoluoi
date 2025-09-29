<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'max_per_day',
        'active',
        'order',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relationship với UserDailyTask
     */
    public function userDailyTasks()
    {
        return $this->hasMany(UserDailyTask::class);
    }

    // Các loại nhiệm vụ
    const TYPE_LOGIN = 'login';
    const TYPE_COMMENT = 'comment';
    const TYPE_BOOKMARK = 'bookmark';
    const TYPE_SHARE = 'share';

    /**
     * Lấy các nhiệm vụ đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Sắp xếp theo thứ tự
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    /**
     * Kiểm tra user đã hoàn thành nhiệm vụ hôm nay chưa
     */
    public function isCompletedByUserToday($userId)
    {
        $today = now()->format('Y-m-d');
        
        $userTask = \App\Models\UserDailyTask::where('user_id', $userId)
            ->where('daily_task_id', $this->id)
            ->where('task_date', $today)
            ->first();

        if (!$userTask) {
            return false;
        }

        return $userTask->completed_count >= $this->max_per_day;
    }

    /**
     * Lấy số lần đã hoàn thành hôm nay
     */
    public function getCompletedCountToday($userId)
    {
        $today = now()->format('Y-m-d');
        
        $userTask = \App\Models\UserDailyTask::where('user_id', $userId)
            ->where('daily_task_id', $this->id)
            ->where('task_date', $today)
            ->first();

        return $userTask ? $userTask->completed_count : 0;
    }

    /**
     * Lấy reward coin từ config
     */
    public function getCoinRewardAttribute($value)
    {
        if ($value > 0) {
            return $value;
        }

        $configKey = "daily_task_{$this->type}_reward";
        return \App\Models\Config::getConfig($configKey, 0);
    }

    /**
     * Tạo các nhiệm vụ mặc định
     */
    public static function createDefaultTasks()
    {
        $defaultTasks = [
            [
                'name' => 'Đăng nhập hàng ngày',
                'type' => self::TYPE_LOGIN,
                'description' => 'Đăng nhập vào trang web để nhận thưởng',
                'coin_reward' => 0,
                'max_per_day' => 1,
                'order' => 1,
            ],
            [
                'name' => 'Bình luận truyện',
                'type' => self::TYPE_COMMENT,
                'description' => 'Viết bình luận cho truyện để nhận thưởng',
                'coin_reward' => 0,
                'max_per_day' => 1,
                'order' => 2,
            ],
            [
                'name' => 'Theo dõi truyện',
                'type' => self::TYPE_BOOKMARK,
                'description' => 'Theo dõi truyện mới để nhận thưởng',
                'coin_reward' => 0,
                'max_per_day' => 1,
                'order' => 3,
            ],
            [
                'name' => 'Chia sẻ truyện',
                'type' => self::TYPE_SHARE,
                'description' => 'Chia sẻ truyện lên mạng xã hội để nhận thưởng',
                'coin_reward' => 0,
                'max_per_day' => 1,
                'order' => 4,
            ],
        ];

        foreach ($defaultTasks as $task) {
            if (!self::where('type', $task['type'])->exists()) {
                self::create($task);
            }
        }
    }
}