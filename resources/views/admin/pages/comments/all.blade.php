@extends('admin.layouts.app')

@push('styles-admin')
<style>
    .comment-thread {
        padding-left: 20px;
        border-left: 2px solid #e9ecef;
        margin-top: 10px;
    }
    .comment-level-1 { border-left-color: #5e72e4; }
    .comment-level-2 { border-left-color: #11cdef; }
    .comment-level-3 { border-left-color: #fb6340; }
    
    .comment-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    
    .comment-card.border-primary {
        box-shadow: 0 0 0 1px #5e72e4;
        background-color: #f8fbff;
    }
    
    mark {
        padding: 0 2px;
        border-radius: 2px;
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .comment-user {
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .comment-meta {
        font-size: 0.75rem;
        color: #8898aa;
    }
    
    .comment-content {
        font-size: 0.875rem;
        margin-bottom: 5px;
    }
    
    .comment-actions {
        font-size: 0.75rem;
    }
    
    .story-badge {
        font-size: 0.7rem;
        padding: 3px 6px;
        margin-left: 5px;
    }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex flex-row justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-0">Quản lý tất cả bình luận</h5>
                        <p class="text-sm mb-0">
                            Tổng số: {{ $totalComments }} bình luận
                            @if(isset($pendingCommentsCount) && $pendingCommentsCount > 0)
                                | <span class="text-warning">Chờ duyệt: {{ $pendingCommentsCount }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <form method="GET" class="mt-3 d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                        <div class="d-flex flex-column flex-md-row gap-2 mb-2 mb-md-0 w-100">
                            <select name="story" class="form-select form-select-sm">
                                <option value="">Tất cả truyện</option>
                                @foreach($stories as $story)
                                    <option value="{{ $story->id }}" {{ request('story') == $story->id ? 'selected' : '' }}>
                                        {{ $story->title }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <select name="user" class="form-select form-select-sm">
                                <option value="">Tất cả người dùng</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <input type="date" name="date" 
                                   class="form-control form-control-sm" 
                                   value="{{ request('date') }}">
                                   
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" 
                                       name="search" placeholder="Nội dung..." 
                                       value="{{ request('search') }}">
                                <button class="btn bg-gradient-primary btn-sm px-2 mb-0" type="submit">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('admin.comments.all') }}" class="btn btn-outline-secondary btn-sm px-2 mb-0">
                                    <i class="fa-solid fa-rotate"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body px-0 pt-2 pb-2">
                

                <div class="px-4">
                    @if($comments->count() > 0)
                        @foreach($comments as $comment)
                            @include('admin.pages.comments.partials.comment-item', ['comment' => $comment, 'level' => 0])
                        @endforeach
                        
                        <div class="mt-4">
                            <x-pagination :paginator="$comments" />
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            Không có bình luận nào phù hợp với tiêu chí tìm kiếm
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts-admin')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle visibility of replies
        document.querySelectorAll('.toggle-replies').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                const repliesContainer = document.querySelector(`.replies-container-${commentId}`);
                
                if (repliesContainer) {
                    if (repliesContainer.classList.contains('d-none')) {
                        repliesContainer.classList.remove('d-none');
                        const originalText = this.innerHTML;
                        const pendingBadge = originalText.match(/<span class="badge[^>]*>.*?<\/span>/);
                        const pendingBadgeHtml = pendingBadge ? pendingBadge[0] : '';
                        this.innerHTML = '<i class="fa-solid fa-chevron-up me-1"></i>Ẩn trả lời ' + pendingBadgeHtml;
                    } else {
                        repliesContainer.classList.add('d-none');
                        const originalText = this.innerHTML;
                        const pendingBadge = originalText.match(/<span class="badge[^>]*>.*?<\/span>/);
                        const pendingBadgeHtml = pendingBadge ? pendingBadge[0] : '';
                        this.innerHTML = '<i class="fa-solid fa-chevron-down me-1"></i>Xem trả lời ' + pendingBadgeHtml;
                    }
                }
            });
        });
        
        // Confirm delete
        document.querySelectorAll('.delete-comment-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Approve comment (works for both main comments and replies)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.approve-comment-btn')) {
                e.preventDefault();
                const button = e.target.closest('.approve-comment-btn');
                const commentId = button.dataset.commentId;
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                
                if (!csrfToken) {
                    showToast('Không tìm thấy CSRF token', 'error');
                    return;
                }
                
                if (confirm('Bạn có chắc chắn muốn duyệt bình luận này?')) {
                    fetch(`/admin/comments/${commentId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            showToast('Đã duyệt bình luận thành công', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast('Có lỗi xảy ra: ' + (data.message || 'Unknown error'),'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Có lỗi xảy ra khi duyệt bình luận: ' + error.message,'error');
                    });
                }
            }
        });
        
        // Reject comment (works for both main comments and replies)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.reject-comment-btn')) {
                e.preventDefault();
                const button = e.target.closest('.reject-comment-btn');
                const commentId = button.dataset.commentId;
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                
                if (!csrfToken) {
                    showToast('Không tìm thấy CSRF token', 'error');
                    return;
                }
                
                if (confirm('Bạn có chắc chắn muốn từ chối bình luận này?')) {
                    fetch(`/admin/comments/${commentId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            showToast('Đã từ chối bình luận thành công', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast('Có lỗi xảy ra: ' + (data.message || 'Unknown error'),'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                       showToast('Có lỗi xảy ra khi từ chối bình luận: ' + error.message,'error');
                    });
                }
            }
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        let alertClass = 'alert-success';
        let icon = '<i class="fas fa-check-circle me-2"></i>';

        if (type === 'error') {
            alertClass = 'alert-danger';
            icon = '<i class="fas fa-exclamation-circle me-2"></i>';
        } else if (type === 'warning') {
            alertClass = 'alert-warning';
            icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
        } else if (type === 'info') {
            alertClass = 'alert-info';
            icon = '<i class="fas fa-info-circle me-2"></i>';
        }

        const toast = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
            <div class="toast show align-items-center ${alertClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${icon} ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        `;

        const existingToasts = document.querySelectorAll('.toast.show');
        existingToasts.forEach(toast => {
            toast.parentElement.remove();
        });

        document.body.insertAdjacentHTML('beforeend', toast);

        setTimeout(() => {
            const toastElement = document.querySelector('.toast.show');
            if (toastElement) {
                toastElement.remove();
            }
        }, 3000);
    }
</script>
@endpush 