<?php

namespace App\Observers;

use App\Models\Bookmark;
use Illuminate\Support\Facades\Cache;

class BookmarkObserver
{
    /**
     * Clear story cache when bookmark is created or deleted
     * (affects total_bookmarks count)
     */
    public function saved(Bookmark $bookmark): void
    {
        $this->clearStoryCache($bookmark->story_id);
    }

    public function deleted(Bookmark $bookmark): void
    {
        $this->clearStoryCache($bookmark->story_id);
    }

    private function clearStoryCache($storyId): void
    {
        if ($storyId) {
            $story = \App\Models\Story::find($storyId);
            if ($story && $story->slug) {
                Cache::forget('story_data_' . $story->slug);
                Cache::forget('home_data_all'); // Home page có thể hiển thị bookmarks count
            }
        }
    }
}

