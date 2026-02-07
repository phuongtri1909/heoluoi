<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoryKeyword extends Model
{
    use HasFactory;

    protected $fillable = ['story_id', 'keyword'];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
