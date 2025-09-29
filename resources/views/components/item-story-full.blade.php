<div class="story-item-full d-flex align-items-start">
    <div class="story-image-container flex-shrink-0 me-3">
        <a href="{{ route('show.page.story', $story->slug) }}" class="d-block position-relative">
            <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                alt="{{ $story->title }}" class="img-fluid rounded-3 image-story-full-item">

            @if ($story->is_18_plus === 1)
                @include('components.tag18plus')
            @endif

            @if (isset($story) && $story->has_combo)
                @php
                    $totalChapterPrice = $story->total_chapter_price ?? 0;
                    if ($totalChapterPrice > 0) {
                        $savingPercent = round((($totalChapterPrice - $story->combo_price) / $totalChapterPrice) * 100);
                    } else {
                        $savingPercent = 0;
                    }
                @endphp
                @if ($savingPercent > 0)
                    <div class="flower-discount-badge">
                        <div class="flower-shape">
                            <div class="flower-content">
                                -{{ $savingPercent }}%
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </a>
    </div>

    <div class="story-content-container flex-grow-1 min-w-0">
        <div class="story-header">
            <h5 class="story-title mb-1 text-sm fw-semibold lh-base">
                <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none color-3">

                    {{ Str::limit($story->title, 25, '...') }}
                </a>
            </h5>
            <p class="text-ssm text-gray-600 mb-2">{{ $story->latestChapter->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="story-description">
            {{ cleanDescription($story->description, 300) }}
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .story-item-full {
                gap: 0;
                width: 100%;
            }

            .story-image-container {
                position: relative;
            }

            .story-content-container {
                overflow: hidden;
            }

            .image-story-full-item {
                width: 135px;
                height: 185px;
                object-fit: cover;
            }

            .badge-custom-full {
                border-color: #c2c2c2 !important;
                transition: all 0.3s ease;
            }

            .badge-custom-full:hover {
                background-color: var(--primary-color-3);
            }

            .badge-custom-full:hover a {
                color: white !important;
            }

            /* Story description responsive - Fixed */
            .story-description {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.4;
                font-size: 0.8rem;
                color: #666;
                word-wrap: break-word;
                word-break: break-word;
                hyphens: auto;
                max-width: 100%;
            }

            /* Remove any potential HTML formatting that could break layout */
            .story-description * {
                display: inline !important;
                margin: 0 !important;
                padding: 0 !important;
                line-height: inherit !important;
            }

            .story-description p {
                display: inline !important;
                margin: 0 !important;
            }

            .story-description br {
                display: none !important;
            }

            /* Flower discount badge */
            .flower-discount-badge {
                position: absolute;
                top: 5px;
                right: 5px;
                z-index: 10;
            }

            .flower-shape {
                position: relative;
                width: 25px;
                height: 25px;
                background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
                border-radius: 50% 10px 50% 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                transform: rotate(15deg);
                box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
                animation: flowerPulse 2s infinite ease-in-out;
            }

            .flower-shape::before {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(135deg, #ff4757, #ff6b6b);
                border-radius: 50% 10px 50% 10px;
                z-index: -1;
                animation: flowerRotate 4s infinite linear;
            }

            .flower-shape::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 16px;
                height: 16px;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
                border-radius: 50%;
                transform: translate(-50%, -50%);
            }

            .flower-content {
                color: white;
                font-size: 0.55rem;
                font-weight: bold;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
                transform: rotate(-15deg);
                z-index: 2;
            }

            @keyframes flowerPulse {
                0% {
                    transform: rotate(15deg) scale(1);
                    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
                }

                50% {
                    transform: rotate(15deg) scale(1.05);
                    box-shadow: 0 3px 12px rgba(255, 107, 107, 0.6);
                }

                100% {
                    transform: rotate(15deg) scale(1);
                    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
                }
            }

            @keyframes flowerRotate {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            /* Responsive styles */
            @media (max-width: 767.98px) {
                .story-item-full {
                    align-items: flex-start;
                }

                .story-image-container {
                    margin-right: 10px !important;
                }

                .story-description {
                    -webkit-line-clamp: 6;
                    font-size: 0.75rem;
                }

                .story-title {
                    font-size: 0.85rem;
                }

                .flower-shape {
                    width: 20px;
                    height: 20px;
                }

                .flower-content {
                    font-size: 0.5rem;
                }

                .flower-shape::after {
                    width: 12px;
                    height: 12px;
                }
            }

            @media (max-width: 575.98px) {
                .story-item-full {
                    align-items: flex-start;
                }

                .story-image-container {
                    margin-right: 8px !important;
                }

                .story-description {
                    -webkit-line-clamp: 8;
                    font-size: 0.7rem;
                }

                .story-title {
                    font-size: 0.8rem;
                }

                .flower-shape {
                    width: 18px;
                    height: 18px;
                }

                .flower-content {
                    font-size: 0.45rem;
                }

                .flower-shape::after {
                    width: 10px;
                    height: 10px;
                }
            }

            /* Desktop và tablet */
            @media (min-width: 768px) {
                .story-item-full {
                    align-items: flex-start;
                }

                .story-image-container {
                    margin-right: 15px !important;
                }

                .story-description {
                    -webkit-line-clamp: 5;
                    font-size: 0.8rem;
                }
            }

            /* Large desktop */
            @media (min-width: 1200px) {

                .story-description {

                    font-size: 0.85rem;
                }
            }
        </style>
    @endpush
@endonce
