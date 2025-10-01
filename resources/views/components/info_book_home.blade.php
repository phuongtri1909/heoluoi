@php
    $isBookmarked = auth()->check() ? App\Models\Bookmark::isBookmarked(auth()->id(), $story->id) : false;
@endphp

<section id="info-book-home">
    <div class="mt-3">
        <div class="info-card-home h-100 py-5">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 d-flex flex-column mb-3 mb-md-0 ">
                    <div class="shadow rounded-2 position-relative">
                        <img src="{{ Storage::url($story->cover) }}" alt="{{ $story->title }}" class="img-fluid img-book">
                        @if ($story->is_18_plus === 1)
                            @include('components.tag18plus')
                        @endif
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <span class="fw-semibold d-flex align-items-center rating-count">
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($stats['ratings']['average'], 1) }}
                        </span>
                        <span class="fw-semibold d-flex align-items-center bookmark-count">
                            <img src="{{ asset('images/svg/bookmark.svg') }}" alt="eye" class="img-fluid">
                            {{ number_format($stats['total_bookmarks']) }}
                        </span>
                        <span class="fw-semibold d-flex align-items-center view-count">
                            <img src="{{ asset('images/svg/view.svg') }}" alt="eye" class="img-fluid">
                            {{ number_format($stats['total_views']) }}
                        </span>
                    </div>

                </div>
                <div class="col-12 col-md-6 col-lg-8 col-xl-9">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <div class="mb-3 text-start">
                            <h2 class="fw-semibold color-3 fs-3">{{ $story->title }}</h2>
                            <div class="mt-3">
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
    
    
                                <div class="info-row d-flex align-items-end">
                                    <div class="info-label mt-2">
                                        <span class="text-muted fw-semibold">Dịch giả</span>
                                    </div>
                                    <div class="info-content">
                                        <a href="{{ route('search.translator', ['query' => $story->user->name]) }}"
                                            class="text-decoration-none text-dark fw-semibold">
                                            {{ $story->user->name }}
                                        </a>
                                    </div>
                                </div>
    
    
                                <div class="info-row d-flex mt-2">
                                    <div class="info-label">
                                        <span class="text-muted fw-semibold">Tác Giả</span>
                                    </div>
                                    <div class="info-content">
                                        <a href="{{ route('search.author', ['query' => $story->author_name]) }}"
                                            class="text-decoration-none text-dark fw-semibold">
                                            {{ $story->author_name }}
                                        </a>
                                    </div>
                                </div>
    
                                <div class="info-row d-flex mt-2">
                                    <div class="info-label">
                                        <span class="text-muted fw-semibold">Tình Trạng</span>
                                    </div>
                                    <div class="info-content">
                                        <a href="{{ route('search.author', ['query' => $story->status]) }}"
                                            class="text-decoration-none text-dark fw-semibold">
                                            @if ($story->completed)
                                                <span class="text-success fw-bold">Hoàn Thành</span>
                                            @else
                                                <span class="text-primary fw-bold">Đang tiến hành</span>
                                            @endif
    
                                        </a>
                                    </div>
                                </div>
    
    
                                <!-- Thể loại -->
                                <div class="info-row d-flex mt-2">
    
                                    <span class="text-dark fw-semibold me-2">Thể Loại: </span>
    
                                    <div class="info-content">
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($storyCategories as $category)
                                                <a href="{{ route('categories.story.show', $category['slug']) }}"
                                                    class="tag-category">
                                                    {{ $category['name'] }}
                                                </a>
                                                @if (!$loop->last)
                                                    <span class="tag-category">,</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
    
                               
                            </div>
                        </div>
                       
                        <div class="d-flex">
                            <a href="#all-chapter"
                                class="btn btn-sm bg-7 text-decoration-none fw-semibold rounded-0 me-3">
                                Đọc truyện
                            </a>

                            <button
                                class="btn btn-sm btn-outline-dark rounded-0 text-decoration-none fw-semibold bookmark-toggle-btn"
                                data-story-id="{{ $story->id }}"
                                title="@auth @if ($isBookmarked) Bỏ theo dõi @else Theo dõi @endif @else Đăng nhập để theo dõi @endauth">
                                <span class="bookmark-label">
                                    <i class="fa-regular fa-bookmark"></i>
                                    @auth
                                        @if ($isBookmarked)
                                            Bỏ theo dõi
                                        @else
                                            Theo dõi
                                        @endif
                                    @else
                                        Theo dõi
                                    @endauth
                                </span>
                                <span class="bookmark-count ms-1">({{ $stats['total_bookmarks'] ?? 0 }})</span>
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<section id="description">
    <div class="container">
        <div class="section-title d-flex align-items-baseline bg-1 px-3 py-1">
            <h5 class="mb-0">Giới Thiệu</h5>
        </div>

        <div class="description-container">
            <div class="description-content text-muted mt-4 mb-0 text-justify"
                id="description-content-{{ $story->id }}">
                {!! $story->description !!}
            </div>
            <div class="description-toggle-btn mt-2 text-end d-none">
                <button class="btn btn-sm btn-link show-more-btn">Xem thêm »</button>
                <button class="btn btn-sm btn-link show-less-btn d-none">« Thu gọn</button>
            </div>
        </div>
    </div>
</section>
@push('styles')
    <style>
        .tag-category {
            color: #1fcad7;
            text-decoration: none;
        }

        .tag-category:hover {
            color: #1fcad7;
            text-decoration: underline !important;
        }

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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }

        .share-facebook {
            background: linear-gradient(135deg, #1877f2, #42a5f5);
            color: white;
        }

        .share-twitter {
            background: linear-gradient(135deg, #1da1f2, #0d8bd9);
            color: white;
        }

        .share-telegram {
            background: linear-gradient(135deg, #0088cc, #26a5e4);
            color: white;
        }

        .share-zalo {
            background: linear-gradient(135deg, #005baa, #0084ff);
            color: white;
        }

        .share-copy {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            color: white;
        }

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
            height: 300px;
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
            max-height: 60px;
            overflow: hidden;
            position: relative;
            transition: max-height 0.5s ease;
        }

        .description-content.expanded {
            max-height: 5000px;
        }

        .description-toggle-btn .btn-link {
            color: #000;
            text-decoration: none;
            padding: 2px 15px;
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

        .action-button {
            user-select: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function initDescriptionToggle() {
            const descriptionContent = document.getElementById('description-content-{{ $story->id }}');
            const toggleBtnContainer = document.querySelector('.description-toggle-btn');
            const showMoreBtn = document.querySelector('.show-more-btn');
            const showLessBtn = document.querySelector('.show-less-btn');

            if (descriptionContent && toggleBtnContainer) {
                if (descriptionContent.scrollHeight > descriptionContent.offsetHeight) {
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

                        descriptionContent.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initDescriptionToggle();
            initBookmarkToggle();

        });

        function initBookmarkToggle() {
            const bookmarkBtn = document.querySelector('.bookmark-toggle-btn');
            if (!bookmarkBtn) return;

            bookmarkBtn.addEventListener('click', function() {
                    @auth
                    const storyId = this.getAttribute('data-story-id');
                    const bookmarkLabel = this.querySelector('.bookmark-label');
                    const bookmarkCount = this.querySelector('.bookmark-count');
                    const isBookmarked = bookmarkLabel.textContent.trim() === 'Bỏ theo dõi';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    let currentCount = parseInt(bookmarkCount.textContent.replace(/[()]/g, '')) || 0;

                    if (isBookmarked) {
                        bookmarkLabel.innerHTML = '<i class="fa-regular fa-bookmark"></i> Theo dõi';
                        this.setAttribute('title', 'Theo dõi');
                        bookmarkCount.textContent = `(${Math.max(0, currentCount - 1)})`;
                    } else {
                        bookmarkLabel.innerHTML = '<i class="fa-regular fa-bookmark"></i> Bỏ theo dõi';
                        this.setAttribute('title', 'Bỏ theo dõi');
                        bookmarkCount.textContent = `(${currentCount + 1})`;
                    }

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
                            const coverBookmarkCount = document.querySelector(
                                '.col-12.col-md-6.col-lg-4.col-xl-3 .bookmark-count');
                            if (coverBookmarkCount && data.total_bookmarks !== undefined) {
                                const img = coverBookmarkCount.querySelector('img');
                                coverBookmarkCount.innerHTML = '';
                                if (img) {
                                    coverBookmarkCount.appendChild(img);
                                }
                                coverBookmarkCount.appendChild(document.createTextNode(' ' + data.total_bookmarks));
                            }

                            showToast(data.message, data.status === 'added' ? 'success' : 'info');
                        })
                        .catch(error => {
                            console.error('Error toggling bookmark:', error);

                            if (isBookmarked) {
                                bookmarkLabel.innerHTML = '<i class="fa-regular fa-bookmark"></i> Bỏ theo dõi';
                                this.setAttribute('title', 'Bỏ theo dõi');
                                bookmarkCount.textContent = `(${currentCount})`;
                            } else {
                                bookmarkLabel.innerHTML = '<i class="fa-regular fa-bookmark"></i> Theo dõi';
                                this.setAttribute('title', 'Theo dõi');
                                bookmarkCount.textContent = `(${Math.max(0, currentCount - 1)})`;
                            }

                            showToast('Đã xảy ra lỗi khi thực hiện thao tác này.', 'error');
                        });
                @else
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating-star');
            const starsContainer = document.getElementById('rating-stars');
            const storyId = starsContainer ? starsContainer.getAttribute('data-story-id') : null;
            const ratingMessage = document.getElementById('rating-message');

            if (ratingStars.length > 0 && storyId) {
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

                starsContainer.addEventListener('mouseout', function() {
                    resetStars();
                });

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

                function submitRating(rating) {
                    const existingIndicator = ratingMessage.querySelector('.rating-loading');
                    if (existingIndicator) {
                        ratingMessage.removeChild(existingIndicator);
                    }

                    const loadingIndicator = document.createElement('div');
                    loadingIndicator.className = 'rating-loading';
                    loadingIndicator.textContent = 'Đang gửi...';
                    ratingMessage.appendChild(loadingIndicator);

                    ratingStars.forEach(star => {
                        star.style.pointerEvents = 'none';
                    });

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
                            ratingMessage.removeChild(loadingIndicator);

                            ratingStars.forEach(star => {
                                star.style.pointerEvents = 'auto';
                            });

                            if (data.success) {
                                const coverRatingCount = document.querySelector('.rating-count');
                                if (coverRatingCount) {
                                    const ratingText = coverRatingCount.textContent;
                                    const newRatingText = ratingText.replace(/\d+\.\d+/, data.average);
                                    coverRatingCount.textContent = newRatingText;
                                }

                                let userRatingElement = document.getElementById('user-rating');
                                if (!userRatingElement) {
                                    const ratingStats = document.querySelector('.rating-stats');
                                    const userRatingDiv = document.createElement('div');
                                    userRatingDiv.className = 'mt-1 small text-muted';
                                    userRatingDiv.innerHTML = 'Đánh giá của bạn: <span id="user-rating">' + data
                                        .user_rating + '</span>/5';
                                    ratingStats.appendChild(userRatingDiv);
                                } else {
                                    userRatingElement.textContent = data.user_rating;
                                }

                                showToast(data.message, 'success');

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
                                showToast(data.message || 'Có lỗi xảy ra', 'error');
                            }
                        })
                        .catch(error => {
                            ratingMessage.removeChild(loadingIndicator);

                            ratingStars.forEach(star => {
                                star.style.pointerEvents = 'auto';
                            });

                            showToast('Đã xảy ra lỗi khi gửi đánh giá', 'error');

                        });
                }
            }
        });
    </script>
@endpush
