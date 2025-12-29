<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateLimitViolation extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'violated_at',
    ];

    protected $casts = [
        'violated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
