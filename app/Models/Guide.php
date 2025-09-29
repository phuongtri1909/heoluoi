<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'meta_description',
        'meta_keywords',
        'is_published'
    ];
    
    protected $casts = [
        'is_published' => 'boolean'
    ];
    
    /**
     * Scope a query to only include published guides.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
} 