<?php

namespace App\Observers;

use App\Models\Chapter;
use Illuminate\Support\Facades\Cache;

class ChapterObserver
{
    /**
     * Clear home page cache when chapter is created, updated, or deleted
     * because it affects latest updated stories and top viewed stories
     */
    public function saved(Chapter $chapter): void
    {
        $this->clearCache($chapter);
    }

    public function deleted(Chapter $chapter): void
    {
        $this->clearCache($chapter);
    }

    private function clearCache(Chapter $chapter): void
    {
        // Clear home page cache
        Cache::forget('home_data_all');
        
        // Clear story detail page cache
        if ($chapter->relationLoaded('story') && $chapter->story) {
            $storySlug = $chapter->story->slug;
            Cache::forget('story_data_' . $storySlug);
        } elseif ($chapter->story_id) {
            // Load story to get slug if not loaded
            $story = \App\Models\Story::find($chapter->story_id);
            if ($story && $story->slug) {
                Cache::forget('story_data_' . $story->slug);
            }
        }
        
        // Clear chapter page cache - cần load tất cả chapters của story để clear cache
        if ($chapter->story_id) {
            $story = $chapter->relationLoaded('story') ? $chapter->story : \App\Models\Story::find($chapter->story_id);
            if ($story) {
                // Clear cache cho tất cả chapters của story này
                $chapters = \App\Models\Chapter::where('story_id', $story->id)
                    ->select('slug')
                    ->get();
                
                foreach ($chapters as $chap) {
                    Cache::forget('chapter_data_' . $story->slug . '_' . $chap->slug);
                }
                
                // Clear chapters list cache cho story detail page
                // Clear tất cả pages (có thể có nhiều pages)
                for ($page = 1; $page <= 20; $page++) {
                    Cache::forget('story_chapters_' . $story->id . '_page_' . $page);
                }
            }
        }
        
        // Clear top stories cache vì purchases có thể thay đổi
        Cache::forget('app_top_stories_' . \Carbon\Carbon::today()->format('Y-m-d'));
    }
}

