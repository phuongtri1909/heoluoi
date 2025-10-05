<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BanIp extends Model
{
    use HasFactory;
    protected $table = 'ban_ips';
    protected $fillable = ['ip_address', 'user_id', 'reason', 'banned_by', 'banned_at'];
    protected $casts = [
        'banned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}
