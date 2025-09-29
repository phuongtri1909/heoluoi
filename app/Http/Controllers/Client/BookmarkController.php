<?php

namespace App\Http\Controllers\Client;

use App\Models\Story;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController
{

    public function toggle(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'chapter_id' => 'nullable|exists:chapters,id',
        ]);

        $userId = Auth::id();
        $storyId = $request->story_id;
        $chapterId = $request->chapter_id;

        $result = Bookmark::toggleBookmark($userId, $storyId, $chapterId);

        if ($result['status'] === 'added') {
            \App\Models\UserDailyTask::completeTask(
                $userId,
                \App\Models\DailyTask::TYPE_BOOKMARK,
                [
                    'story_id' => $storyId,
                    'bookmark_time' => now()->toISOString(),
                ],
                $request
            );
        }

        return response()->json($result);
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
        ]);

        $userId = Auth::id();
        $storyId = $request->story_id;

        $isBookmarked = Bookmark::isBookmarked($userId, $storyId);

        return response()->json([
            'is_bookmarked' => $isBookmarked
        ]);
    }

    public function getUserBookmarks()
    {
        $userId = Auth::id();
        $bookmarks = Bookmark::with([
            'story' => function ($query) {
                $query->withCount(['chapters' => function ($q) {
                    $q->where('status', 'published');
                }]);
            },
            'lastChapter'
        ])
            ->where('user_id', $userId)
            ->orderBy('last_read_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.information.bookmarks', compact('bookmarks'));
    }

    public function updateCurrentChapter(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        $userId = Auth::id();
        $storyId = $request->story_id;
        $chapterId = $request->chapter_id;

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('story_id', $storyId)
            ->first();

        if ($bookmark) {
            $updated = Bookmark::saveCurrentChapter($userId, $storyId, $chapterId);
            return response()->json([
                'success' => $updated,
                'message' => $updated ? 'Đã cập nhật vị trí đọc' : 'Không thể cập nhật vị trí đọc'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Bạn chưa đánh dấu truyện này'
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
        ]);

        $userId = Auth::id();
        $storyId = $request->story_id;

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('story_id', $storyId)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bookmark'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy bookmark'
        ]);
    }
}
