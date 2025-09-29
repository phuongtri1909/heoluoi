<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanIp extends Model
{
    use HasFactory;
    protected $table = 'ban_ips';
    protected $fillable = ['ip_address','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
