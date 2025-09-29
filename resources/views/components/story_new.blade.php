<div class="mb-2 text-gray-600">
    <div class="story-content-wrapper">
        <div class="story-image-wrapper position-relative d-inline-block">
            <img src="{{ Storage::url($story->cover) }}" class="story-image-new" alt="{{ $story->title }}">
            <span class="new-tag">NEW</span>
            @if ($story->is_18_plus === 1)
                @include('components.tag18plus')
            @endif
        </div>
        <div class="story-info-section">
            <div class="story-chapter-inline">
                <span class="fw-semibold">
                    <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none color-hover">
                        {{ $story->title }}
                    </a>
                    <span class="chapter-separator">-</span>

                    <span class="chapter-wrapper">
                        @if ($story->latestChapter)
                            <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $story->latestChapter->slug]) }}"
                                class="text-decoration-none chapter-link chapter-number">
                                Chương {{ $story->latestChapter->number }}
                            </a>
                        @else
                            <span class="text-muted">Chưa cập nhật</span>
                        @endif
                    </span>
                </span>
            </div>
        </div>

        <div class="time-info">
            <div class="text-muted text-sm mb-1 fs-8">
                @if ($story->latestChapter)
                    {{ $story->latestChapter->created_at->diffForHumans() }}
                @else
                    Chưa cập nhật
                @endif
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .chapter-number {
                color: #5e44ef;
            }

            .story-image-new {
                width: 70px;
                height: 100px;
                object-fit: cover;
                display: block;
                flex-shrink: 0;
            }

            .story-image-new:hover {
                transform: scale(1.05);
                transition: transform 0.3s ease;
            }

            .story-content-wrapper {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            .story-info-section {
                flex: 1;
                min-width: 0;
                /* Cho phép shrink */
            }

            .story-chapter-inline {
                line-height: 1.4;
            }

            .story-title {
                font-size: 1rem;
                margin: 0;
                display: inline;
                word-wrap: break-word;
                hyphens: auto;
                white-space: normal;
            }

            .story-title a {
                display: inline;
            }

            .chapter-separator {
                color: #666;
                margin: 0 3px;
                display: inline;
            }

            .chapter-wrapper {
                display: inline;
            }

            .chapter-link {
                word-wrap: break-word;
                hyphens: auto;
                line-height: inherit;
                display: inline;
            }

            .time-info {
                flex-shrink: 0;
                text-align: right;
                min-width: 80px;
            }

            .story-image-wrapper {
                position: relative;
                display: inline-block;
            }

            /* NEW Tag dính lên góc trên phải ảnh */
            .new-tag {
                position: absolute;
                top: -3px;
                right: -3px;
                background: linear-gradient(45deg, #263bdc, #5e44ef, #7c71f8, #2f26dc);
                background-size: 300% 300%;
                color: white;
                padding: 2px 6px;
                border-radius: 8px;
                font-size: 0.6rem;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                overflow: hidden;
                animation: redShimmer 2s infinite, redPulse 1.5s infinite alternate;
                box-shadow: 0 2px 8px rgba(38, 50, 220, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.3);
                z-index: 10;
            }

            .new-tag::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
                animation: shine 3s infinite;
            }

            /* Animation keyframes */
            @keyframes redShimmer {
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

            @keyframes redPulse {
                0% {
                    transform: scale(1);
                    box-shadow: 0 2px 8px rgba(38, 50, 220, 0.5);
                }

                100% {
                    transform: scale(1.05);
                    box-shadow: 0 3px 12px rgba(38, 50, 220, 0.7);
                }
            }

            @keyframes shine {
                0% {
                    left: -100%;
                }

                50% {
                    left: 100%;
                }

                100% {
                    left: 100%;
                }
            }

            /* Responsive cho NEW tag */
            @media (max-width: 767.98px) {
                .new-tag {
                    font-size: 0.5rem;
                    padding: 1px 4px;
                    top: -2px;
                    right: -2px;
                }
            }

            @media (max-width: 575.98px) {
                .new-tag {
                    font-size: 0.45rem;
                    padding: 1px 3px;
                    top: -1px;
                    right: -1px;
                }
            }

            /* Responsive */
            @media (max-width: 767.98px) {


                .time-info {
                    text-align: left;
                    min-width: auto;
                }

                .story-title {
                    font-size: 0.9rem;
                }

                /* Mobile: Ẩn dấu : */
                .chapter-separator {
                    display: none;
                }

                .new-tag {
                    font-size: 0.6rem;
                    padding: 1px 4px;
                    margin-left: 3px;
                    margin-right: 1px;
                }

                /* Mobile: Chapter number xuống hàng */
                .chapter-wrapper {
                    display: block;
                    margin-left: 0;
                    margin-top: 2px;
                }

                .chapter-link {
                    display: block;
                    margin-left: 0;
                }
            }

            @media (max-width: 575.98px) {

                /* Mobile nhỏ: Tiếp tục ẩn dấu : */
                .chapter-separator {
                    display: none;
                }

                .new-tag {
                    font-size: 0.55rem;
                    padding: 0px 3px;
                    margin-left: 2px;
                }

                /* Mobile nhỏ: Chapter number xuống hàng */
                .chapter-wrapper {
                    display: block;
                    margin-top: 3px;
                }
            }
        </style>
    @endpush
@endonce
