<section class="mt-4">
    <h2 class="fs-5 m-0 color-8 title-dark fs-2 text-center fw-bold font-FzAstila"> TRUYỆN ĐỀ CỬ</h2>
    <div class="mt-4 bg-list rounded-2 p-2 position-relative">
        <div id="storiesContainer" class="rounded-bottom-custom">
            <div class="featured-stories-grid" id="featuredStoriesGrid">
                @forelse ($hotStories as $story)
                    <div class="featured-grid-item">
                        @include('components.stories-grid', ['story' => $story])
                    </div>
                @empty
                    <div class="featured-grid-empty">
                        <div class="alert alert-info text-center py-4 mb-4">
                            <i class="fas fa-book-open fa-2x mb-3 text-muted"></i>
                            <h5 class="mb-1">Không tìm thấy truyện nào</h5>
                            <p class="text-muted mb-0">Hiện không có truyện nào trong danh mục này.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@once
    @push('styles')
        <style>
            /* Grid Truyện đề cử: mobile 4 cột x 4 dòng, desktop 8 cột x 2 dòng */
            .featured-stories-grid {
                display: grid;
                gap: .5rem;
                grid-template-columns: repeat(4, 1fr);
            }

            @media (min-width: 768px) {
                .featured-stories-grid {
                    grid-template-columns: repeat(8, 1fr);

                }
            }

            .featured-grid-item {
                min-width: 0;
            }

            .featured-grid-empty {
                grid-column: 1 / -1;
            }

            .rounded-bottom-custom {
                border-bottom-left-radius: 1rem !important;
                border-bottom-right-radius: 1rem !important;
            }

            .rounded-top-custom {
                border-top-left-radius: 1rem !important;
                border-top-right-radius: 1rem !important;
            }

            #storiesContainer.loading {
                opacity: 0.6;
                pointer-events: none;
            }

            /* Dark mode styles */
            body.dark-mode .bg-list {
                background-color: #2d2d2d !important;
            }

            body.dark-mode .alert-info {
                background-color: rgba(13, 202, 240, 0.2) !important;
                border-color: #0dcaf0 !important;
                color: #0dcaf0 !important;
            }
        </style>
    @endpush
@endonce
