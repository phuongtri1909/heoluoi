<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    protected $table = 'user_bans';

    protected $fillable = ['user_id', 'login', 'comment', 'rate', 'read', 'read_banned_until', 'temp_ban_count', 'permanent_ban_count', 'rate_limit_ban'];

    protected $casts = [
        'read_banned_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}