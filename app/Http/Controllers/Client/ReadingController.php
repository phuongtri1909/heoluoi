<?php

namespace App\Http\Controllers\Client;

use App\Models\Story;
use App\Models\Chapter;
use App\Services\ReadingHistoryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReadingController extends Controller
{
    protected $readingService;

    public function __construct(ReadingHistoryService $readingService)
    {
        $this->readingService = $readingService;
    }

    public function saveProgress(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'chapter_id' => 'required|exists:chapters,id',
            'progress_percent' => 'required|integer|min:0|max:100'
        ]);

        $story = Story::find($request->story_id);
        $chapter = Chapter::find($request->chapter_id);
        $progressPercent = $request->progress_percent;

        $this->readingService->saveReadingProgress($story, $chapter, $progressPercent);

        return response()->json(['success' => true]);
    }
}