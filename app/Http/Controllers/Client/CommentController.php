<?php

namespace App\Http\Controllers\Client;

use App\Models\Story;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\CommentReaction;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{

    public function react(Request $request, $commentId)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để thực hiện',
                'redirect' => route('login')
            ], 401);
        }

        $comment = Comment::findOrFail($commentId);
        $type = $request->type;
        $userId = auth()->id();

        if (!in_array($type, ['like', 'dislike'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loại phản ứng không hợp lệ'
            ], 400);
        }

        $existingReaction = CommentReaction::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $type) {
                $existingReaction->delete();
                $message = $type === 'like' ? 'Đã bỏ thích bình luận' : 'Đã bỏ không thích bình luận';
            } else {
                $existingReaction->update(['type' => $type]);
                $message = $type === 'like' ? 'Đã thích bình luận' : 'Đã không thích bình luận';
            }
        } else {
            CommentReaction::create([
                'comment_id' => $commentId,
                'user_id' => $userId,
                'type' => $type
            ]);
            $message = $type === 'like' ? 'Đã thích bình luận' : 'Đã không thích bình luận';
        }

        $likes = CommentReaction::where('comment_id', $commentId)->where('type', 'like')->count();
        $dislikes = CommentReaction::where('comment_id', $commentId)->where('type', 'dislike')->count();

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }

    public function loadComments(Request $request, $storyId)
    {
        $pinnedComments = Comment::with([
            'user',
            'approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.user',
            'approvedReplies.reactions',
            'approvedReplies.approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.approvedReplies.user',
            'approvedReplies.approvedReplies.reactions',
            'reactions'
        ])
            ->where('story_id', $storyId)
            ->whereNull('reply_id')
            ->where('is_pinned', true)
            ->approved()
            ->latest('pinned_at')
            ->get();

        $regularComments = Comment::with([
            'user',
            'approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.user',
            'approvedReplies.reactions',
            'approvedReplies.approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.approvedReplies.user',
            'approvedReplies.approvedReplies.reactions',
            'reactions'
        ])
            ->where('story_id', $storyId)
            ->whereNull('reply_id')
            ->where('is_pinned', false)
            ->approved()
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            $html = view('components.comments-list', compact('pinnedComments', 'regularComments'))->render();
            return response()->json([
                'html' => $html,
                'hasMore' => $regularComments->hasMorePages()
            ]);
        }

        return view('components.comment', compact('pinnedComments', 'regularComments', 'storyId'));
    }

    public function togglePin($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if (auth()->user()->role !== 'admin_main' && auth()->user()->role !== 'admin_sub' || $comment->level !== 0) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if (!$comment->is_pinned) {
            $pinnedCount = Comment::where('is_pinned', true)->count();
            if ($pinnedCount >= 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã đạt giới hạn số bình luận được ghim'
                ], 400);
            }
        }

        $comment->is_pinned = !$comment->is_pinned;
        $comment->pinned_at = $comment->is_pinned ? now() : null;
        $comment->save();

        $pinnedComments = Comment::with([
            'user',
            'approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.user',
            'approvedReplies.reactions',
            'approvedReplies.approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.approvedReplies.user',
            'approvedReplies.approvedReplies.reactions',
            'reactions'
        ])
            ->where('story_id', $comment->story_id)
            ->whereNull('reply_id')
            ->where('is_pinned', true)
            ->approved()
            ->latest('pinned_at')
            ->get();

        $regularComments = Comment::with([
            'user',
            'approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.user',
            'approvedReplies.reactions',
            'approvedReplies.approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.approvedReplies.user',
            'approvedReplies.approvedReplies.reactions',
            'reactions'
        ])
            ->where('story_id', $comment->story_id)
            ->whereNull('reply_id')
            ->where('is_pinned', false)
            ->approved()
            ->latest()
            ->paginate(10);

        $html = view('components.comments-list', compact('pinnedComments', 'regularComments'))->render();

        return response()->json([
            'status' => 'success',
            'message' => $comment->is_pinned ? 'Đã ghim bình luận' : 'Đã bỏ ghim bình luận',
            'is_pinned' => $comment->is_pinned,
            'html' => $html
        ]);
    }

    public function deleteComment($comment)
    {
        try {
            $authUser = auth()->user();
            $comment = Comment::with('user')->find($comment);

            if (!$comment) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bình luận đã được xóa'
                ]);
            }

            $isPinned = $comment->is_pinned;
            $storyId = $comment->story_id;

            if ($authUser->role === 'admin_main' || $authUser->role === 'admin_sub') {
                $comment->delete();
                
                if ($isPinned) {
                    $pinnedComments = Comment::with([
                        'user',
                        'approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.user',
                        'approvedReplies.reactions',
                        'approvedReplies.approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.approvedReplies.user',
                        'approvedReplies.approvedReplies.reactions',
                        'reactions'
                    ])
                        ->where('story_id', $storyId)
                        ->whereNull('reply_id')
                        ->where('is_pinned', true)
                        ->approved()
                        ->latest('pinned_at')
                        ->get();
                    
                    $regularComments = Comment::with([
                        'user',
                        'approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.user',
                        'approvedReplies.reactions',
                        'approvedReplies.approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.approvedReplies.user',
                        'approvedReplies.approvedReplies.reactions',
                        'reactions'
                    ])
                        ->where('story_id', $storyId)
                        ->whereNull('reply_id')
                        ->where('is_pinned', false)
                        ->approved()
                        ->latest()
                        ->paginate(10);
                    
                    $html = view('components.comments-list', compact('pinnedComments', 'regularComments'))->render();
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Xóa bình luận thành công',
                        'isPinned' => true,
                        'html' => $html
                    ]);
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Xóa bình luận thành công'
                ]);
            }

            if ($authUser->role === 'admin_main' || $authUser->role === 'admin_sub') {
                if ($comment->user && $comment->user->role === 'admin_main' || $comment->user->role === 'admin_sub') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không thể xóa bình luận của Admin'
                    ], 403);
                }
                $comment->delete();
                
                if ($isPinned) {
                    $pinnedComments = Comment::with([
                        'user',
                        'approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.user',
                        'approvedReplies.reactions',
                        'approvedReplies.approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.approvedReplies.user',
                        'approvedReplies.approvedReplies.reactions',
                        'reactions'
                    ])
                        ->where('story_id', $storyId)
                        ->whereNull('reply_id')
                        ->where('is_pinned', true)
                        ->approved()
                        ->latest('pinned_at')
                        ->get();
                    
                    $regularComments = Comment::with([
                        'user',
                        'approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.user',
                        'approvedReplies.reactions',
                        'approvedReplies.approvedReplies' => function ($q) {
                            $q->approved()->latest();
                        },
                        'approvedReplies.approvedReplies.user',
                        'approvedReplies.approvedReplies.reactions',
                        'reactions'
                    ])
                        ->where('story_id', $storyId)
                        ->whereNull('reply_id')
                        ->where('is_pinned', false)
                        ->approved()
                        ->latest()
                        ->paginate(10);
                    
                    $html = view('components.comments-list', compact('pinnedComments', 'regularComments'))->render();
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Xóa bình luận thành công',
                        'isPinned' => true,
                        'html' => $html
                    ]);
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Xóa bình luận thành công'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Không có quyền thực hiện'
            ], 403);
        } catch (\Exception $e) {
           Log::error('Error deleting comment: ' . $e->getMessage());
            $stillExists = Comment::find($comment);
            if (!$stillExists) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bình luận đã được xóa'
                ]);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xóa bình luận'
            ], 500);
        }
    }

    public function storeClient(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để bình luận'
            ], 401);
        }

        $validated = $request->validate([
            'comment' => 'required|max:700',
            'story_id' => 'required|exists:stories,id',
            'reply_id' => 'nullable|exists:comments,id'
        ]);

        $user = auth()->user();
        
        if ($user->ban_comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản của bạn đã bị cấm bình luận'
            ], 403);
        }

        $story = Story::findOrFail($validated['story_id']);

        $parentComment = null;
        $level = 0;
        
        if (!empty($validated['reply_id'])) {
            $parentComment = Comment::where('story_id', $validated['story_id'])
                ->find($validated['reply_id']);

            if (!$parentComment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bình luận để trả lời'
                ], 404);
            }

            if ($parentComment->level >= 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể trả lời bình luận này'
                ], 403);
            }
            
            $level = $parentComment->level + 1;
        }
        
        $recentComments = Comment::where('user_id', $user->id)
            ->where('story_id', $validated['story_id'])
            ->where('comment', $validated['comment'])
            ->where('created_at', '>=', now()->subSeconds(30))
            ->exists();
        
        if ($recentComments) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bình luận của bạn đã được gửi, vui lòng không gửi lại.'
            ], 400);
        }

        try {
            $approvalStatus = 'pending';
            if ($user->role === 'admin_main' || $user->role === 'admin_sub') {
                $approvalStatus = 'approved';
            } elseif ($story->user_id === $user->id) {
                $approvalStatus = 'approved';
            }

            $comment = Comment::create([
                'user_id' => $user->id,
                'story_id' => $validated['story_id'],
                'comment' => $validated['comment'],
                'reply_id' => $validated['reply_id'] ?? null,
                'level' => $level,
                'approval_status' => $approvalStatus,
                'approved_at' => $approvalStatus === 'approved' ? now() : null,
                'approved_by' => $approvalStatus === 'approved' ? $user->id : null,
            ]);

            $comment->load([
                'user',
                'reactions',
                'approvedReplies' => function ($q) {
                    $q->approved()->latest();
                },
                'approvedReplies.user',
                'approvedReplies.reactions',
                'approvedReplies.approvedReplies' => function ($q) {
                    $q->approved()->latest();
                },
                'approvedReplies.approvedReplies.user',
                'approvedReplies.approvedReplies.reactions'
            ]);

        $pinnedComments = Comment::with([
            'user',
            'approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.user',
            'approvedReplies.reactions',
            'approvedReplies.approvedReplies' => function ($q) {
                $q->select('id', 'user_id', 'comment', 'reply_id', 'level', 'approval_status', 'created_at')
                    ->approved()
                    ->latest();
            },
            'approvedReplies.approvedReplies.user',
            'approvedReplies.approvedReplies.reactions',
            'reactions'
        ])
            ->where('story_id', $validated['story_id'])
            ->whereNull('reply_id')
            ->where('is_pinned', true)
            ->approved()
            ->latest('pinned_at')
            ->get();

            if (empty($validated['reply_id'])) {
                if ($approvalStatus === 'approved') {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Đã thêm bình luận',
                        'html' => view('components.comments-item', compact('comment'))->render(),
                        'isPinned' => false,
                        'pinnedComments' => $pinnedComments->count() > 0 ? view('components.comments-list', ['pinnedComments' => $pinnedComments])->render() : null
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Bình luận của bạn đã được gửi và đang chờ duyệt',
                        'html' => null,
                        'isPinned' => false,
                        'pinnedComments' => $pinnedComments->count() > 0 ? view('components.comments-list', ['pinnedComments' => $pinnedComments])->render() : null
                    ]);
                }
            } else {
                if ($approvalStatus === 'approved') {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Đã thêm bình luận',
                        'html' => view('components.comments-item', compact('comment'))->render()
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Bình luận của bạn đã được gửi và đang chờ duyệt',
                        'html' => null
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error saving comment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lưu bình luận'
            ], 500);
        }
    }
}
