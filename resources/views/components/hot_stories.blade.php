<div class="sidebar-widget recent-reads rounded-4 shadow-sm">
    <div class="widget-header bg-2">
        <h2 class="fs-5 m-0 text-dark fw-bold title-dark"><i class="fa-solid fa-fire fa-lg" style="color: #ef4444;"></i> Truyện Hot
        </h2>
        <ul class="nav nav-tabs nav-fill mt-3" id="hotStoriesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily"
                    type="button" role="tab">
                    Hôm nay
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button"
                    role="tab">
                    Tuần này
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button"
                    role="tab">
                    Tháng này
                </button>
            </li>
        </ul>
    </div>
    <div class="widget-content mt-4">


        <!-- Tab Content -->
        <div class="tab-content" id="hotStoriesContent">
            <!-- Daily Hot Stories -->
            <div class="tab-pane fade show active" id="daily" role="tabpanel">
                <div class="hot-stories-list">
                    @foreach ($dailyTopPurchased as $index => $story)
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

                                    <div class="text-muted text-sm">
                                        @if ($story->latestChapter)
                                            {{ $story->latest_purchase_diff ?? 'Chưa có ai mua' }}
                                        @else
                                            Chưa cập nhật
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mt-2">
                                    <span class="story-rank">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Weekly Hot Stories -->
            <div class="tab-pane fade" id="weekly" role="tabpanel">
                <div class="hot-stories-list">
                    @foreach ($weeklyTopPurchased as $index => $story)
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

                                    <div class="text-muted text-sm">
                                        @if ($story->latestChapter)
                                            {{ $story->latest_purchase_diff ?? 'Chưa có ai mua' }}
                                        @else
                                            Chưa cập nhật
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mt-2">
                                    <span class="story-rank">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Monthly Hot Stories -->
            <div class="tab-pane fade" id="monthly" role="tabpanel">
                <div class="hot-stories-list">
                    @foreach ($monthlyTopPurchased as $index => $story)
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

                                    <div class="text-muted text-sm">
                                        @if ($story->latestChapter)
                                            {{ $story->latest_purchase_diff ?? 'Chưa có ai mua' }}
                                        @else
                                            Chưa cập nhật
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="story-rank">
                                        {{ $index + 1 }}
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
            /* Hot Stories Styles */


            .hot-story-item {
                transition: background-color 0.2s;
            }

            .hot-story-item:hover {
                background-color: rgba(0, 0, 0, 0.03);
            }

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
                border-radius: 4px;
                margin-right: 10px;
                color: var(--primary-color-3);
                border: 1px solid var(--primary-color-4);
                background-color: transparent;
            }

            .hot-story-thumb {
                width: 100px;
                height: 140px;
                object-fit: cover;
                border-radius: 4px;
            }

            .hot-story-title {
                font-size: 0.9rem;
                margin-bottom: 5px;
                line-height: 1.3;
                max-height: 2.6rem;
                overflow: hidden;
            }

            .latest-chapter a {
                color: #666;
                text-decoration: none;
            }

            .latest-chapter a:hover {
                color: #007bff;
            }

            .publish-date {
                font-size: 0.8rem;
                margin-top: 2px;
            }

            /* Style the tab nav */
            #hotStoriesTabs .nav-link {
                padding: 0.5rem;
                font-size: 0.9rem;
                font-weight: 500;
                color: #555;
                border-top: none;
            }

            #hotStoriesTabs .nav-link.active {
                color: var(--primary-color-3);
                border-color: #dee2e6 #dee2e6 #fff;
                position: relative;
            }

            #hotStoriesTabs .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                height: 2px;
                background-color: var(--primary-color-3);
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

            body.dark-mode #hotStoriesTabs .nav-link.active {
                color: var(--primary-color-3) !important;
                background-color: #404040 !important;
                border-color: #404040 #404040 #2d2d2d !important;
            }

            body.dark-mode .badge.bg-1 {
                background-color: var(--primary-color-3) !important;
            }
        </style>
    @endpush
@endonce
