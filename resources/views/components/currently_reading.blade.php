@if($currentlyReading->count() > 0)
<div class="sidebar-widget recent-reads rounded-4 border-5 border border-color-7 shadow-sm mt-4">
    <div class="widget-header border-bottom-0">
        <div class="text-center">
            <h2 class="fs-3 text-center m-0 text-dark fw-bold title-dark">ĐANG ĐỌC</h2>
        </div>
    </div>
    <div class="widget-content px-md-4 px-2">
        <div class="currently-reading-list">
            @foreach ($currentlyReading as $reading)
                <div class="currently-reading-item d-flex align-items-center p-2">
                    <div class="reading-icon me-2">
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                    <div class="reading-content flex-grow-1 d-flex justify-content-between ">
                        <h6 class="reading-title mb-1">
                            <a href="{{ route('show.page.story', $reading->story->slug) }}" 
                               class="text-decoration-none text-dark fs-5">
                                {{ $reading->story->title }}
                            </a>
                        </h6>
                        <div class="reading-chapter">
                            <a href="{{ route('chapter', ['storySlug' => $reading->story->slug, 'chapterSlug' => $reading->chapter->slug]) }}" 
                               class="text-decoration-none text-primary fs-5">
                                Đọc tiếp C {{ $reading->chapter->number }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@once
    @push('styles')
        <style>
            .currently-reading-item {
                transition: background-color 0.2s;
            }

            .currently-reading-item:hover {
                background-color: rgba(0, 0, 0, 0.03);
            }

            .reading-title {
                font-size: 0.9rem;
                line-height: 1.3;
                max-height: 2.6rem;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .reading-chapter {
                font-size: 0.8rem;
            }

            .reading-icon {
                width: 20px;
                text-align: center;
            }

            /* Dark mode styles */
            body.dark-mode .sidebar-widget {
                background-color: #2d2d2d !important;
                border-color: #404040 !important;
            }

            body.dark-mode .widget-header {
                background-color: #404040 !important;
            }

            body.dark-mode .currently-reading-item {
                border-color: #404040 !important;
            }

            body.dark-mode .currently-reading-item:hover {
                background-color: rgba(255, 255, 255, 0.05) !important;
            }

            body.dark-mode .reading-title a {
                color: #e0e0e0 !important;
            }

            body.dark-mode .reading-title a:hover {
                color: var(--primary-color-3) !important;
            }
        </style>
    @endpush
@endonce
