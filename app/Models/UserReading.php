<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'story_id',
        'chapter_id',
        'progress_percent',
    ];

    /**
     * Lấy thông tin người dùng sở hữu
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy thông tin truyện được đọc
     */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Lấy thông tin chương đã đọc
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}