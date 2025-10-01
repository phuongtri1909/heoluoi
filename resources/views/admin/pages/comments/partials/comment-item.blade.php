<div class="comment-card {{ isset($highlight) && $highlight ? 'border border-primary' : '' }}">
    <div class="comment-header">
        <div class="comment-user">
            @if($comment->user)
                <a href="{{ route('admin.users.show', $comment->user->id) }}">{{ $comment->user->name }}</a>
                <span class="badge bg-{{ $comment->user->role == 'admin_main' ? 'danger' : ($comment->user->role == 'admin_sub' ? 'warning' : 'info') }} text-white">
                    {{ ucfirst($comment->user->role) }}
                </span>
            @else
                <span class="text-muted">Người dùng không tồn tại</span>
            @endif
            
            @if($comment->story)
                <a href="{{ route('admin.stories.show', $comment->story->id) }}" class="badge bg-primary story-badge">
                    {{ Str::limit($comment->story->title, 30) }}
                </a>
            @endif
        </div>
        <div class="comment-meta">
            {{ $comment->created_at->format('d/m/Y H:i') }}
            @if($comment->approved_at && $comment->approver)
                <br><small class="text-muted">Duyệt bởi: {{ $comment->approver->name }} ({{ $comment->approved_at->format('d/m/Y H:i') }})</small>
            @endif
        </div>
    </div>
    
    <div class="comment-content">
        @php
            $content = $comment->comment;
            $search = request('search');
            if ($search && stripos($content, $search) !== false) {
                $content = preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark class="bg-warning">$1</mark>', $content);
                $highlight = true;
            }
        @endphp
        {!! $content !!}
    </div>
    
    <div class="comment-actions d-flex justify-content-between align-items-center">
        <div>
            @if($comment->is_pinned)
                <span class="badge bg-success">Đã ghim</span>
            @endif
            
            @if($comment->approval_status === 'pending')
                @if($comment->user && $comment->user->role === 'admin_main' || $comment->user->role === 'admin_sub')
                    <span class="badge bg-info">Admin</span>
                @elseif($comment->story && $comment->story->user_id === $comment->user_id)
                    <span class="badge bg-primary">Tác giả</span>
                @else
                    <span class="badge bg-warning">Chờ duyệt</span>
                @endif
            @elseif($comment->approval_status === 'approved')
                <span class="badge bg-success">Đã duyệt</span>
            @elseif($comment->approval_status === 'rejected')
                <span class="badge bg-danger">Đã từ chối</span>
            @endif
            
            @if($comment->replies && $comment->replies->count() > 0)
                @php
                    // Check if any replies match our search filters
                    $hasMatchingReply = false;
                    $search = request('search');
                    $userId = request('user');
                    $date = request('date');
                    
                    $pendingReplies = 0;
                    foreach ($comment->replies as $reply) {
                        if ($reply->approval_status === 'pending' && 
                            $reply->user && 
                            $reply->user->role !== 'admin_main' && 
                            $reply->user->role !== 'admin_sub' && 
                            $reply->story && 
                            $reply->story->user_id !== $reply->user_id) {
                            $pendingReplies++;
                        }
                        if ($reply->replies) {
                            foreach ($reply->replies as $nestedReply) {
                                if ($nestedReply->approval_status === 'pending' && 
                                    $nestedReply->user && 
                                    $nestedReply->user->role !== 'admin_main' && 
                                    $nestedReply->user->role !== 'admin_sub' && 
                                    $nestedReply->story && 
                                    $nestedReply->story->user_id !== $nestedReply->user_id) {
                                    $pendingReplies++;
                                }
                                // Count level 3 replies
                                if ($nestedReply->replies) {
                                    foreach ($nestedReply->replies as $level3Reply) {
                                        if ($level3Reply->approval_status === 'pending' && 
                                            $level3Reply->user && 
                                            $level3Reply->user->role !== 'admin_main' && 
                                            $level3Reply->user->role !== 'admin_sub' && 
                                            $level3Reply->story && 
                                            $level3Reply->story->user_id !== $level3Reply->user_id) {
                                            $pendingReplies++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($search || $userId || $date) {
                        foreach ($comment->replies as $reply) {
                            if ($search && stripos($reply->comment, $search) !== false) {
                                $hasMatchingReply = true;
                                break;
                            }
                            
                            if ($userId && $reply->user_id == $userId) {
                                $hasMatchingReply = true;
                                break;
                            }
                            
                            if ($date && $reply->created_at->format('Y-m-d') == $date) {
                                $hasMatchingReply = true;
                                break;
                            }
                        }
                    }
                @endphp
                <button class="btn btn-link btn-sm p-0 toggle-replies" data-comment-id="{{ $comment->id }}">
                    <i class="fa-solid fa-chevron-{{ $hasMatchingReply ? 'up' : 'down' }} me-1"></i>
                    {{ $hasMatchingReply ? 'Ẩn trả lời' : 'Xem trả lời' }} ({{ $comment->replies->count() }})
                    @if($pendingReplies > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $pendingReplies }} chờ duyệt</span>
                    @endif
                </button>
            @endif
        </div>
        
        <div class="d-flex gap-2">
            @if($comment->approval_status === 'pending' && 
                $comment->user && 
                $comment->user->role !== 'admin_main' && 
                $comment->user->role !== 'admin_sub' && 
                $comment->story && 
                $comment->story->user_id !== $comment->user_id)
                <button class="btn btn-link text-success btn-sm p-0 approve-comment-btn" data-comment-id="{{ $comment->id }}">
                    <i class="fa-solid fa-check"></i> Duyệt
                </button>
                <button class="btn btn-link text-danger btn-sm p-0 reject-comment-btn" data-comment-id="{{ $comment->id }}">
                    <i class="fa-solid fa-times"></i> Từ chối
                </button>
            @endif
            
            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link text-danger btn-sm p-0 delete-comment-btn">
                    <i class="fa-solid fa-trash"></i> Xóa
                </button>
            </form>
        </div>
    </div>
</div>

@if($comment->replies && $comment->replies->count() > 0)
    @php
        // Check if any replies match our search filters
        $hasMatchingReply = false;
        $search = request('search');
        $userId = request('user');
        $date = request('date');
        
        if ($search || $userId || $date) {
            foreach ($comment->replies as $reply) {
                if ($search && stripos($reply->comment, $search) !== false) {
                    $hasMatchingReply = true;
                    break;
                }
                
                if ($userId && $reply->user_id == $userId) {
                    $hasMatchingReply = true;
                    break;
                }
                
                if ($date && $reply->created_at->format('Y-m-d') == $date) {
                    $hasMatchingReply = true;
                    break;
                }
            }
        }
    @endphp
    <div class="comment-thread comment-level-{{ $level + 1 }} replies-container-{{ $comment->id }} {{ $hasMatchingReply ? '' : 'd-none' }}">
        @foreach($comment->replies as $reply)
            @php
                $highlight = false;
                if ($search && stripos($reply->comment, $search) !== false) {
                    $highlight = true;
                }
                
                if ($userId && $reply->user_id == $userId) {
                    $highlight = true;
                }
                
                if ($date && $reply->created_at->format('Y-m-d') == $date) {
                    $highlight = true;
                }
            @endphp
            @include('admin.pages.comments.partials.comment-item', ['comment' => $reply, 'level' => $level + 1, 'highlight' => $highlight])
        @endforeach
    </div>
@endif 