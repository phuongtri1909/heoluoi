<?php

namespace App\Observers;

use App\Models\ChapterPurchase;
use Illuminate\Support\Facades\Cache;

class ChapterPurchaseObserver
{
    /**
     * Clear top stories cache when chapter purchase is created
     * (affects top purchased stories)
     */
    public function saved(ChapterPurchase $purchase): void
    {
        $this->clearCaches($purchase);
    }

    public function deleted(ChapterPurchase $purchase): void
    {
        $this->clearCaches($purchase);
    }

    private function clearCaches(ChapterPurchase $purchase): void
    {
        // Clear top stories cache
        Cache::forget('app_top_stories_' . \Carbon\Carbon::today()->format('Y-m-d'));
        
        // Clear story cache
        if ($purchase->chapter_id) {
            $chapter = \App\Models\Chapter::find($purchase->chapter_id);
            if ($chapter && $chapter->story_id) {
                $story = \App\Models\Story::find($chapter->story_id);
                if ($story && $story->slug) {
                    Cache::forget('story_data_' . $story->slug);
                }
            }
        }
    }
}


