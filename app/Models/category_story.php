<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category_story extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'story_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
