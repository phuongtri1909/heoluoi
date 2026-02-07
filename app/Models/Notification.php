<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'is_broadcast',
        'created_by',
    ];

    protected $casts = [
        'is_broadcast' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Scope: thông báo mà user được xem (broadcast hoặc có trong pivot)
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_broadcast', true)
                ->orWhereHas('recipients', fn ($r) => $r->where('users.id', $userId));
        });
    }

    /**
     * Scope: thông báo cho user kèm trạng thái đã đọc (user_read_at: null = chưa đọc)
     * Broadcast: chưa đọc = chưa có bản ghi notification_user; đã đọc = có bản ghi với read_at.
     * Targeted: đã có bản ghi khi gửi; đã đọc = read_at not null.
     */
    public function scopeForUserWithReadStatus($query, int $userId)
    {
        return $query->forUser($userId)
            ->leftJoin('notification_user', function ($join) use ($userId) {
                $join->on('notifications.id', '=', 'notification_user.notification_id')
                    ->where('notification_user.user_id', '=', $userId);
            })
            ->select('notifications.*', 'notification_user.read_at as user_read_at');
    }

    /**
     * Đếm số thông báo chưa đọc của user (chưa có bản ghi read = chưa đọc)
     */
    public static function unreadCountForUser(int $userId): int
    {
        return (int) static::forUserWithReadStatus($userId)
            ->whereNull('notification_user.read_at')
            ->count();
    }

    /**
     * Kiểm tra user đã đọc thông báo này chưa (có bản ghi với read_at)
     */
    public function isReadByUser(int $userId): bool
    {
        return \Illuminate\Support\Facades\DB::table('notification_user')
            ->where('notification_id', $this->id)
            ->where('user_id', $userId)
            ->whereNotNull('read_at')
            ->exists();
    }
}
