<?php

namespace App\Observers;

use App\Models\Story;
use Illuminate\Support\Facades\Cache;

class StoryObserver
{
    /**
     * Clear home page cache when story is created, updated, or deleted
     */
    public function saved(Story $story): void
    {
        $this->clearHomeCache($story);
    }

    public function deleted(Story $story): void
    {
        $this->clearHomeCache($story);
    }

    private function clearHomeCache(Story $story): void
    {
        // Clear home page cache
        Cache::forget('home_data_all');
        
        // Clear story detail page cache for this story
        if ($story->slug) {
            Cache::forget('story_data_' . $story->slug);
            
            // Clear chapters list cache cho story này
            for ($page = 1; $page <= 20; $page++) {
                Cache::forget('story_chapters_' . $story->id . '_page_' . $page);
            }
            
            // Clear chapter page caches
            $chapters = \App\Models\Chapter::where('story_id', $story->id)
                ->select('slug')
                ->get();
            
            foreach ($chapters as $chap) {
                Cache::forget('chapter_data_' . $story->slug . '_' . $chap->slug);
            }
            
            // Clear comments cache cho story này
            for ($page = 1; $page <= 20; $page++) {
                Cache::forget('story_comments_' . $story->id . '_page_' . $page);
            }
        }
        
        // Clear AppServiceProvider caches
        Cache::forget('app_categories_with_count'); // Categories có thể thay đổi
        Cache::forget('app_top_stories_' . \Carbon\Carbon::today()->format('Y-m-d')); // Top stories
        Cache::forget('app_banners_all'); // Banners có thể liên quan đến story
        Cache::forget('app_banners_hide18'); // Banners với hide18
    }
}

