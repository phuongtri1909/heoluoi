<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'link',
        'story_id',
        'status',
        'link_aff',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    
}
