<?php

namespace App\Observers;

use App\Models\StoryPurchase;
use Illuminate\Support\Facades\Cache;

class PurchaseObserver
{
    /**
     * Clear top stories cache when story purchase is created
     * (affects top purchased stories)
     */
    public function saved(StoryPurchase $purchase): void
    {
        $this->clearCaches($purchase);
    }

    public function deleted(StoryPurchase $purchase): void
    {
        $this->clearCaches($purchase);
    }

    private function clearCaches(StoryPurchase $purchase): void
    {
        // Clear top stories cache
        Cache::forget('app_top_stories_' . \Carbon\Carbon::today()->format('Y-m-d'));
        
        // Clear story cache
        if ($purchase->story_id) {
            $story = \App\Models\Story::find($purchase->story_id);
            if ($story && $story->slug) {
                Cache::forget('story_data_' . $story->slug);
            }
        }
    }
}

