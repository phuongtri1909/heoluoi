<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterReport extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id', 
        'story_id',
        'description',
        'status',
        'admin_response'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Chờ xử lý</span>',
            'processing' => '<span class="badge bg-info">Đang xử lý</span>',
            'resolved' => '<span class="badge bg-success">Đã xử lý</span>',
            'rejected' => '<span class="badge bg-danger">Từ chối</span>',
            default => '<span class="badge bg-secondary">Không xác định</span>'
        };
    }
}
