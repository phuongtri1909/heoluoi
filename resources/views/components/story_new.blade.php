<div class="mb-2 d-flex ">
    <a href="{{ route('show.page.story', $story->slug) }}"
        class="story-image-wrapper position-relative d-inline-block rounded-3">
        <img src="{{ Storage::url($story->cover) }}" class="story-image-new rounded-2 me-3" alt="{{ $story->title }}">
       
        @if($story->completed == true)
            <span class="full-label"></span>
        @endif
    </a>
    <div class="story-info-section">
        <a href="{{ route('show.page.story', $story->slug) }}"
            class="text-decoration-none color-hover fs-5 fw-semibold line-height-05">
            {{ $story->title }}
        </a>
        <div class="story-chapter-inline">

            <div class="author-chapter-container">
                @if ($story->author_name)
                    <p class="mb-0 fs-6 fw-semibold">{{ $story->author_name }}</p>

                    <span class="chapter-separator fs-6">|</span>
                @endif


                <span class="chapter-wrapper">
                    @if ($story->latestChapter)
                        <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $story->latestChapter->slug]) }}"
                            class="text-decoration-none chapter-link text-muted fs-6">
                            @php
                                $chapterNumber = $story->latestChapter->number;
                                $chapterTitle = $story->latestChapter->title;

                                $hasCustomTitle =
                                    !empty($chapterTitle) &&
                                    $chapterTitle !== "Chương {$chapterNumber}" &&
                                    $chapterTitle !== "Chapter {$chapterNumber}";

                                if ($hasCustomTitle) {
                                    echo "Chương {$chapterNumber}: <span class='chapter-title-text'>{$chapterTitle}</span>";
                                } else {
                                    echo "Chương {$chapterNumber}";
                                }
                            @endphp
                        </a>
                    @else
                        <span class="text-muted">Chưa cập nhật</span>
                    @endif
                </span>
            </div>


            <div class="text-muted text-sm mb-2 fs-6">
                <span class="text-dark fs-6 fw-semibold">Cập nhật:</span>
                @if ($story->latestChapter)
                    {{ $story->latestChapter->created_at->diffForHumans() }}
                @else
                    Chưa cập nhật
                @endif
            </div>

            @include('components.category_badges', ['story' => $story])

        </div>
    </div>


</div>

@once
    @push('styles')
        <style>
            .full-label {
                width: 34px;
                height: 50px;
                position: absolute;
                display: block;
                top: 55%;
                left: -6%;
                z-index: 1;
                background: transparent url(/images/defaults/full-label.png) no-repeat;
            }

            .chapter-number {
                color: #5e44ef;
            }

            .story-image-new {
                width: 90px;
                height: 120px;
                object-fit: cover;
                display: block;
                flex-shrink: 0;
            }

            .story-image-wrapper:hover {
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
                display: flex;
                flex-direction: column;
                gap: 4px;
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
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .chapter-title-text {
                display: inline-block;
                max-width: 200px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                vertical-align: top;
            }

            .author-chapter-container {
                display: flex;
                align-items: center;
                flex-wrap: nowrap;
            }

            .author-chapter-container p {
                flex-shrink: 0;
                margin-right: 8px;
            }

            .chapter-separator {
                flex-shrink: 0;
                margin: 0 8px;
            }

            .chapter-wrapper {
                flex: 1;
                min-width: 0;
                overflow: hidden;
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
                    display: inline;
                    margin-left: 0;
                    white-space: nowrap;
                }

                .author-chapter-container {
                    flex-wrap: wrap;
                    gap: 4px;
                }

                .author-chapter-container p {
                    margin-right: 4px;
                }

                .chapter-separator {
                    margin: 0 4px;
                }

                .chapter-title-text {
                    max-width: 120px;
                    /* Mobile: 120px */
                }
            }

            @media (max-width: 575.98px) {


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

                .author-chapter-container {
                    flex-wrap: wrap;
                    gap: 2px;
                }

                .author-chapter-container p {
                    margin-right: 2px;
                }

                .chapter-separator {
                    margin: 0 2px;
                }

                .chapter-title-text {
                    max-width: 100px;
                    /* Mobile nhỏ: 100px */
                }
            }
        </style>
    @endpush
@endonce
