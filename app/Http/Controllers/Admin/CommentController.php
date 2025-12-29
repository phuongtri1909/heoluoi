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
        $approvalStatus = $request->approval_status;

        $query = Comment::with(['user', 'story', 'approver']);

        if ($authUser->role === 'admin_sub') {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', ['user']);
            });
        }

        $baseQuery = clone $query;

        $matchingChildIds = collect([]);
        $parentIdsToInclude = collect([]);

        if ($search || $userId || $date || $approvalStatus) {
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

            if ($approvalStatus) {
                $childQuery->where('approval_status', $approvalStatus);
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
            ->with(['replies.user', 'replies.story', 'replies.approver', 'replies.replies.user', 'replies.replies.story', 'replies.replies.approver', 'replies.replies.replies.user', 'replies.replies.replies.story', 'replies.replies.replies.approver'])
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

        if ($approvalStatus) {
            $finalQuery->where(function ($q) use ($approvalStatus, $parentIdsToInclude, $matchingChildIds) {
                $q->where('approval_status', $approvalStatus)
                    ->orWhereIn('id', $parentIdsToInclude)
                    ->orWhereIn('id', $matchingChildIds->filter(function ($id) {
                        return Comment::find($id) && Comment::find($id)->reply_id === null;
                    }));
            });
        }

        if ($storyId) {
            $finalQuery->where('story_id', $storyId);
        }

        if ($authUser->role === 'admin_sub') {
            $finalQuery->whereHas('user', function ($q) {
                $q->whereIn('role', ['user']);
            });
        } else {
            $finalQuery->where(function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('role', '!=', 'admin_main');
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

        if ($authUser->role === 'admin_sub') {
            $usersQuery->whereIn('role', ['user']);
        }

        $users = $usersQuery->orderBy('name')->get();

        $totalComments = Comment::count();
        
        $pendingCommentsCount = Comment::where('approval_status', 'pending')
            ->whereHas('user', function($q) {
                $q->where('role', '!=', 'admin_main')->where('role', '!=', 'admin_sub');
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
        $comment = Comment::with(['user', 'story'])->find($comment);
        if (!$comment) {
            return redirect()->route('admin.comments.all')->with('error', 'Không tìm thấy bình luận này');
        }

        if (
            $authUser->role === 'admin_main' || $authUser->role === 'admin_sub' ||
            (!$comment->user || $comment->user->role !== 'admin_main' && $comment->user->role !== 'admin_sub')
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
        $comment = Comment::with(['user', 'story'])->findOrFail($commentId);
        
        if (auth()->user()->role !== 'admin_main' && auth()->user()->role !== 'admin_sub') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $comment->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);

        // Complete daily task for comment when approved
        $user = $comment->user;
        if ($user && $user->role !== 'admin_main' && $user->role !== 'admin_sub') {
            \App\Models\UserDailyTask::completeTask(
                $comment->user_id,
                \App\Models\DailyTask::TYPE_COMMENT,
                [
                    'story_id' => $comment->story_id,
                    'comment_id' => $comment->id,
                    'comment_time' => $comment->approved_at->toISOString(),
                ],
                request()
            );
        }

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
        $comment = Comment::with(['user', 'story'])->findOrFail($commentId);
        
        if (auth()->user()->role !== 'admin_main' && auth()->user()->role !== 'admin_sub') {
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

    /**
     * Approve multiple comments at once
     */
    public function approveBatch(Request $request)
    {
        if (auth()->user()->role !== 'admin_main' && auth()->user()->role !== 'admin_sub') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $commentIds = $request->input('comment_ids', []);
        
        if (empty($commentIds) || !is_array($commentIds)) {
            return response()->json(['status' => 'error', 'message' => 'Không có bình luận nào được chọn'], 400);
        }

        $comments = Comment::with(['user', 'story'])->whereIn('id', $commentIds)->get();
        $approvedCount = 0;

        foreach ($comments as $comment) {
            // Only approve comments that are pending and meet the criteria
            if ($comment->approval_status === 'pending' && 
                $comment->user && 
                $comment->user->role !== 'admin_main' && 
                $comment->user->role !== 'admin_sub' &&
                $comment->story && 
                $comment->story->user_id !== $comment->user_id) {
                
                $comment->update([
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id()
                ]);

                // Complete daily task for comment when approved
                if ($comment->user && $comment->user->role !== 'admin_main' && $comment->user->role !== 'admin_sub') {
                    \App\Models\UserDailyTask::completeTask(
                        $comment->user_id,
                        \App\Models\DailyTask::TYPE_COMMENT,
                        [
                            'story_id' => $comment->story_id,
                            'comment_id' => $comment->id,
                            'comment_time' => $comment->approved_at->toISOString(),
                        ],
                        $request
                    );
                }

                $approvedCount++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã duyệt {$approvedCount} bình luận",
            'approved_count' => $approvedCount
        ]);
    }

    /**
     * Reject multiple comments at once
     */
    public function rejectBatch(Request $request)
    {
        if (auth()->user()->role !== 'admin_main' && auth()->user()->role !== 'admin_sub') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $commentIds = $request->input('comment_ids', []);
        
        if (empty($commentIds) || !is_array($commentIds)) {
            return response()->json(['status' => 'error', 'message' => 'Không có bình luận nào được chọn'], 400);
        }

        $comments = Comment::with(['user', 'story'])->whereIn('id', $commentIds)->get();
        $rejectedCount = 0;

        foreach ($comments as $comment) {
            // Only reject comments that are pending and meet the criteria
            if ($comment->approval_status === 'pending' && 
                $comment->user && 
                $comment->user->role !== 'admin_main' && 
                $comment->user->role !== 'admin_sub' &&
                $comment->story && 
                $comment->story->user_id !== $comment->user_id) {
                
                $comment->update([
                    'approval_status' => 'rejected',
                    'approved_at' => now(),
                    'approved_by' => auth()->id()
                ]);

                $rejectedCount++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã từ chối {$rejectedCount} bình luận",
            'rejected_count' => $rejectedCount
        ]);
    }
}
