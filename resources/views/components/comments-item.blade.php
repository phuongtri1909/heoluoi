@php
    $level = $comment->level ?? 0;
    $isPinned = $comment->is_pinned ?? false;
@endphp

<li class="clearfix d-flex {{ $isPinned ? 'pinned-comment' : '' }} animate__animated animate__fadeIn" id="comment-{{ $comment->id }}">
    <img src="{{ $comment->user && $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : asset('images/defaults/avatar_default.jpg') }}"
        class="{{ $level > 0 ? 'avatar-reply' : 'avatar' }}"
        alt="{{ $comment->user ? $comment->user->name : 'Người dùng không tồn tại' }}">
    <div class="post-comments p-2 p-md-3">
        <div class="content-post-comments">
            <p class="meta mb-2">
                <a class="fw-bold ms-2 text-decoration-none" target="_blank">
                    @if ($comment->user)
                        @if ($comment->user->role === 'admin')
                            <span class="role-badge admin-badge">
                                @if (auth()->check() && auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.users.show', $comment->user->id) }}" target="_blank"
                                        class="text-decoration-none admin-badge">
                                        <i class="fas fa-crown"></i> {{ $comment->user->name }}
                                    </a>
                                @else
                                    <i class="fas fa-crown"></i> {{ $comment->user->name }}
                                @endif
                            </span>
                        @elseif($comment->user->role === 'mod')
                            <span class="role-badge mod-badge">
                                @if (auth()->check() && auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.users.show', $comment->user->id) }}" target="_blank"
                                        class="text-decoration-none mod-badge">
                                        <i class="fas fa-shield-alt"></i> {{ $comment->user->name }}
                                    </a>
                                @else
                                    <i class="fas fa-shield-alt"></i> {{ $comment->user->name }}
                                @endif
                            </span>
                        @else
                            @if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'mod'))
                                <a href="{{ route('admin.users.show', $comment->user->id) }}" target="_blank"
                                    class="text-decoration-none text-dark">
                                    {{ $comment->user->name }}
                                </a>
                            @else
                                <span class="text-dark">{{ $comment->user->name }}</span>
                            @endif
                        @endif
                    @else
                        <span>Người dùng không tồn tại</span>
                    @endif
                </a>

                @if ($isPinned)
                    <span class="pinned-badge ms-2">
                        <i class="fas fa-thumbtack"></i> Đã ghim
                    </span>
                @endif

                @if ($level < 2 && auth()->check())
                    <span class="pull-right">
                        <small class="reply-btn text-primary" style="cursor: pointer;"
                            data-id="{{ $comment->id }}">
                            <i class="far fa-comment-dots me-1"></i> Trả lời
                        </small>
                    </span>
                @endif

                @if (auth()->check())
                    @if (auth()->user()->role === 'admin' ||
                            (auth()->user()->role === 'mod' && $comment->user && in_array($comment->user->role, ['user', 'vip'])))
                        <span class="delete-comment text-danger ms-2" style="cursor: pointer;"
                            data-id="{{ $comment->id }}">
                            <i class="fas fa-times"></i>
                        </span>
                    @endif

                    @if ($level == 0 && auth()->user()->role === 'admin')
                        <button class="btn btn-sm pin-btn pin-comment ms-2" data-id="{{ $comment->id }}">
                            @if ($isPinned)
                                <i class="fas fa-thumbtack text-warning" title="Bỏ ghim"></i>
                            @else
                                <i class="fas fa-thumbtack" title="Ghim"></i>
                            @endif
                        </button>
                    @endif
                @endif
            </p>

            <p class="comment-content mb-2">{{ $comment->comment }}</p>

            <div class="d-flex align-items-center gap-2 comment-actions">
                <span class="text-muted small comment-time">
                    <i class="far fa-clock me-1"></i>{{ $comment->created_at->locale('vi')->diffForHumans() }}
                </span>

                @php
                    $userLiked = auth()->check()
                        ? $comment->reactions
                            ->where('user_id', auth()->id())
                            ->where('type', 'like')
                            ->first()
                        : null;
                    $userDisliked = auth()->check()
                        ? $comment->reactions
                            ->where('user_id', auth()->id())
                            ->where('type', 'dislike')
                            ->first()
                        : null;
                    $likesCount = $comment->reactions->where('type', 'like')->count();
                    $dislikesCount = $comment->reactions->where('type', 'dislike')->count();
                @endphp

                <button class="btn btn-sm btn-outline-primary reaction-btn {{ $userLiked ? 'active' : '' }}"
                    data-type="like" data-id="{{ $comment->id }}">
                    <i class="fas fa-thumbs-up"></i>
                    <span class="likes-count">{{ $likesCount }}</span>
                </button>

                <button class="btn btn-sm btn-outline-danger reaction-btn {{ $userDisliked ? 'active' : '' }}"
                    data-type="dislike" data-id="{{ $comment->id }}">
                    <i class="fas fa-thumbs-down"></i>
                    <span class="dislikes-count">{{ $dislikesCount }}</span>
                </button>
            </div>
        </div>

        @if ($comment->approvedReplies && $comment->approvedReplies->count() > 0)
            <ul class="comments mt-3">
                @foreach ($comment->approvedReplies as $reply)
                    @include('components.comments-item', ['comment' => $reply])
                @endforeach
            </ul>
        @endif
    </div>
</li>

@once
    @push('styles')
        <style>
            .role-badge {
                font-weight: bold;
                padding: 0 3px;
                transition: all 0.3s ease;
            }

            .admin-badge {
                color: #dc3545;
            }

            .mod-badge {
                color: #198754;
            }

            .vip-badge {
                color: #0d6efd;
            }
            
            .comment-content {
                font-size: 14px;
                line-height: 1.5;
                word-break: break-word;
            }
            
            .comment-time {
                font-size: 12px;
                color: #777;
            }
            
            .comment-actions {
                transition: all 0.3s ease;
            }
            
            .pin-btn {
                background: transparent;
                border: none;
                padding: 0;
                margin: 0;
                color: #777;
                transition: all 0.3s ease;
            }
            
            .pin-btn:hover {
                color: #ffc107;
                transform: rotate(45deg);
            }
            
            .delete-comment {
                opacity: 0.7;
                transition: all 0.3s ease;
            }
            
            .delete-comment:hover {
                opacity: 1;
            }
            
            .reply-btn {
                opacity: 0.8;
                transition: all 0.3s ease;
            }
            
            .reply-btn:hover {
                opacity: 1;
                text-decoration: underline !important;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .pinned-badge {
                animation: pulse 2s infinite;
            }

            .clickable-name {
                cursor: pointer;
                text-decoration: underline;
            }

            .clickable-name:hover {
                opacity: 0.8;
            }
        </style>
    @endpush
@endonce
