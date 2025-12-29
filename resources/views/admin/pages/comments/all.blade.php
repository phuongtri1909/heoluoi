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
    
    .comment-checkbox,
    #select-all-comments {
        cursor: pointer;
        width: 18px;
        height: 18px;
        border: 2px solid #6c757d;
        border-radius: 3px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        position: relative;
        flex-shrink: 0;
    }
    
    .comment-checkbox:checked,
    #select-all-comments:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .comment-checkbox:checked::after,
    #select-all-comments:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }
    
    .comment-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.5;
        background-color: #e9ecef;
        border-color: #ced4da;
    }
    
    .comment-checkbox:hover:not(:disabled),
    #select-all-comments:hover {
        border-color: #0d6efd;
    }
    
    #select-all-comments:indeterminate {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    #select-all-comments:indeterminate::after {
        content: '−';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 14px;
        font-weight: bold;
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
                            
                            <select name="approval_status" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
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
                        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" id="select-all-comments" class="form-check-input">
                                <label for="select-all-comments" class="form-check-label mb-0">Chọn tất cả</label>
                                <span class="text-muted ms-2" id="selected-count">Đã chọn: 0</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success btn-sm" id="approve-batch-btn" disabled>
                                    <i class="fa-solid fa-check"></i> Duyệt hàng loạt
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="reject-batch-btn" disabled>
                                    <i class="fa-solid fa-times"></i> Từ chối hàng loạt
                                </button>
                            </div>
                        </div>
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
                e.preventDefault();
                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: 'Bạn có chắc chắn muốn xóa bình luận này?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
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
                
                Swal.fire({
                    title: 'Xác nhận duyệt',
                    text: 'Bạn có chắc chắn muốn duyệt bình luận này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Duyệt',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                });
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
                
                Swal.fire({
                    title: 'Xác nhận từ chối',
                    text: 'Bạn có chắc chắn muốn từ chối bình luận này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Từ chối',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                });
            }
        });
        
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('select-all-comments');
        const commentCheckboxes = document.querySelectorAll('.comment-checkbox:not(:disabled)');
        const approveBatchBtn = document.getElementById('approve-batch-btn');
        const rejectBatchBtn = document.getElementById('reject-batch-btn');
        const selectedCountSpan = document.getElementById('selected-count');
        
        function updateSelectedCount() {
            const selected = document.querySelectorAll('.comment-checkbox:checked:not(:disabled)').length;
            selectedCountSpan.textContent = `Đã chọn: ${selected}`;
            approveBatchBtn.disabled = selected === 0;
            rejectBatchBtn.disabled = selected === 0;
            
            // Update select all checkbox state
            if (selected === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (selected === commentCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
        
        selectAllCheckbox.addEventListener('change', function() {
            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
        
        commentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        // Approve batch
        approveBatchBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.comment-checkbox:checked:not(:disabled)'))
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                showToast('Vui lòng chọn ít nhất một bình luận', 'warning');
                return;
            }
            
            Swal.fire({
                title: 'Xác nhận duyệt hàng loạt',
                text: `Bạn có chắc chắn muốn duyệt ${selectedIds.length} bình luận đã chọn?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Duyệt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    showToast('Không tìm thấy CSRF token', 'error');
                    return;
                }
                
                approveBatchBtn.disabled = true;
                approveBatchBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
                
                fetch('/admin/comments/batch-approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ comment_ids: selectedIds })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showToast(`Đã duyệt thành công ${data.approved_count} bình luận`, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('Có lỗi xảy ra: ' + (data.message || 'Unknown error'), 'error');
                        approveBatchBtn.disabled = false;
                        approveBatchBtn.innerHTML = '<i class="fa-solid fa-check"></i> Duyệt hàng loạt';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra khi duyệt bình luận: ' + error.message, 'error');
                    approveBatchBtn.disabled = false;
                    approveBatchBtn.innerHTML = '<i class="fa-solid fa-check"></i> Duyệt hàng loạt';
                });
            });
        });
        
        // Reject batch
        rejectBatchBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.comment-checkbox:checked:not(:disabled)'))
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                showToast('Vui lòng chọn ít nhất một bình luận', 'warning');
                return;
            }
            
            Swal.fire({
                title: 'Xác nhận từ chối hàng loạt',
                text: `Bạn có chắc chắn muốn từ chối ${selectedIds.length} bình luận đã chọn?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Từ chối',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    showToast('Không tìm thấy CSRF token', 'error');
                    return;
                }
                
                rejectBatchBtn.disabled = true;
                rejectBatchBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
                
                fetch('/admin/comments/batch-reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                    body: JSON.stringify({ comment_ids: selectedIds })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showToast(`Đã từ chối thành công ${data.rejected_count} bình luận`, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('Có lỗi xảy ra: ' + (data.message || 'Unknown error'), 'error');
                        rejectBatchBtn.disabled = false;
                        rejectBatchBtn.innerHTML = '<i class="fa-solid fa-times"></i> Từ chối hàng loạt';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra khi từ chối bình luận: ' + error.message, 'error');
                    rejectBatchBtn.disabled = false;
                    rejectBatchBtn.innerHTML = '<i class="fa-solid fa-times"></i> Từ chối hàng loạt';
                });
            });
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