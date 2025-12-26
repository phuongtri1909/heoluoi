<?php

namespace App\Observers;

use App\Models\Rating;
use Illuminate\Support\Facades\Cache;

class RatingObserver
{
    /**
     * Clear story cache when rating is created, updated, or deleted
     * (affects average_rating and ratings_count)
     */
    public function saved(Rating $rating): void
    {
        $this->clearStoryCache($rating->story_id);
    }

    public function deleted(Rating $rating): void
    {
        $this->clearStoryCache($rating->story_id);
    }

    private function clearStoryCache($storyId): void
    {
        if ($storyId) {
            $story = \App\Models\Story::find($storyId);
            if ($story && $story->slug) {
                Cache::forget('story_data_' . $story->slug);
                Cache::forget('home_data_all'); // Home page có thể hiển thị ratings
            }
        }
    }
}



