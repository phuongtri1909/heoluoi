<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'story_id',
        'amount_paid',
        'amount_received'
    ];

    /**
     * Get the user who made the purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchased story
     */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Get the price (alias for amount_paid)
     */
    public function getPriceAttribute()
    {
        return $this->amount_paid;
    }

    /**
     * Check if a user has purchased a story combo
     */
    public static function hasUserPurchased($userId, $storyId)
    {
        return self::where('user_id', $userId)
                   ->where('story_id', $storyId)
                   ->exists();
    }

    /**
     * Get user's purchased stories
     */
    public static function getUserPurchasedStories($userId)
    {
        return self::where('user_id', $userId)
                   ->with('story')
                   ->get();
    }
}
