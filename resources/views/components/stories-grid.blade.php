<div class="story-card rounded">
    <div class="story-thumbnail">
        <a href="{{ route('show.page.story', $story->slug) }}">
            <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                alt="{{ $story->title }}" class="img-fluid">
            @if($story->is_18_plus === 1)
                @include('components.tag18plus')
            @endif

            @if ($story->completed === 1)
                <span class="badge-full"> Full </span>
            @endif

            @if(isset($story->featured_time_remaining) && $story->featured_time_remaining > 0)
                <div class="featured-timer">
                    <div class="timer-triangle">
                        <i class="fas fa-clock timer-icon"></i>
                        <span class="timer-text">{{ $story->featured_time_remaining }}h</span>
                    </div>
                </div>
            @endif
        </a>
    </div>
    <div class="story-info px-2 text-sm text-gray-600 fw-semibold">
        <div>
            <h5 class="story-title mb-0 text-sm fw-semibold lh-base">
                <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none text-dark">
                    {{ $story->title }}
                </a>
            </h5>
        </div>
        <div class="story-views-time-container mt-1">
            <span class="story-views">
                <i class="fas fa-eye eye fs-8 text-primary"></i>
                {{ format_number_short($story->total_views) }}
            </span>
            <span class="story-time text-danger">
                <i class="fa-solid fa-heart"></i>
                {{ number_format($story->bookmarks_count) }}
            </span>
        </div>
        <div class="story-stats-container mb-2 mt-1">
            <div class="d-flex justify-content-between">
                <span class="d-flex align-items-center">
                    <i class="fas fa-star star cl-ffe371 fs-8 me-1"></i> 
                    {{ number_format($story->average_rating, 1) }}
                </span>
                <span class="mb-0 badge bg-1 text-white small rounded-pill d-flex align-items-center">Chương {{ $story->latestChapter->number }} </span>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .story-thumbnail {
                position: relative;
                padding-top: 150%;
                overflow: hidden;
                border-top-left-radius: inherit;
                border-top-right-radius: inherit;
            }

            .story-thumbnail img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .badge-full {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: #28a745 !important;
                color: white;
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 600;
                z-index: 2;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }

            .featured-timer {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 3;
            }

            .timer-triangle {
                width: 0;
                height: 0;
                border-style: solid;
                border-width: 50px 50px 0 0;
                border-color: #3b82f6 transparent transparent transparent;
                position: relative;
            }

            .timer-icon {
                position: absolute;
                top: -45px;
                left: 5px;
                color: white;
                font-size: 0.6rem;
                z-index: 4;
                transform: rotate(-45deg);
            }

            .timer-text {
                position: absolute;
                top: -36px;
                left: 9px;
                color: white;
                font-size: 0.7rem;
                font-weight: 600;
                z-index: 4;
                transform: rotate(-45deg);
                line-height: 1;
            }

            .hover-content {
                color: white;
                text-align: center;
                transform: translateY(10px);
                transition: transform 0.3s ease;
            }

            .story-card {
                overflow: hidden;
                transition: all 0.3s ease;
                height: 100%;
                position: relative;
                z-index: 1;
            }

            .story-card:hover {
                z-index: 10;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .story-card:hover .story-thumbnail img {
                transform: scale(1.05);
            }

            .story-categories {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                justify-content: center;
                margin-top: 8px;
            }

            .story-title {
                height: 3em;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .category-badge {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                padding: 3px 10px;
                border-radius: 12px;
                font-size: 0.75rem;
                backdrop-filter: blur(4px);
                transition: all 0.3s ease;
            }

            .category-badge:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-1px);
            }

            .story-views-time-container {
                display: flex;
                align-items: center;
                white-space: nowrap;
                justify-content: space-between;
                gap: 8px;
            }

            .story-views {
                flex-shrink: 0;
            }

            .story-time {
                flex-shrink: 0;
                text-align: right;
                white-space: nowrap;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endonce
