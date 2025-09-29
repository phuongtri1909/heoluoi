<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    protected $table = 'user_bans';

    protected $fillable = ['user_id', 'login', 'comment', 'rate', 'read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}