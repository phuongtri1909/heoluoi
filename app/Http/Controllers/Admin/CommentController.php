<?php

namespace App\Http\Controllers\Admin;

use App\Models\Story;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\CommentReaction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{


    public function allComments(Request $request)
    {
        $authUser = auth()->user();
        $search = $request->search;
        $userId = $request->user;
        $storyId = $request->story;
        $date = $request->date;

        $query = Comment::with(['user', 'story', 'approver']);

        if ($authUser->role === 'mod') {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', ['user']);
            });
        }

        $baseQuery = clone $query;

        $matchingChildIds = collect([]);
        $parentIdsToInclude = collect([]);

        if ($search || $userId || $date) {
            $childQuery = clone $baseQuery;

            if ($search) {
                $childQuery->where('comment', 'like', '%' . $search . '%');
            }

            if ($userId) {
                $childQuery->where('user_id', $userId);
            }

            if ($date) {
                $childQuery->whereDate('created_at', $date);
            }

            $matchingChildIds = $childQuery->pluck('id');

            if ($matchingChildIds->isNotEmpty()) {
                $parentIds = Comment::whereIn('id', $matchingChildIds)
                    ->whereNotNull('reply_id')
                    ->pluck('reply_id');

                $allParentIds = collect([]);
                $currentParentIds = $parentIds;

                while ($currentParentIds->isNotEmpty()) {
                    $allParentIds = $allParentIds->merge($currentParentIds);
                    $currentParentIds = Comment::whereIn('id', $currentParentIds)
                        ->whereNotNull('reply_id')
                        ->pluck('reply_id');
                }

                $parentIdsToInclude = $allParentIds->unique();
            }
        }

        if ($storyId) {
            $query->where('story_id', $storyId);
        }

        $finalQuery = Comment::with(['user', 'story', 'approver'])
            ->with(['replies.user', 'replies.approver', 'replies.replies.user', 'replies.replies.approver', 'replies.replies.replies.user', 'replies.replies.replies.approver'])
            ->whereNull('reply_id');

        if ($search) {
            $finalQuery->where(function ($q) use ($search, $parentIdsToInclude, $matchingChildIds) {
                $q->where('comment', 'like', '%' . $search . '%')
                    ->orWhereIn('id', $parentIdsToInclude)
                    ->orWhereIn('id', $matchingChildIds->filter(function ($id) {
                        return Comment::find($id) && Comment::find($id)->reply_id === null;
                    }));
            });
        }

        if ($userId) {
            $finalQuery->where(function ($q) use ($userId, $parentIdsToInclude, $matchingChildIds) {
                $q->where('user_id', $userId)
                    ->orWhereIn('id', $parentIdsToInclude)
                    ->orWhereIn('id', $matchingChildIds->filter(function ($id) {
                        return Comment::find($id) && Comment::find($id)->reply_id === null;
                    }));
            });
        }

        if ($date) {
            $finalQuery->where(function ($q) use ($date, $parentIdsToInclude, $matchingChildIds) {
                $q->whereDate('created_at', $date)
                    ->orWhereIn('id', $parentIdsToInclude)
                    ->orWhereIn('id', $matchingChildIds->filter(function ($id) {
                        return Comment::find($id) && Comment::find($id)->reply_id === null;
                    }));
            });
        }

        if ($storyId) {
            $finalQuery->where('story_id', $storyId);
        }

        if ($authUser->role === 'mod') {
            $finalQuery->whereHas('user', function ($q) {
                $q->whereIn('role', ['user']);
            });
        } else {
            $finalQuery->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('role', '!=', 'admin');
                })
                ->orWhereDoesntHave('story', function($storyQuery) {
                    $storyQuery->whereColumn('stories.user_id', 'comments.user_id');
                });
            });
        }

        $comments = $finalQuery->orderBy('id', 'desc')->paginate(15);

        $stories = Story::orderBy('title')->get();

        $usersQuery = \App\Models\User::whereHas('comments')
            ->where('active', 'active');

        if ($authUser->role === 'mod') {
            $usersQuery->whereIn('role', ['user', 'vip']);
        }

        $users = $usersQuery->orderBy('name')->get();

        $totalComments = Comment::count();
        
        $pendingCommentsCount = Comment::where('approval_status', 'pending')
            ->whereHas('user', function($q) {
                $q->where('role', '!=', 'admin');
            })
            ->whereDoesntHave('story', function($q) {
                $q->whereColumn('stories.user_id', 'comments.user_id');
            })
            ->count();

        return view('admin.pages.comments.all', compact('comments', 'users', 'stories', 'totalComments', 'pendingCommentsCount'));
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy($comment)
    {
        $authUser = auth()->user();
        $comment = Comment::find($comment);
        if (!$comment) {
            return redirect()->route('admin.comments.all')->with('error', 'Không tìm thấy bình luận này');
        }

        if (
            $authUser->role === 'admin' ||
            ($authUser->role === 'mod' && (!$comment->user || $comment->user->role !== 'admin'))
        ) {
            $comment->delete();
            return redirect()->route('admin.comments.all')->with('success', 'Xóa bình luận thành công');
        }

        return redirect()->route('admin.comments.all')->with('error', 'Không thể xóa bình luận của Admin');
    }

    /**
     * Approve a comment
     */
    public function approve($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'mod') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã duyệt bình luận'
        ]);
    }

    /**
     * Reject a comment
     */
    public function reject($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'mod') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'approval_status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã từ chối bình luận'
        ]);
    }
}
