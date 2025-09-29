@php
    // Tối ưu: tính toán bookmark status một lần để tránh duplicate queries
    $isBookmarked = auth()->check() ? App\Models\Bookmark::isBookmarked(auth()->id(), $story->id) : false;
@endphp

<section id="info-book-home">
    <div class="mt-3">
        <div class="info-card-home h-100 py-5">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 d-flex flex-column mb-3 mb-md-0 ">
                        <div class="shadow rounded-4 position-relative">
                            <img src="{{ Storage::url($story->cover) }}" alt="{{ $story->title }}"
                                class="img-fluid img-book">
                            @if ($story->is_18_plus === 1)
                                @include('components.tag18plus')
                            @endif
                        </div>

                    </div>
                    <div class="col-12 col-md-6 col-lg-8 col-xl-9">
                        <div class="rounded-4 bg-white p-4 h-100">
                            <div class="mb-3 text-start">
                                <h2 class="fw-semibold color-3">{{ $story->title }}</h2>
                            </div>

                            <div class="d-flex">
                                <div class="rating">
                                    @php
                                        $userRating = 0;
                                        if (auth()->check()) {
                                            $existingRating = \App\Models\Rating::where('user_id', auth()->id())
                                                ->where('story_id', $story->id)
                                                ->first();
                                            $userRating = $existingRating ? $existingRating->rating : 0;
                                        }
                                        $fullStars = floor($userRating);
                                    @endphp

                                    <div class="stars-container">
                                        <div class="stars" id="rating-stars" data-story-id="{{ $story->id }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star rating-star {{ $i <= $fullStars ? 'full' : 'empty' }}"
                                                    data-rating="{{ $i }}"></i>
                                            @endfor
                                        </div>
                                        <div id="rating-message">

                                        </div>

                                        @if (!auth()->check())
                                            <div class="rating-login-message mt-2 text-muted small">
                                                <a href="{{ route('login') }}">Đăng nhập</a> để đánh giá truyện!
                                            </div>
                                        @endif
                                    </div>


                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 col-lg-8">

                                    <div class="info-row d-flex">
                                        <div class="info-label">
                                            <span class="color-3 fw-semibold">Đánh Giá</span>
                                        </div>
                                        <div class="info-content">
                                            <div class="rating-stats">
                                                <div>
                                                    <span
                                                        id="average-rating">{{ number_format($stats['ratings']['average'], 1) }}</span>/5
                                                    (<span id="ratings-count">{{ $stats['ratings']['count'] }}</span>
                                                    đánh giá)
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-row d-flex">
                                        <div class="info-label">
                                            <span class="color-3 fw-semibold">Lượt Xem</span>
                                        </div>
                                        <div class="info-content">
                                            <div class="rating-stats">
                                                <div>
                                                    <span>{{ number_format($stats['total_views']) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (auth()->check() && auth()->user()->role != 'user')
                                        <div class="info-row d-flex mt-2">
                                            <div class="info-label">
                                                <span class="color-3 fw-semibold">Tác Giả</span>
                                            </div>
                                            <div class="info-content">
                                                <a href="{{ route('search.author', ['query' => $story->author_name]) }}"
                                                    class="text-decoration-none text-dark">
                                                    {{ $story->author_name }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="info-row d-flex">
                                        <div class="info-label">
                                            <span class="color-3 fw-semibold">Chuyển Ngữ</span>
                                        </div>
                                        <div class="info-content">
                                            <a href="{{ route('search.translator', ['query' => $story->user->name]) }}"
                                                class="text-decoration-none text-dark">
                                                {{ $story->user->name }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="info-row d-flex">
                                        <div class="info-label">
                                            <span class="color-3 fw-semibold">Tổng Chương</span>
                                        </div>
                                        <div class="info-content">
                                            <span class="text-dark">
                                                {{ $stats['total_chapters'] }} Chương
                                            </span>
                                        </div>
                                    </div>



                                    <!-- Thể loại -->
                                    <div class="info-row d-flex mt-2">
                                        <div class="info-label">
                                            <span class="color-3 fw-semibold">Thể Loại</span>
                                        </div>
                                        <div class="info-content">
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($storyCategories as $category)
                                                    <a href="{{ route('categories.story.show', $category['slug']) }}"
                                                        class="badge bg-1 text-white small rounded-pill d-flex align-items-center me-2 text-decoration-none">
                                                        {{ $category['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="d-flex justify-content-start gap-3">
                                        <div class="stat-item text-dark">
                                            <i class="fas fa-book-open me-1 cl-8ed7ff"></i>
                                            <span class="counter" data-target="{{ $stats['total_chapters'] }}">0</span>
                                            <span>Chương</span>
                                        </div>
                                        <div class="stat-item text-dark">
                                            <i class="fas fa-eye eye text-primary"></i>
                                            <span class="counter" data-target="{{ $stats['total_views'] }}">0</span>
                                            <span>Lượt Xem</span>
                                        </div>
                                        <div class="stat-item text-dark">
                                            <i class="fas fa-star star cl-ffe371"></i>
                                            <span class="counter"
                                                data-target="{{ $stats['ratings']['count'] }}">0</span>
                                            <span>đánh giá</span>
                                        </div>

                                    </div> --}}
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="stat-item text-dark mt-2 d-flex">
                                        <p class="text-start mb-0 me-2">Trạng thái:</p>
                                        @if ($status->status == 'done')
                                            <span class="text-success fw-bold">Hoàn Thành</span>
                                        @else
                                            <span class="text-primary fw-bold">Đang tiến hành</span>
                                        @endif
                                    </div>

                                    @if($story->source_link)
                                        <div class="stat-item text-dark mt-2 d-flex justify-content-center">
                                            <a href="{{ $story->source_link }}" target="_blank" rel="noopener noreferrer" 
                                               class="action-button d-flex flex-column align-items-center text-decoration-none"
                                               title="Xem nguồn gốc">
                                                <div class="action-icon">
                                                    <i class="fas fa-external-link-alt fs-4 color-3"></i>
                                                </div>
                                                <div class="action-label small mt-1 text-center">
                                                    Nguồn
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    <div class="row mt-3">
                                        <div class="col-4">
                                            <a href="#comments"
                                                class="action-button d-flex flex-column align-items-center text-decoration-none">
                                                <div class="action-icon">
                                                    <i class="fas fa-comments fs-4 color-3"></i>
                                                </div>
                                                <div class="action-label small mt-1 text-center">
                                                    Bình luận
                                                </div>
                                            </a>
                                        </div>

                                        <!-- Số người theo dõi -->
                                        <div class="col-4">
                                            <div class="action-button d-flex flex-column align-items-center bookmark-toggle-btn"
                                                data-story-id="{{ $story->id }}"
                                                title="@auth @if ($isBookmarked) Bỏ theo dõi @else Theo dõi @endif @else Đăng nhập để theo dõi @endauth">
                                                <div class="action-icon position-relative">
                                                    <i
                                                        class="fas fa-heart fs-4 @auth @if ($isBookmarked) text-danger active @else color-3 @endif @else color-3 @endauth bookmark-icon"></i>
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small bookmark-count">
                                                        {{ $stats['total_bookmarks'] ?? 0 }}
                                                    </span>
                                                </div>
                                                <div class="action-label small mt-1 text-center bookmark-label">
                                                    @auth
                                                        @if ($isBookmarked)
                                                            Bỏ theo dõi
                                                        @else
                                                            Theo dõi
                                                        @endif
                                                    @else
                                                        Theo dõi
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Chia sẻ -->
                                        <div class="col-4">
                                            <div class="action-button d-flex flex-column align-items-center share-toggle-btn"
                                                data-story-id="{{ $story->id }}"
                                                data-story-title="{{ $story->title }}"
                                                data-story-url="{{ url()->current() }}"
                                                title="Chia sẻ truyện">
                                                <div class="action-icon">
                                                    <i class="fas fa-share-alt fs-4 color-3"></i>
                                                </div>
                                                <div class="action-label small mt-1 text-center">
                                                    Chia sẻ
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<section id="description">
    <div class="container mt-5">
        <div class="section-title d-flex align-items-baseline ">
            <i class="fa-solid fa-comment-medical fa-xl color-2 me-2"></i>
            <h5 class="mb-0">GIỚI THIỆU</h5>
        </div>

        <div class="description-container">
            <div class="description-content text-muted mt-4 mb-0 text-justify"
                id="description-content-{{ $story->id }}">
                {!! $story->description !!}
            </div>
            <div class="description-toggle-btn mt-2 text-center d-none">
                <button class="btn btn-sm btn-link show-more-btn">Xem thêm <i class="fas fa-chevron-down"></i></button>
                <button class="btn btn-sm btn-link show-less-btn d-none">Thu gọn <i
                        class="fas fa-chevron-up"></i></button>
            </div>
        </div>
    </div>
</section>
@push('styles')
    <style>
        .action-bar {
            width: 100%;
            margin-bottom: 1rem;
        }

        .action-button {
            padding: 0.5rem;
            color: #333;
            transition: all 0.2s ease;
        }

        .action-button:hover,
        .action-button:focus {
            color: var(--primary-color);
            background-color: rgba(67, 80, 255, 0.05);
            border-radius: 8px;
        }

        .action-button:active {
            transform: scale(0.95);
        }

        .action-icon {
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .color-3 {
            color: var(--primary-color);
        }

        /* Responsive styles */
        @media (max-width: 576px) {
            .action-icon {
                height: 30px;
            }

            .action-icon i {
                font-size: 1.2rem !important;
            }

            .action-label {
                font-size: 0.7rem !important;
            }
        }

        .info-table {
            width: 100%;
        }

        .info-row {
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .info-label {
            min-width: 120px;
        }

        /* Share Modal Styles */
        .share-modal {
            z-index: 1055;
        }
        
        .share-modal .modal-dialog {
            max-width: 280px;
        }
        
        .share-modal .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .share-modal .modal-header {
            padding: 12px 16px 8px;
        }
        
        .share-modal .modal-body {
            padding: 8px 16px 16px;
        }

        .share-option {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            margin-bottom: 6px;
            font-size: 0.85rem;
        }

        .share-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .share-facebook { background: linear-gradient(135deg, #1877f2, #42a5f5); color: white; }
        .share-twitter { background: linear-gradient(135deg, #1da1f2, #0d8bd9); color: white; }
        .share-telegram { background: linear-gradient(135deg, #0088cc, #26a5e4); color: white; }
        .share-zalo { background: linear-gradient(135deg, #005baa, #0084ff); color: white; }
        .share-copy { background: linear-gradient(135deg, #6c757d, #adb5bd); color: white; }

        .info-value {
            width: auto;
            padding-right: 1.5rem;
            text-align: start;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .info-content {
            flex: 1;
            text-align: start;
        }

        .story-categories {
            margin: 1rem 0;
        }

        .category-tag {
            display: inline-block;
            padding: 0.2rem 0.2rem;
            background: rgba(67, 80, 255, 0.1);
            color: #e26dfa;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .category-tag:hover {
            background: #f5a3ff;
            color: white;
            transform: translateY(-2px);
        }

        .category-count {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-left: 4px;
        }

        .category-tag:hover .category-count {
            opacity: 1;
        }

        /*  */

        body.dark-mode .info-card-home {
            background: #2d2d2d !important;
            border-color: #404040 !important;
        }

        .info-card-home {
            background: var(--primary-color-6);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .text-justify {
            text-align: justify;
            text-justify: inter-word;
            word-break: normal;
            line-height: 1.8;
            margin-bottom: 1rem;
            hyphens: auto;
        }

        .img-book {
            transition: transform 0.3s ease;
            width: 300px;
            height: 440px;
            object-fit: cover;

        }

        .shadow {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .stars {
            display: flex;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .rating-star {
            margin: 0 2px;
            transition: all 0.2s ease;
        }

        .rating-star.full {
            color: #ffe371;
        }

        .rating-star.hover {
            color: #ffe371;
            transform: scale(1.2);
        }

        .rating-loading {
            font-size: 0.8rem;
            margin-top: 8px;
            color: #6c757d;
        }

        .rating-success,
        .rating-error {
            animation: fadeIn 0.3s ease;
        }

        .stars-container {
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .rating-stats {
            font-size: 0.9rem;
        }

        #average-rating {
            font-weight: bold;
            color: #ffe371;
        }

        .description-content {
            max-height: 180px;
            /* Approx height for 10 lines - adjust as needed */
            overflow: hidden;
            position: relative;
            transition: max-height 0.5s ease;
        }

        .description-content.expanded {
            max-height: 5000px;
            /* Large enough to contain any description */
        }

        .description-content:not(.expanded)::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: linear-gradient(transparent, var(--primary-color-6));
            pointer-events: none;
        }

        .description-toggle-btn .btn-link {
            color: var(--primary-color-3);
            text-decoration: none;
            padding: 5px 15px;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .description-toggle-btn .btn-link:hover {
            background-color: var(--primary-color-2);
            color: white
        }

        /* Bookmark button styles */
        .bookmark-toggle-btn {
            cursor: pointer;
        }

        .bookmark-toggle-btn:hover .bookmark-icon.color-3 {
            color: #ff6b6b !important;
        }

        .bookmark-toggle-btn:hover .bookmark-icon.text-danger {
            filter: brightness(1.2);
        }

        .bookmark-icon {
            transition: all 0.3s ease;
        }

        .bookmark-icon.active {
            animation: heartbeat 0.3s ease-in-out;
        }

        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.3);
            }

            100% {
                transform: scale(1);
            }
        }

        .bookmark-count {
            transition: all 0.3s ease;
        }

        /* Disable text selection on action buttons */
        .action-button {
            user-select: none;
        }
    </style>
@endpush

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Description show more/less functionality
        function initDescriptionToggle() {
            const descriptionContent = document.getElementById('description-content-{{ $story->id }}');
            const toggleBtnContainer = document.querySelector('.description-toggle-btn');
            const showMoreBtn = document.querySelector('.show-more-btn');
            const showLessBtn = document.querySelector('.show-less-btn');

            if (descriptionContent && toggleBtnContainer) {
                // Check if content height exceeds the max-height
                if (descriptionContent.scrollHeight > descriptionContent.offsetHeight) {
                    // Content is taller than the container, show the toggle button
                    toggleBtnContainer.classList.remove('d-none');

                    showMoreBtn.addEventListener('click', function() {
                        descriptionContent.classList.add('expanded');
                        showMoreBtn.classList.add('d-none');
                        showLessBtn.classList.remove('d-none');
                    });

                    showLessBtn.addEventListener('click', function() {
                        descriptionContent.classList.remove('expanded');
                        showLessBtn.classList.add('d-none');
                        showMoreBtn.classList.remove('d-none');

                        // Scroll back to start of description
                        descriptionContent.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Add to your existing DOMContentLoaded code
            initDescriptionToggle();
            initBookmarkToggle();

            // Your existing code continues below...
        });

        // Function to initialize bookmark toggle functionality
        function initBookmarkToggle() {
            const bookmarkBtn = document.querySelector('.bookmark-toggle-btn');
            if (!bookmarkBtn) return;

            bookmarkBtn.addEventListener('click', function() {
                    @auth
                    const storyId = this.getAttribute('data-story-id');
                    const bookmarkIcon = this.querySelector('.bookmark-icon');
                    const bookmarkLabel = this.querySelector('.bookmark-label');
                    const bookmarkCount = this.querySelector('.bookmark-count');
                    const isActive = bookmarkIcon.classList.contains('active');

                    // CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Optimistic UI update
                    let currentCount = parseInt(bookmarkCount.textContent);

                    if (isActive) {
                        // Remove bookmark
                        bookmarkIcon.classList.remove('active', 'text-danger');
                        bookmarkIcon.classList.add('color-3');
                        bookmarkLabel.textContent = 'Theo dõi';
                        this.setAttribute('title', 'Theo dõi');
                        bookmarkCount.textContent = Math.max(0, currentCount - 1);
                    } else {
                        // Add bookmark
                        bookmarkIcon.classList.add('active', 'text-danger');
                        bookmarkIcon.classList.remove('color-3');
                        bookmarkLabel.textContent = 'Bỏ theo dõi';
                        this.setAttribute('title', 'Bỏ theo dõi');
                        bookmarkCount.textContent = currentCount + 1;
                    }

                    // Send request to server
                    fetch('{{ route('user.bookmark.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                story_id: storyId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hiển thị thông báo
                            showToast(data.message, data.status === 'added' ? 'success' : 'info');
                        })
                        .catch(error => {
                            console.error('Error toggling bookmark:', error);

                            // Rollback UI changes in case of error
                            if (isActive) {
                                bookmarkIcon.classList.add('active', 'text-danger');
                                bookmarkIcon.classList.remove('color-3');
                                bookmarkLabel.textContent = 'Bỏ theo dõi';
                                this.setAttribute('title', 'Bỏ theo dõi');
                                bookmarkCount.textContent = currentCount;
                            } else {
                                bookmarkIcon.classList.remove('active', 'text-danger');
                                bookmarkIcon.classList.add('color-3');
                                bookmarkLabel.textContent = 'Theo dõi';
                                this.setAttribute('title', 'Theo dõi');
                                bookmarkCount.textContent = Math.max(0, currentCount - 1);
                            }

                            // Hiển thị thông báo lỗi
                            showToast('Đã xảy ra lỗi khi thực hiện thao tác này.', 'error');
                        });
                @else
                    // Redirect to login page
                    Swal.fire({
                        title: 'Cần đăng nhập',
                        text: 'Bạn cần đăng nhập để theo dõi truyện này',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng nhập',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('login') }}';
                        }
                    });
                @endauth
            });
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating-star');
            const starsContainer = document.getElementById('rating-stars');
            const storyId = starsContainer ? starsContainer.getAttribute('data-story-id') : null;
            const ratingMessage = document.getElementById('rating-message');

            // Only set up rating functionality if we have stars and a story ID
            if (ratingStars.length > 0 && storyId) {
                // Star hover effect
                ratingStars.forEach(star => {
                    star.addEventListener('mouseover', function() {
                        const rating = parseInt(this.getAttribute('data-rating'));
                        highlightStars(rating);
                    });

                    star.addEventListener('click', function() {
                        @if (auth()->check())
                            const rating = parseInt(this.getAttribute('data-rating'));
                            submitRating(rating);
                        @else
                            window.location.href = "{{ route('login') }}";
                        @endif
                    });
                });

                // Reset stars on mouse out of the container
                starsContainer.addEventListener('mouseout', function() {
                    resetStars();
                });

                // Function to highlight stars up to a certain rating
                function highlightStars(rating) {
                    ratingStars.forEach(star => {
                        const starRating = parseInt(star.getAttribute('data-rating'));
                        if (starRating <= rating) {
                            star.classList.add('hover');
                            star.classList.remove('empty');
                        } else {
                            star.classList.remove('hover');
                            star.classList.remove('full');
                            star.classList.add('empty');
                        }
                    });
                }

                // Function to reset stars to their original state
                function resetStars() {
                    const userRating = {{ $userRating ?? 0 }};
                    ratingStars.forEach(star => {
                        star.classList.remove('hover');
                        const starRating = parseInt(star.getAttribute('data-rating'));
                        if (starRating <= userRating) {
                            star.classList.add('full');
                            star.classList.remove('empty');
                        } else {
                            star.classList.remove('full');
                            star.classList.add('empty');
                        }
                    });
                }

                // Function to submit the rating via AJAX
                function submitRating(rating) {
                    // Remove any existing loading indicator first (to avoid duplicates)
                    const existingIndicator = ratingMessage.querySelector('.rating-loading');
                    if (existingIndicator) {
                        ratingMessage.removeChild(existingIndicator);
                    }

                    // Create a loading indicator
                    const loadingIndicator = document.createElement('div');
                    loadingIndicator.className = 'rating-loading';
                    loadingIndicator.textContent = 'Đang gửi...';
                    ratingMessage.appendChild(loadingIndicator);

                    // Disable stars during submission
                    ratingStars.forEach(star => {
                        star.style.pointerEvents = 'none';
                    });

                    // Send the AJAX request
                    fetch("{{ route('ratings.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                story_id: storyId,
                                rating: rating
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading indicator
                            ratingMessage.removeChild(loadingIndicator);

                            // Re-enable stars
                            ratingStars.forEach(star => {
                                star.style.pointerEvents = 'auto';
                            });

                            if (data.success) {
                                // Update UI with new values
                                document.getElementById('average-rating').textContent = data.average;
                                document.getElementById('ratings-count').textContent = data.count;

                                // Update user rating display
                                let userRatingElement = document.getElementById('user-rating');
                                if (!userRatingElement) {
                                    // Create the user rating element if it doesn't exist
                                    const ratingStats = document.querySelector('.rating-stats');
                                    const userRatingDiv = document.createElement('div');
                                    userRatingDiv.className = 'mt-1 small text-muted';
                                    userRatingDiv.innerHTML = 'Đánh giá của bạn: <span id="user-rating">' + data
                                        .user_rating + '</span>/5';
                                    ratingStats.appendChild(userRatingDiv);
                                } else {
                                    userRatingElement.textContent = data.user_rating;
                                }

                                // Show success message using showToast
                                showToast(data.message, 'success');

                                // Update the active stars
                                ratingStars.forEach(star => {
                                    const starRating = parseInt(star.getAttribute('data-rating'));
                                    if (starRating <= data.user_rating) {
                                        star.classList.add('full');
                                        star.classList.remove('empty');
                                    } else {
                                        star.classList.remove('full');
                                        star.classList.add('empty');
                                    }
                                });
                            } else {
                                // Show error message using showToast
                                showToast(data.message || 'Có lỗi xảy ra', 'error');
                            }
                        })
                        .catch(error => {
                            // Remove loading indicator
                            ratingMessage.removeChild(loadingIndicator);

                            // Re-enable stars
                            ratingStars.forEach(star => {
                                star.style.pointerEvents = 'auto';
                            });

                            // Show error message using showToast
                            showToast('Đã xảy ra lỗi khi gửi đánh giá', 'error');

                        });
                }
            }
        });
    </script>
@endpush

<!-- Share Modal -->
<div class="modal fade share-modal" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 py-2">
                <h6 class="modal-title" id="shareModalLabel">
                    <i class="fas fa-share-alt me-2 text-primary"></i>
                    Chia sẻ
                </h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 pb-3">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="#" class="share-option share-facebook" data-platform="facebook">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fab fa-facebook-f me-2"></i>
                                <span class="small">Facebook</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="share-option share-twitter" data-platform="twitter">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fab fa-twitter me-2"></i>
                                <span class="small">Twitter</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="share-option share-telegram" data-platform="telegram">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fab fa-telegram-plane me-2"></i>
                                <span class="small">Telegram</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="share-option share-copy" data-platform="copy">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-copy me-2"></i>
                                <span class="small">Sao chép</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Share functionality
        document.addEventListener('DOMContentLoaded', function() {
            const shareToggleBtn = document.querySelector('.share-toggle-btn');
            const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
            
            if (shareToggleBtn) {
                let storyId, storyTitle, storyUrl;
                
                shareToggleBtn.addEventListener('click', function() {
                    storyId = this.dataset.storyId;
                    storyTitle = this.dataset.storyTitle;
                    storyUrl = this.dataset.storyUrl;
                    
                    shareModal.show();
                });
                
                // Handle share options
                document.querySelectorAll('.share-option').forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const platform = this.dataset.platform;
                        const text = `Đọc truyện "${storyTitle}" tại ${storyUrl}`;
                        
                        let shareUrl = '';
                        
                        switch(platform) {
                            case 'facebook':
                                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(storyUrl)}`;
                                break;
                            case 'twitter':
                                shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
                                break;
                            case 'telegram':
                                shareUrl = `https://t.me/share/url?url=${encodeURIComponent(storyUrl)}&text=${encodeURIComponent(storyTitle)}`;
                                break;
                            case 'zalo':
                                shareUrl = `https://zalo.me/share?url=${encodeURIComponent(storyUrl)}`;
                                break;
                            case 'copy':
                                // Ensure we have a valid URL
                                const urlToCopy = storyUrl || window.location.href;
                                
                                copyToClipboardReliable(urlToCopy, storyId);
                                return;
                        }
                        
                        if (shareUrl) {
                            window.open(shareUrl, '_blank', 'width=600,height=400');
                            // Close modal properly
                            const modalElement = document.getElementById('shareModal');
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            if (modal) {
                                modal.hide();
                            }
                            
                            completeShareTask(storyId, platform);
                        }
                    });
                });
            }
        });
        
        function copyToClipboardReliable(text, storyId) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Đã sao chép liên kết!', 'success');
                    closeModalAndCompleteTask(storyId);
                }).catch((err) => {
                    copyWithSelection(text, storyId);
                });
            } else {
                copyWithSelection(text, storyId);
            }
        }
        
        function copyWithSelection(text, storyId) {
            const tempDiv = document.createElement('div');
            tempDiv.textContent = text;
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '-9999px';
            tempDiv.style.opacity = '0';
            tempDiv.style.pointerEvents = 'none';
            tempDiv.style.userSelect = 'text';
            tempDiv.style.webkitUserSelect = 'text';
            tempDiv.style.mozUserSelect = 'text';
            tempDiv.style.msUserSelect = 'text';
            
            document.body.appendChild(tempDiv);
            
            // Create a selection
            const selection = window.getSelection();
            const range = document.createRange();
            
            selection.removeAllRanges();
            
            range.selectNodeContents(tempDiv);
            selection.addRange(range);
            
            try {
                const successful = document.execCommand('copy');
                
                selection.removeAllRanges();
                document.body.removeChild(tempDiv);
                
                if (successful) {
                    showToast('Đã sao chép liên kết!', 'success');
                    closeModalAndCompleteTask(storyId);
                } else {
                    copyWithInputElement(text, storyId);
                }
            } catch (err) {
                selection.removeAllRanges();
                document.body.removeChild(tempDiv);
                copyWithInputElement(text, storyId);
            }
        }
        
        function copyWithInputElement(text, storyId) {
            const input = document.createElement('input');
            input.type = 'text';
            input.value = text;
            input.style.position = 'fixed';
            input.style.left = '50%';
            input.style.top = '50%';
            input.style.transform = 'translate(-50%, -50%)';
            input.style.opacity = '0';
            input.style.pointerEvents = 'none';
            input.style.zIndex = '-1';
            input.style.width = '1px';
            input.style.height = '1px';
            input.style.border = 'none';
            input.style.outline = 'none';
            input.style.padding = '0';
            input.style.margin = '0';
            
            document.body.appendChild(input);
            
            input.focus();
            input.select();
            input.setSelectionRange(0, text.length);
            
            setTimeout(() => {
                try {
                    const successful = document.execCommand('copy');
                    document.body.removeChild(input);
                    
                    if (successful) {
                        showToast('Đã sao chép liên kết!', 'success');
                        closeModalAndCompleteTask(storyId);
                    } else {
                        copyWithTextareaElement(text, storyId);
                    }
                } catch (err) {
                    document.body.removeChild(input);
                    copyWithTextareaElement(text, storyId);
                }
            }, 10);
        }
        
        function copyWithTextareaElement(text, storyId) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '50%';
            textarea.style.top = '50%';
            textarea.style.transform = 'translate(-50%, -50%)';
            textarea.style.opacity = '0';
            textarea.style.pointerEvents = 'none';
            textarea.style.zIndex = '-1';
            textarea.style.width = '1px';
            textarea.style.height = '1px';
            textarea.style.border = 'none';
            textarea.style.outline = 'none';
            textarea.style.padding = '0';
            textarea.style.margin = '0';
            textarea.style.resize = 'none';
            textarea.style.overflow = 'hidden';
            
            document.body.appendChild(textarea);
            
            textarea.focus();
            textarea.select();
            textarea.setSelectionRange(0, text.length);
            
            setTimeout(() => {
                try {
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);
                    
                    if (successful) {
                        showToast('Đã sao chép liên kết!', 'success');
                        closeModalAndCompleteTask(storyId);
                    } else {
                        showToast('Không thể sao chép liên kết. Vui lòng thử lại.', 'error');
                    }
                } catch (err) {
                    document.body.removeChild(textarea);
                    showToast('Không thể sao chép liên kết. Vui lòng thử lại.', 'error');
                }
            }, 10);
        }
        
        function closeModalAndCompleteTask(storyId) {
            const modalElement = document.getElementById('shareModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            if (storyId) {
                completeShareTask(storyId, 'copy');
            }
        }
        
        // Complete share task
        function completeShareTask(storyId, platform) {
            if (!@json(auth()->check())) {
                showToast('Vui lòng đăng nhập để nhận thưởng', 'info');
                return;
            }
            
            fetch('{{ route("user.daily-tasks.complete.share") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    story_id: storyId,
                    platform: platform
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                   
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

    </style>
@endpush

@push('scripts')
    <script>
        // Share functionality
        document.addEventListener('DOMContentLoaded', function() {
            const shareToggleBtn = document.querySelector('.share-toggle-btn');
            const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
            
            if (shareToggleBtn) {
                let storyId, storyTitle, storyUrl;
                
                shareToggleBtn.addEventListener('click', function() {
                    storyId = this.dataset.storyId;
                    storyTitle = this.dataset.storyTitle;
                    storyUrl = this.dataset.storyUrl;
                    
                    shareModal.show();
                });
                
                // Handle share options
                document.querySelectorAll('.share-option').forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const platform = this.dataset.platform;
                        const text = `Đọc truyện "${storyTitle}" tại ${storyUrl}`;
                        
                        let shareUrl = '';
                        
                        switch(platform) {
                            case 'facebook':
                                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(storyUrl)}`;
                                break;
                            case 'twitter':
                                shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
                                break;
                            case 'telegram':
                                shareUrl = `https://t.me/share/url?url=${encodeURIComponent(storyUrl)}&text=${encodeURIComponent(storyTitle)}`;
                                break;
                            case 'zalo':
                                shareUrl = `https://zalo.me/share?url=${encodeURIComponent(storyUrl)}`;
                                break;
                            case 'copy':
                                // Ensure we have a valid URL
                                const urlToCopy = storyUrl || window.location.href;
                                
                                copyToClipboardReliable(urlToCopy, storyId);
                                return;
                        }
                        
                        if (shareUrl) {
                            window.open(shareUrl, '_blank', 'width=600,height=400');
                            // Close modal properly
                            const modalElement = document.getElementById('shareModal');
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            if (modal) {
                                modal.hide();
                            }
                            
                            completeShareTask(storyId, platform);
                        }
                    });
                });
            }
        });
        
        function copyToClipboardReliable(text, storyId) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Đã sao chép liên kết!', 'success');
                    closeModalAndCompleteTask(storyId);
                }).catch((err) => {
                    copyWithSelection(text, storyId);
                });
            } else {
                copyWithSelection(text, storyId);
            }
        }
        
        function copyWithSelection(text, storyId) {
            const tempDiv = document.createElement('div');
            tempDiv.textContent = text;
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '-9999px';
            tempDiv.style.opacity = '0';
            tempDiv.style.pointerEvents = 'none';
            tempDiv.style.userSelect = 'text';
            tempDiv.style.webkitUserSelect = 'text';
            tempDiv.style.mozUserSelect = 'text';
            tempDiv.style.msUserSelect = 'text';
            
            document.body.appendChild(tempDiv);
            
            // Create a selection
            const selection = window.getSelection();
            const range = document.createRange();
            
            selection.removeAllRanges();
            
            range.selectNodeContents(tempDiv);
            selection.addRange(range);
            
            try {
                const successful = document.execCommand('copy');
                
                selection.removeAllRanges();
                document.body.removeChild(tempDiv);
                
                if (successful) {
                    showToast('Đã sao chép liên kết!', 'success');
                    closeModalAndCompleteTask(storyId);
                } else {
                    copyWithInputElement(text, storyId);
                }
            } catch (err) {
                selection.removeAllRanges();
                document.body.removeChild(tempDiv);
                copyWithInputElement(text, storyId);
            }
        }
        
        function copyWithInputElement(text, storyId) {
            const input = document.createElement('input');
            input.type = 'text';
            input.value = text;
            input.style.position = 'fixed';
            input.style.left = '50%';
            input.style.top = '50%';
            input.style.transform = 'translate(-50%, -50%)';
            input.style.opacity = '0';
            input.style.pointerEvents = 'none';
            input.style.zIndex = '-1';
            input.style.width = '1px';
            input.style.height = '1px';
            input.style.border = 'none';
            input.style.outline = 'none';
            input.style.padding = '0';
            input.style.margin = '0';
            
            document.body.appendChild(input);
            
            input.focus();
            input.select();
            input.setSelectionRange(0, text.length);
            
            setTimeout(() => {
                try {
                    const successful = document.execCommand('copy');
                    document.body.removeChild(input);
                    
                    if (successful) {
                        showToast('Đã sao chép liên kết!', 'success');
                        closeModalAndCompleteTask(storyId);
                    } else {
                        copyWithTextareaElement(text, storyId);
                    }
                } catch (err) {
                    document.body.removeChild(input);
                    copyWithTextareaElement(text, storyId);
                }
            }, 10);
        }
        
        function copyWithTextareaElement(text, storyId) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '50%';
            textarea.style.top = '50%';
            textarea.style.transform = 'translate(-50%, -50%)';
            textarea.style.opacity = '0';
            textarea.style.pointerEvents = 'none';
            textarea.style.zIndex = '-1';
            textarea.style.width = '1px';
            textarea.style.height = '1px';
            textarea.style.border = 'none';
            textarea.style.outline = 'none';
            textarea.style.padding = '0';
            textarea.style.margin = '0';
            textarea.style.resize = 'none';
            textarea.style.overflow = 'hidden';
            
            document.body.appendChild(textarea);
            
            textarea.focus();
            textarea.select();
            textarea.setSelectionRange(0, text.length);
            
            setTimeout(() => {
                try {
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);
                    
                    if (successful) {
                        showToast('Đã sao chép liên kết!', 'success');
                        closeModalAndCompleteTask(storyId);
                    } else {
                        showToast('Không thể sao chép liên kết. Vui lòng thử lại.', 'error');
                    }
                } catch (err) {
                    document.body.removeChild(textarea);
                    showToast('Không thể sao chép liên kết. Vui lòng thử lại.', 'error');
                }
            }, 10);
        }
        
        function closeModalAndCompleteTask(storyId) {
            const modalElement = document.getElementById('shareModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            if (storyId) {
                completeShareTask(storyId, 'copy');
            }
        }
        
        // Complete share task
        function completeShareTask(storyId, platform) {
            if (!@json(auth()->check())) {
                showToast('Vui lòng đăng nhập để nhận thưởng', 'info');
                return;
            }
            
            fetch('{{ route("user.daily-tasks.complete.share") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    story_id: storyId,
                    platform: platform
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                   
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endpush
