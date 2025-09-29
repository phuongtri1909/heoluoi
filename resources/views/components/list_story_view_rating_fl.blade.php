<div class="sidebar-widget recent-reads rounded-4 shadow-sm">
    <div class="widget-header bg-pb">
        <h2 class="fs-5 m-0 text-dark fw-bold title-dark">
            <span class="hot-stories-tag">HOT</span>
            Phổ Biến
        </h2>
        <ul class="nav nav-tabs nav-fill mt-3" id="hotStoriesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="viewed-tab" data-bs-toggle="tab" data-bs-target="#viewed"
                    type="button" role="tab">
                    <i class="fa-solid fa-eye me-1"></i>Lượt Xem
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rating-tab" data-bs-toggle="tab" data-bs-target="#rating" type="button"
                    role="tab">
                    <i class="fa-solid fa-star me-1"></i>Đánh Giá
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="followed-tab" data-bs-toggle="tab" data-bs-target="#followed"
                    type="button" role="tab">
                    <i class="fa-solid fa-heart me-1"></i>Theo Dõi
                </button>
            </li>
        </ul>
    </div>
    <div class="widget-content">
        <!-- Tab Content -->
        <div class="tab-content" id="hotStoriesContent">
            <!-- Top Viewed Stories -->
            <div class="tab-pane fade show active" id="viewed" role="tabpanel">
                <a class="color-3 text-decoration-none d-flex justify-content-center align-items-baseline"
                    href="{{ route('story.view') }}">Xem tất cả</a>
                <div class="hot-stories-list">
                    @foreach ($topViewedStories as $index => $story)
                        <div class="hot-story-item d-flex p-2 {{ $index < 9 ? 'border-bottom' : '' }}">

                            <div class="story-cover me-2">
                                <a class="text-decoration-none" href="{{ route('show.page.story', $story->slug) }}">
                                    <img src="{{ asset('storage/' . $story->cover) }}" alt="{{ $story->title }}"
                                        class="hot-story-thumb">
                                </a>
                            </div>
                            <div class="story-info w-100 d-flex flex-column justify-content-center">
                                <h4 class="hot-story-title">
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('show.page.story', $story->slug) }}">{{ $story->title }}</a>
                                    @if ($index < 3)
                                        <span class="trending-badge">TRENDING</span>
                                    @endif
                                </h4>
                                <div class="d-flex">
                                    @php
                                        $mainCategories = $story->categories->where('is_main', true);
                                        $displayCategories = collect();

                                        foreach ($mainCategories->take(2) as $category) {
                                            $displayCategories->push($category);
                                        }

                                        if ($displayCategories->count() < 2) {
                                            $subCategories = $story->categories->where('is_main', false);
                                            foreach (
                                                $subCategories->take(2 - $displayCategories->count())
                                                as $category
                                            ) {
                                                $displayCategories->push($category);
                                            }
                                        }
                                    @endphp

                                    @foreach ($displayCategories as $category)
                                        <span
                                            class="badge bg-1 text-white small rounded-pill d-flex align-items-center me-2">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">{{ $story->chapters_count }} chương</p>
                                    <div class="stats-info">
                                        <span class="text-primary">
                                            <i class="fa-solid fa-eye"></i> {{ number_format($story->total_views) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="story-rank rank-{{ $index < 3 ? 'top' : 'normal' }}">
                                        @if ($index < 3)
                                            <i class="fa-solid fa-crown"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Rated Stories -->
            <div class="tab-pane fade" id="rating" role="tabpanel">
                <a class="color-3 text-decoration-none d-flex justify-content-center align-items-baseline"
                    href="{{ route('story.rating') }}">Xem tất cả </a>
                <div class="hot-stories-list">
                    @foreach ($ratingStories as $index => $story)
                        <div class="hot-story-item d-flex p-2 {{ $index < 9 ? 'border-bottom' : '' }}">

                            <div class="story-cover me-2">
                                <a class="text-decoration-none" href="{{ route('show.page.story', $story->slug) }}">
                                    <img src="{{ asset('storage/' . $story->cover) }}" alt="{{ $story->title }}"
                                        class="hot-story-thumb">
                                </a>
                            </div>
                            <div class="story-info w-100 d-flex flex-column justify-content-center">
                                <h4 class="hot-story-title">
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('show.page.story', $story->slug) }}">{{ $story->title }}</a>
                                    @if ($index < 3)
                                        <span class="top-rated-badge">TOP RATED</span>
                                    @endif
                                </h4>
                                <div class="d-flex">
                                    @php
                                        $mainCategories = $story->categories->where('is_main', true);
                                        $displayCategories = collect();

                                        foreach ($mainCategories->take(2) as $category) {
                                            $displayCategories->push($category);
                                        }

                                        if ($displayCategories->count() < 2) {
                                            $subCategories = $story->categories->where('is_main', false);
                                            foreach (
                                                $subCategories->take(2 - $displayCategories->count())
                                                as $category
                                            ) {
                                                $displayCategories->push($category);
                                            }
                                        }
                                    @endphp

                                    @foreach ($displayCategories as $category)
                                        <span
                                            class="badge bg-1 text-white small rounded-pill d-flex align-items-center me-2">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">{{ $story->chapters_count }} chương</p>
                                    <div class="stats-info">
                                        <span class="text-warning">
                                            <i class="fa-solid fa-star"></i>
                                            {{ number_format($story->average_rating, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="story-rank rank-{{ $index < 3 ? 'top' : 'normal' }}">
                                        @if ($index < 3)
                                            <i class="fa-solid fa-trophy"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Followed Stories -->
            <div class="tab-pane fade" id="followed" role="tabpanel">
                <a class="color-3 text-decoration-none d-flex justify-content-center align-items-baseline"
                    href="{{ route('story.follow') }}">Xem tất cả </a>
                <div class="hot-stories-list">
                    @foreach ($topFollowedStories as $index => $story)
                        <div class="hot-story-item d-flex p-2 {{ $index < 9 ? 'border-bottom' : '' }}">

                            <div class="story-cover me-2">
                                <a class="text-decoration-none" href="{{ route('show.page.story', $story->slug) }}">
                                    <img src="{{ asset('storage/' . $story->cover) }}" alt="{{ $story->title }}"
                                        class="hot-story-thumb">
                                </a>
                            </div>
                            <div class="story-info w-100 d-flex flex-column justify-content-center">
                                <h4 class="hot-story-title">
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('show.page.story', $story->slug) }}">{{ $story->title }}</a>
                                    @if ($index < 3)
                                        <span class="popular-badge">POPULAR</span>
                                    @endif
                                </h4>
                                <div class="d-flex">
                                    @php
                                        $mainCategories = $story->categories->where('is_main', true);
                                        $displayCategories = collect();

                                        foreach ($mainCategories->take(2) as $category) {
                                            $displayCategories->push($category);
                                        }

                                        if ($displayCategories->count() < 2) {
                                            $subCategories = $story->categories->where('is_main', false);
                                            foreach (
                                                $subCategories->take(2 - $displayCategories->count())
                                                as $category
                                            ) {
                                                $displayCategories->push($category);
                                            }
                                        }
                                    @endphp

                                    @foreach ($displayCategories as $category)
                                        <span
                                            class="badge bg-1 text-white small rounded-pill d-flex align-items-center me-2">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="mb-0">{{ $story->chapters_count }} chương</p>
                                    <div class="stats-info">
                                        <span class="text-danger">
                                            <i class="fa-solid fa-heart"></i>
                                            {{ number_format($story->bookmarks_count) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="story-rank rank-{{ $index < 3 ? 'top' : 'normal' }}">
                                        @if ($index < 3)
                                            <i class="fa-solid fa-fire"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .bg-pb{
                background: #99eae7;
            }

            @media (min-width: 768px) {
                .bg-pb{
                    background: var(--primary-color-2);
                }
            }

            /* Hot Stories Tag */
            .hot-stories-tag {
                display: inline-block;
                background: linear-gradient(45deg, #ff6b6b, #ffd93d, #6bcf7f, #4ecdc4, #45b7d1, #96ceb4, #ffeaa7, #fd79a8);
                background-size: 300% 300%;
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 0.7rem;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-right: 8px;
                position: relative;
                overflow: hidden;
                animation: shimmer 2s infinite, pulse 1.5s infinite alternate;
                box-shadow: 0 2px 10px rgba(255, 107, 107, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            .hot-story-item {
                transition: all 0.3s ease;
                position: relative;
            }

            .hot-story-item:hover {
                background-color: rgba(0, 0, 0, 0.03);
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Enhanced story rank styles */
            .story-rank {
                min-width: 30px;
                min-height: 30px;
                width: 30px;
                height: 30px;
                flex: 0 0 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                border-radius: 50%;
                margin-right: 10px;
                font-size: 0.8rem;
                transition: all 0.3s ease;
            }

            .story-rank.rank-top {
                background: linear-gradient(45deg, #ffd700, #ffed4a);
                color: #b45309;
                box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
                animation: rankGlow 2s infinite alternate;
            }

            .story-rank.rank-normal {
                color: var(--primary-color-3);
                border: 1px solid var(--primary-color-4);
                background-color: transparent;
            }

            @keyframes rankGlow {
                0% {
                    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
                }

                100% {
                    box-shadow: 0 4px 16px rgba(255, 215, 0, 0.6);
                }
            }

            /* Story badges */
            .trending-badge {
                background: linear-gradient(45deg, #dc2626, #ef4444);
                color: white;
                padding: 1px 4px;
                border-radius: 4px;
                font-size: 0.6rem;
                font-weight: bold;
                margin-left: 4px;
                animation: pulse 2s infinite;
            }

            .top-rated-badge {
                background: linear-gradient(45deg, #f59e0b, #fbbf24);
                color: white;
                padding: 1px 4px;
                border-radius: 4px;
                font-size: 0.6rem;
                font-weight: bold;
                margin-left: 4px;
                animation: pulse 2s infinite;
            }

            .popular-badge {
                background: linear-gradient(45deg, #e11d48, #f43f5e);
                color: white;
                padding: 1px 4px;
                border-radius: 4px;
                font-size: 0.6rem;
                font-weight: bold;
                margin-left: 4px;
                animation: pulse 2s infinite;
            }

            /* Stats info styling */
            .stats-info {
                font-size: 0.8rem;
                font-weight: 500;
            }

            .stats-info i {
                margin-right: 2px;
            }

            .hot-story-thumb {
                width: 100px;
                height: 140px;
                object-fit: cover;
                border-radius: 6px;
                transition: transform 0.3s ease;
            }

            .hot-story-thumb:hover {
                transform: scale(1.05);
            }

            .hot-story-title {
                font-size: 0.9rem;
                margin-bottom: 5px;
                line-height: 1.3;
                max-height: 2.6rem;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            /* Enhanced tab styling */
            #hotStoriesTabs .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
                font-weight: 500;
                color: #555;
                border-top: none;
                transition: all 0.3s ease;
                position: relative;
            }

            #hotStoriesTabs .nav-link:hover {
                color: var(--primary-color-3);
                background-color: rgba(57, 205, 224, 0.1);
            }

            #hotStoriesTabs .nav-link.active {
                color: var(--primary-color-3);
                border-color: #dee2e6 #dee2e6 #fff;
                background-color: white;
            }

            #hotStoriesTabs .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(45deg, var(--primary-color-3), #4ecdc4);
            }

            /* Animations */
            @keyframes shimmer {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            @keyframes pulse {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.05);
                }

                100% {
                    transform: scale(1);
                }
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .hot-stories-tag {
                    font-size: 0.6rem;
                    padding: 1px 6px;
                    margin-right: 6px;
                }

                .story-rank {
                    min-width: 25px;
                    min-height: 25px;
                    width: 25px;
                    height: 25px;
                    font-size: 0.7rem;
                }

                .hot-story-thumb {
                    width: 70px;
                    height: 115px;
                }

                .hot-story-title {
                    font-size: 0.8rem;
                }

                #hotStoriesTabs .nav-link {
                    font-size: 0.75rem;
                    padding: 0.4rem 0.5rem;
                }

                .trending-badge,
                .top-rated-badge,
                .popular-badge {
                    font-size: 0.5rem;
                    padding: 0px 3px;
                }
            }

            /* Dark mode styles */
            body.dark-mode .sidebar-widget {
                background-color: #2d2d2d !important;
                border-color: #404040 !important;
            }

            body.dark-mode .widget-header {
                background-color: #404040 !important;
            }

            body.dark-mode .hot-story-item {
                border-color: #404040 !important;
            }

            body.dark-mode .hot-story-item:hover {
                background-color: rgba(255, 255, 255, 0.05) !important;
            }

            body.dark-mode .hot-story-title a {
                color: #e0e0e0 !important;
            }

            body.dark-mode .hot-story-title a:hover {
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .story-rank {
                background-color: #404040 !important;
                border-color: var(--primary-color-3) !important;
                color: var(--primary-color-3) !important;
            }

            body.dark-mode #hotStoriesTabs .nav-link {
                color: #ccc !important;
                border-color: #404040 !important;
            }

            body.dark-mode #hotStoriesTabs .nav-link:hover {
                color: var(--primary-color-3) !important;
                background-color: rgba(57, 205, 224, 0.1) !important;
            }

            body.dark-mode #hotStoriesTabs .nav-link.active {
                color: var(--primary-color-3) !important;
                background-color: #404040 !important;
                border-color: #404040 #404040 #2d2d2d !important;
            }

            body.dark-mode .badge.bg-1 {
                background-color: var(--primary-color-3) !important;
            }

            body.dark-mode .hot-stories-tag {
                background: linear-gradient(135deg, #dc2626, #ef4444, #f87171, #dc2626) !important;
            }
        </style>
    @endpush
@endonce
