<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTransfer extends Model
{
    protected $fillable = [
        'from_admin_id',
        'to_user_id',
        'amount',
        'note',
        'status'
    ];

    /**
     * Get the admin who transferred cám
     */
    public function fromAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_admin_id');
    }

    /**
     * Get the user who received cám
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Đang chờ</span>',
            'completed' => '#39<span class="badge bg-success">Hoàn thành</span>',
            'rejected' => '<span class="badge bg-danger">Từ chối</span>',
            'default' => '<span class="badge bg-secondary">Không xác định</span>'
        };
    }
}