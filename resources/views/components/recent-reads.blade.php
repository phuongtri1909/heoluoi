<div class="sidebar-widget recent-reads rounded-4 shadow-sm">
    <div class="widget-header">
        <h3 class="fs-5 m-0 text-dark title-dark">
            <i class="fas fa-history text-primary me-2 fa-xl text-info"></i>Đọc Gần Đây
        </h3>
    </div>
    <div class="widget-content">
        @if (isset($recentReads) && $recentReads->count() > 0)
            @foreach ($recentReads as $reading)
                <div class="recent-story-item">
                    <div class="d-flex">
                        <div class="story-thumb-wrapper">
                            <a href="{{ route('show.page.story', $reading->story->slug) }}">
                                <img src="{{ Storage::url($reading->story->cover) }}" class="recent-story-thumb"
                                    alt="{{ $reading->story->title }}">
                            </a>
                        </div>
                        <div class="story-info">
                            <h4 class="recent-story-title" title="{{ $reading->story->title }}">
                                <a href="{{ route('show.page.story', $reading->story->slug) }}" class="text-truncate d-inline-block w-100">
                                    {{ $reading->story->title }}
                                </a>
                            </h4>
                            <div class="reading-progress">
                                <div class="progress-label">
                                    <a href="{{ route('chapter', ['storySlug' => $reading->story->slug, 'chapterSlug' => $reading->chapter->slug]) }}"
                                        class="text-muted text-decoration-none">
                                        Đọc đến: Chương {{ $reading->chapter->number }}
                                    </a>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $reading->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-3 text-muted">
                <i class="fas fa-book-reader mb-2"></i>
                <p class="mb-0">Bạn chưa đọc truyện nào</p>
            </div>
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            /* Recent Reads Widget Styles */
            .recent-story-thumb {
                width: 50px;
                height: 80px;
                object-fit: cover;
                border-radius: 4px;
            }

            .recent-story-title {
                font-size: 0.95rem;
                margin: 0;
                line-height: 1.4;
                width: 100%; /* Ensure the title container has full width */
            }

            .recent-story-title a {
                color: #333;
                text-decoration: none;
                transition: color 0.3s;
                max-width: calc(100% - 10px); /* Subtract some padding to prevent overflow */
            }

            .text-truncate {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .story-info {
                flex-grow: 1;
                min-width: 0; /* This is crucial for flexbox children to respect width constraints */
                overflow: hidden; /* Ensure content doesn't overflow */
            }

            .recent-story-title a:hover {
                color: #007bff;
            }

            .recent-story-item {
                padding: 10px;
                border-bottom: 1px solid #eee;
                transition: all 0.3s ease;
            }

            .recent-story-item:last-child {
                border-bottom: none;
            }

            .recent-story-item:hover {
                background-color: #f8f9fa;
            }

            .story-thumb-wrapper {
                margin-right: 12px;
                flex-shrink: 0;
            }

            .reading-progress {
                margin-top: 5px;
            }

            .progress-label {
                font-size: 0.75rem;
                color: #6c757d;
                margin-bottom: 3px;
            }

            .progress {
                height: 5px;
                margin-bottom: 0;
            }

            @media (max-width: 768px) {
                .recent-story-thumb {
                    width: 45px;
                    height: 60px;
                }

                .recent-story-title {
                    font-size: 0.85rem;
                }
            }
        </style>
    @endpush
@endonce
