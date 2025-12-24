<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{
    /**
     * Clear comments cache when comment is created, updated, or deleted
     */
    public function saved(Comment $comment): void
    {
        $this->clearCommentsCache($comment->story_id);
    }

    public function deleted(Comment $comment): void
    {
        $this->clearCommentsCache($comment->story_id);
    }

    private function clearCommentsCache($storyId): void
    {
        if ($storyId) {
            // Clear all comment pages for this story (story detail page và chapter page)
            for ($page = 1; $page <= 20; $page++) {
                Cache::forget('story_comments_' . $storyId . '_page_' . $page);
            }
            
            // Clear chapter comments cache nếu có
            $story = \App\Models\Story::find($storyId);
            if ($story) {
                $chapters = \App\Models\Chapter::where('story_id', $storyId)
                    ->select('slug')
                    ->get();
                
                foreach ($chapters as $chap) {
                    for ($page = 1; $page <= 20; $page++) {
                        Cache::forget('chapter_comments_' . $story->slug . '_' . $chap->slug . '_page_' . $page);
                    }
                }
            }
        }
    }
}

