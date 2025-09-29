<section id="comments">
    <div class="container px-2 px-md-3">
        <div class="section-title d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div class="title-container mb-2">
                <i class="fa-regular fa-comments fa-xl color-2"></i>
                <h5 class="fw-bold ms-2 d-inline mb-0">BÌNH LUẬN TRUYỆN</h5>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="comment-form-container">
                    <div class="form-floating submit-comment animate__animated animate__fadeIn">
                        <textarea class="form-control" id="comment-input" placeholder="Nhập bình luận..." rows="2" maxlength="700"></textarea>
                        <label for="comment-input">Bình luận</label>
                        <button class="btn btn-sm btn-primary btn-send-comment" id="btn-comment">
                            <i class="fa-regular fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <div class="blog-comment">
                    <ul class="comments mb-0" id="comments-list">
                        @include('components.comments-list', [
                            'pinnedComments' => $pinnedComments,
                            'regularComments' => $regularComments,
                        ])
                    </ul>
                </div>

                @if ($regularComments->hasMorePages())
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary btn-sm load-more-button" id="load-more-comments">
                            <span>Xem thêm bình luận</span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>


@once
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            (function() {
                // Wait for document ready
                $(document).ready(function() {
                    // Ensure we're not attaching multiple event handlers
                    $('#btn-comment').off('click');
                    $('#comment-input').off('keydown');
                    $(document).off('click', '.reply-btn');
                    $(document).off('click', '.cancel-reply');
                    $(document).off('click', '.submit-reply');
                    $(document).off('click', '.reaction-btn');
                    $(document).off('click', '.pin-comment');
                    $(document).off('click', '.delete-comment');
                    $('#confirmDelete').off('click');

                    let page = 1;
                    let isSubmitting = false;
                    let lastSubmitTime = 0;

                    // Modal initialization for comment deletion
                    let deleteModal;
                    const modalElement = document.getElementById('deleteModal');
                    if (modalElement) {
                        deleteModal = new bootstrap.Modal(modalElement);
                    }

                    // Direct comment submission with debounce
                    $('#btn-comment').on('click', function(e) {
                        e.preventDefault(); // Prevent any default action
                        console.log('click');
                        
                        const btn = $(this);
                        const comment = $('#comment-input').val().trim();
                        
                        // Prevent empty comments and double submissions
                        if (!comment || isSubmitting) return;
                        
                        // Debounce: prevent rapid multiple clicks
                        const now = Date.now();
                        if (now - lastSubmitTime < 1000) return; // 1 second cooldown
                        lastSubmitTime = now;

                        // Disable button and show loading
                        isSubmitting = true;
                        btn.prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i>');

                        $.ajax({
                            url: '{{ route('comment.store.client') }}',
                            type: 'POST',
                            data: {
                                comment: comment,
                                story_id: '{{ $story->id }}',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    // Clear the input field
                                    $('#comment-input').val('');
                                    
                                    // If we have pinned comments, update the pinned section first
                                    if (res.pinnedComments) {
                                        // Remove existing pinned section
                                        $('.pinned-comments').remove();
                                        $('.regular-comments-header').remove();
                                        
                                        // Add the updated pinned comments at the top
                                        $('#comments-list').prepend(res.pinnedComments);
                                    }
                                    
                                    
                                    showToast(res.message, 'success');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 401) {
                                    window.location.href = '{{ route('login') }}';
                                } else if (xhr.status === 400) {
                                    // Duplicate comment
                                    showToast(xhr.responseJSON.message ||
                                        'Bình luận này đã được gửi trước đó', 'warning');
                                } else {
                                    showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                                }
                            },
                            complete: function() {
                                // Re-enable button and restore original state after a short delay
                                setTimeout(() => {
                                    isSubmitting = false;
                                    btn.prop('disabled', false)
                                        .html('<i class="fa-regular fa-paper-plane"></i>');
                                }, 500);
                            }
                        });
                    });

                    // Also handle Enter key in textarea to submit
                    $('#comment-input').on('keydown', function(e) {
                        // Submit on Enter without Shift key
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            $('#btn-comment').trigger('click');
                        }
                    });

                    // Load more comments
                    $('#load-more-comments').on('click', function() {
                        const btn = $(this);
                        btn.html('<i class="fas fa-spinner fa-spin"></i> Đang tải...');

                        page++;
                        $.ajax({
                            url: '{{ route('comments.load', $story->id) }}',
                            data: {
                                page: page
                            },
                            success: function(res) {
                                // Add with animation
                                const newComments = $(res.html).hide();
                                $('#comments-list').append(newComments);
                                newComments.slideDown(300);

                                if (!res.hasMore) {
                                    $('#load-more-comments').fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                } else {
                                    btn.html(
                                        '<span>Xem thêm bình luận</span><i class="fas fa-chevron-down ms-1"></i>'
                                        );
                                }

                                // Make sure event handlers are attached to new elements
                                bindCommentEvents();
                            },
                            error: function(xhr) {
                                showToast('Có lỗi xảy ra khi tải bình luận', 'error');
                                btn.html('<span>Thử lại</span><i class="fas fa-redo ms-1"></i>');
                            }
                        });
                    });

                    // Always use document for event delegation
                    // Reply button click
                    $(document).on('click', '.reply-btn', function(e) {
                        e.preventDefault();
                        const commentId = $(this).data('id');

                        // Remove any existing reply forms first
                        $('.reply-form').slideUp(200, function() {
                            $(this).remove();
                        });
                        $('.reply-btn').show();

                        const replyForm = `
                        <div class="reply-form mt-2 animate__animated animate__fadeIn">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Nhập trả lời..." maxlength="700"></textarea>
                                <label>Trả lời</label>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <button class="btn btn-sm btn-light cancel-reply">Hủy</button>
                                <button class="btn btn-sm btn-primary submit-reply" data-id="${commentId}">Gửi</button>
                            </div>
                        </div>
                    `;
                        $(this).closest('.post-comments').append(replyForm);
                        $(this).hide();

                        // Focus on the textarea
                        setTimeout(() => {
                            $(this).closest('.post-comments').find('.reply-form textarea').focus();
                        }, 100);
                    });

                    // Cancel reply
                    $(document).on('click', '.cancel-reply', function() {
                        const replyForm = $(this).closest('.reply-form');
                        const replyBtn = replyForm.closest('.post-comments').find('.reply-btn');
                        replyForm.slideUp(200, function() {
                            $(this).remove();
                            replyBtn.show();
                        });
                    });

                    // Submit reply with debounce
                    $(document).on('click', '.submit-reply', function(e) {
                        e.preventDefault();
                        
                        const btn = $(this);
                        const commentId = btn.data('id');
                        const reply = btn.closest('.reply-form').find('textarea').val().trim();

                        if (!reply || btn.prop('disabled')) return;
                        
                        // Debounce: prevent rapid multiple clicks
                        const now = Date.now();
                        if (now - lastSubmitTime < 1000) return; // 1 second cooldown
                        lastSubmitTime = now;

                        // Disable button and show loading
                        btn.prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i>');

                        $.ajax({
                            url: '{{ route('comment.store.client') }}',
                            type: 'POST',
                            data: {
                                comment: reply,
                                reply_id: commentId,
                                story_id: '{{ $story->id }}',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    // Only show reply if it's approved (has HTML)
                                    if (res.html) {
                                        let replyContainer = btn.closest('.post-comments').find(
                                            'ul.comments');

                                        // Create replies container if it doesn't exist
                                        if (replyContainer.length === 0) {
                                            btn.closest('.post-comments').append(
                                                '<ul class="comments mt-3"></ul>');
                                            replyContainer = btn.closest('.post-comments').find(
                                                'ul.comments');
                                        }

                                        // Add with animation
                                        const newReply = $(res.html).hide();
                                        replyContainer.append(newReply);
                                        newReply.slideDown(300);
                                        
                                        // Bind events to the new reply
                                        bindCommentEvents();
                                    }
                                    
                                    btn.closest('.reply-form').slideUp(200, function() {
                                        $(this).remove();
                                    });

                                    // Re-enable reply button
                                    btn.closest('.post-comments').find('.reply-btn').show();
                                    showToast(res.message, 'success');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 400) {
                                    // Duplicate comment
                                    showToast(xhr.responseJSON.message ||
                                        'Bình luận này đã được gửi trước đó', 'warning');
                                } else {
                                    showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                                }
                                // Re-enable button on error after a short delay
                                setTimeout(() => {
                                    btn.prop('disabled', false).text('Gửi');
                                }, 500);
                            }
                        });
                    });

                    // Reaction button (like/dislike)
                    $(document).on('click', '.reaction-btn', function() {
                        const btn = $(this);
                        const commentId = btn.data('id');
                        const type = btn.data('type');

                        // Visual feedback
                        btn.addClass('animate__animated animate__pulse');

                        $.ajax({
                            url: `/comments/${commentId}/react`,
                            type: 'POST',
                            data: {
                                type: type,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Update count with animation
                                    const countElement = btn.find(type === 'like' ? '.likes-count' :
                                        '.dislikes-count');
                                    const currentCount = parseInt(countElement.text());
                                    const newCount = response[type + 's'];

                                    if (currentCount !== newCount) {
                                        countElement.addClass('animate__animated animate__bounceIn');
                                        setTimeout(() => {
                                            countElement.removeClass(
                                                'animate__animated animate__bounceIn');
                                        }, 500);
                                    }

                                    countElement.text(newCount);
                                    btn.toggleClass('active');

                                    // If user likes and already disliked, or vice versa
                                    const otherType = type === 'like' ? 'dislike' : 'like';
                                    const otherBtn = btn.siblings(`[data-type="${otherType}"]`);
                                    if (btn.hasClass('active') && otherBtn.hasClass('active')) {
                                        otherBtn.removeClass('active');
                                        otherBtn.find(`.${otherType}s-count`).text(response[otherType +
                                            's']);
                                    }

                                    showToast(response.message, 'success');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 401) {
                                    window.location.href = '{{ route('login') }}';
                                } else {
                                    showToast('Có lỗi xảy ra khi thực hiện phản ứng', 'error');
                                }
                            },
                            complete: function() {
                                setTimeout(() => {
                                    btn.removeClass('animate__animated animate__pulse');
                                }, 500);
                            }
                        });
                    });

                    // Pin comment
                    $(document).on('click', '.pin-comment', function() {
                        const btn = $(this);
                        const commentId = btn.data('id');

                        if (btn.prop('disabled')) return;

                        btn.prop('disabled', true)
                            .find('i').addClass('fa-spin');

                        $.ajax({
                            url: `/comments/${commentId}/pin`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    // Update list with fade effect
                                    $('#comments-list').fadeOut(200, function() {
                                        $(this).html(res.html).fadeIn(200);
                                        bindCommentEvents();
                                    });
                                    showToast(res.message, 'success');
                                }
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                            },
                            complete: function() {
                                btn.prop('disabled', false)
                                    .find('i').removeClass('fa-spin');
                            }
                        });
                    });

                    // Delete comment
                    $(document).on('click', '.delete-comment', function() {
                        const commentId = $(this).data('id');

                        // Set the comment ID for the confirm button
                        $('#confirmDelete').data('id', commentId);

                        // Show modal
                        if (modalElement) {
                            deleteModal.show();
                        }
                    });

                    // Handle comment deletion confirmation
                    $('#confirmDelete').on('click', function() {
                        const btn = $(this);
                        const commentId = btn.data('id');

                        if (!commentId) return;

                        btn.prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin me-1"></i> Đang xóa...');

                        $.ajax({
                            url: '{{ route('delete.comments', ':commentId') }}'.replace(':commentId', commentId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // If it was a pinned comment, refresh the entire comments list
                                    if (response.isPinned) {
                                        $('#comments-list').fadeOut(200, function() {
                                            $(this).html(response.html).fadeIn(200);
                                            bindCommentEvents();
                                        });
                                    } else {
                                        // Remove the comment with animation
                                        const commentElement = $(`#comment-${commentId}`);
                                        commentElement.addClass('animate__animated animate__fadeOutRight');

                                        // Wait for animation to complete then remove the element
                                        setTimeout(() => {
                                            commentElement.remove();
                                        }, 500);
                                    }

                                    showToast('Đã xóa bình luận thành công', 'success');
                                }
                            },
                            error: function(xhr) {
                                // Even if there's an error response but status is 200 or 204, treat as success
                                if (xhr.status === 200 || xhr.status === 204) {
                                    // Remove the comment with animation
                                    const commentElement = $(`#comment-${commentId}`);
                                    commentElement.addClass('animate__animated animate__fadeOutRight');

                                    // Wait for animation to complete then remove the element
                                    setTimeout(() => {
                                        commentElement.remove();
                                    }, 500);

                                    showToast('Đã xóa bình luận thành công', 'success');
                                } else {
                                    showToast(xhr.responseJSON ? xhr.responseJSON.message :
                                        'Có lỗi xảy ra khi xóa bình luận', 'error');
                                }
                            },
                            complete: function() {
                                btn.prop('disabled', false)
                                    .html('Xóa');
                                if (modalElement) {
                                    deleteModal.hide();
                                }
                            }
                        });
                    });

                    // Function to bind all events to comments
                    function bindCommentEvents() {
                        // Events will be handled by document-level event delegation
                        // So there's no need to re-bind them here
                    }

                    // Initial binding
                    bindCommentEvents();
                });

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
                    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                        <div class="toast show align-items-center ${alertClass} border-0 animate__animated animate__fadeInUp" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    ${icon} ${message}
                                </div>
                                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                `;

                    // Remove any existing toasts first
                    const existingToasts = document.querySelectorAll('.toast.show');
                    existingToasts.forEach(toast => {
                        toast.parentElement.remove();
                    });

                    document.body.insertAdjacentHTML('beforeend', toast);

                    setTimeout(() => {
                        const toastElement = document.querySelector('.toast.show');
                        if (toastElement) {
                            toastElement.classList.remove('animate__fadeInUp');
                            toastElement.classList.add('animate__fadeOutDown');
                            setTimeout(() => {
                                if (toastElement.parentElement) {
                                    toastElement.parentElement.remove();
                                }
                            }, 500);
                        }
                    }, 3000);
                }
            })();
        </script>
    @endpush

    @push('styles')
        <style>
            /* Comment Section Styling */
            #comments {
                position: relative;
            }

            .section-title {
                font-weight: 600;
                position: relative;
                display: inline-block;
                padding-bottom: 5px;
            }

            .section-title:after {
                content: '';
                position: absolute;
                left: 0;
                bottom: 0;
                width: 50px;
                height: 2px;
                background: var(--primary-color-3);
            }

            .comment-form-container {
                margin-bottom: 25px;
                transition: all 0.3s ease;
            }

            .submit-comment {
                position: relative;
                border-radius: 10px;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .submit-comment:focus-within {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .submit-comment textarea {
                border-radius: 10px;
                min-height: 60px;
                border: 1px solid #e0e0e0;
                transition: all 0.3s ease;
                padding-right: 40px;
            }

            .submit-comment textarea:focus {
                border-color: #80bdff;
            }

            .btn-send-comment {
                position: absolute;
                right: 12px;
                bottom: 8px;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .btn-send-comment:hover {
                transform: translateY(-2px);
            }

            /* Comment Layout */
            .blog-comment ul.comments ul {
                position: relative;
                margin-left: 25px;
            }

            .blog-comment ul.comments ul:before {
                content: '';
                position: absolute;
                left: -15px;
                top: 0;
                height: 100%;
                border-left: 2px solid #eee;
            }

            .blog-comment ul.comments ul li:before {
                content: '';
                position: absolute;
                left: -15px;
                top: 20px;
                width: 15px;
                border-top: 2px solid #eee;
            }

            .blog-comment ul.comments ul li {
                position: relative;
            }

            /* Comment Styling */
            .blog-comment::before,
            .blog-comment::after,
            .blog-comment-form::before,
            .blog-comment-form::after {
                content: "";
                display: table;
                clear: both;
            }

            .blog-comment ul {
                list-style-type: none;
                padding: 0;
            }

            .blog-comment img {
                opacity: 1;
                filter: Alpha(opacity=100);
                border-radius: 4px;
            }

            .blog-comment img.avatar {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .blog-comment img.avatar-reply {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .blog-comment img.avatar:hover,
            .blog-comment img.avatar-reply:hover {
                transform: scale(1.05);
            }

            .blog-comment .post-comments {
                margin-bottom: 15px;
                position: relative;
                width: 100%;
                transition: all 0.3s ease;
            }

            .blog-comment .post-comments .content-post-comments {
                background: #fff;
                border: 1px solid #eee;
                border-radius: 15px;
                padding: 12px 15px;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            }

            .blog-comment .post-comments .content-post-comments:hover {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }

            .blog-comment .meta {
                font-size: 13px;
                color: #777;
                padding-bottom: 8px;
                margin-bottom: 10px !important;
                border-bottom: 1px solid #f0f0f0;
            }

            /* Reaction buttons */
            .reaction-btn {
                padding: 5px 10px;
                font-size: 12px;
                border-radius: 20px;
                transition: all 0.3s ease;
                background-color: transparent;
            }

            .reaction-btn:hover {
                transform: translateY(-2px);
            }

            .reaction-btn.active {
                background-color: #f0f0f0;
                font-weight: bold;
            }

            /* Reply Form */
            .reply-form {
                margin: 10px 0;
                border-radius: 10px;
                padding: 10px;
                background-color: #f9f9f9;
                border-left: 3px solid #007bff;
            }

            .reply-form .form-floating textarea {
                border-radius: 8px;
                min-height: 60px;
            }

            /* Load More Button */
            .load-more-button {
                padding: 8px 20px;
                border-radius: 20px;
                transition: all 0.3s ease;
            }

            .load-more-button:hover {
                transform: translateY(-2px);
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                .blog-comment ul.comments ul {
                    margin-left: 15px;
                }

                .blog-comment ul.comments ul:before {
                    left: -10px;
                }

                .blog-comment ul.comments ul li:before {
                    left: -10px;
                    width: 10px;
                }

                .blog-comment img.avatar {
                    width: 40px;
                    height: 40px;
                }

                .blog-comment img.avatar-reply {
                    width: 30px;
                    height: 30px;
                }

                .blog-comment .post-comments {
                    padding: 5px !important;
                }

                .blog-comment .post-comments .content-post-comments {
                    padding: 10px;
                }

                .reaction-btn {
                    padding: 3px 8px;
                }

                .btn-send-comment {
                    bottom: 6px;
                    width: 32px;
                    height: 32px;
                }

                .meta {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 5px;
                    align-items: center;
                }

                .meta .pull-right {
                    margin-left: auto;
                }
            }

            /* Pinned comment styling */
            .pinned-comment .content-post-comments {
                border: 1px solid #ffc107 !important;
                background-color: #fffdf5 !important;
                box-shadow: 0 3px 10px rgba(255, 193, 7, 0.1) !important;
            }

            .pinned-comment .pinned-badge {
                color: #ffc107;
                font-size: 12px;
                display: inline-flex;
                align-items: center;
                gap: 3px;
            }

            /* Animations */
            .animate__fadeIn {
                animation-duration: 0.5s;
            }

            /* Comment item appearing animation */
            @keyframes commentAppear {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .blog-comment li {
                animation: commentAppear 0.4s ease-out;
            }

            /* Dark mode styles for comment component */
            body.dark-mode .section-title {
                color: #e0e0e0 !important;
            }

            body.dark-mode .submit-comment {
                background-color: #2d2d2d !important;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2) !important;
            }

            body.dark-mode .submit-comment:focus-within {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3) !important;
            }

            body.dark-mode .submit-comment textarea {
                background-color: #2d2d2d !important;
                border-color: #555 !important;
                color: #e0e0e0 !important;
            }

            body.dark-mode .submit-comment textarea:focus {
                border-color: var(--primary-color-3) !important;
                box-shadow: 0 0 0 0.2rem rgba(57, 205, 224, 0.25) !important;
            }

            body.dark-mode .submit-comment textarea::placeholder {
                color: rgba(224, 224, 224, 0.6) !important;
            }

            body.dark-mode .blog-comment .post-comments .content-post-comments {
                background-color: #2d2d2d !important;
                border-color: #555 !important;
                color: #e0e0e0 !important;
            }

            body.dark-mode .blog-comment .post-comments .content-post-comments:hover {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
            }

            body.dark-mode .blog-comment .meta {
                color: rgba(224, 224, 224, 0.6) !important;
                border-bottom-color: #404040 !important;
            }

            body.dark-mode .blog-comment ul.comments ul:before {
                border-left-color: #555 !important;
            }

            body.dark-mode .blog-comment ul.comments ul li:before {
                border-top-color: #555 !important;
            }

            body.dark-mode .reaction-btn {
                color: #e0e0e0 !important;
            }

            body.dark-mode .reaction-btn:hover {
                background-color: #404040 !important;
            }

            body.dark-mode .reaction-btn.active {
                background-color: #404040 !important;
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .reply-form {
                background-color: #404040 !important;
                border-left-color: var(--primary-color-3) !important;
            }

            body.dark-mode .reply-form textarea {
                background-color: #2d2d2d !important;
                border-color: #555 !important;
                color: #e0e0e0 !important;
            }

            body.dark-mode .reply-form textarea:focus {
                border-color: var(--primary-color-3) !important;
                box-shadow: 0 0 0 0.2rem rgba(57, 205, 224, 0.25) !important;
            }

            body.dark-mode .reply-form textarea::placeholder {
                color: rgba(224, 224, 224, 0.6) !important;
            }

            body.dark-mode .btn-outline-primary {
                border-color: var(--primary-color-3) !important;
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .btn-outline-primary:hover {
                background-color: var(--primary-color-3) !important;
                color: white !important;
            }

            body.dark-mode .pinned-comment .content-post-comments {
                border-color: #ffc107 !important;
                background-color: #2d2d2d !important;
                box-shadow: 0 3px 10px rgba(255, 193, 7, 0.2) !important;
            }

            body.dark-mode .pinned-comment .pinned-badge {
                color: #ffc107 !important;
            }
        </style>
    @endpush
@endonce

<!-- Delete Modal template -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p>Bạn có chắc muốn xóa bình luận này?</p>
                    <p class="text-muted small">Hành động này không thể hoàn tác.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>
