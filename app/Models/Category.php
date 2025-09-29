<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'is_main'];

    public function stories()
    {
        return $this->belongsToMany(Story::class)
                    ->withTimestamps();
    }
    
    /**
     * Get only main categories
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }
}
