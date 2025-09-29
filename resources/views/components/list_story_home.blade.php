<section>
    <div class="mt-4 bg-list rounded-4 px-0 p-md-4 pb-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center p-3 rounded-top-custom">
            <h2 class="fs-5 m-0 text-dark fw-bold title-dark"><i class="fa-solid fa-fire fa-xl" style="color: #ef4444;"></i> Truyện Đề
                Cử</h2>
            <div>
                <a class="color-3 text-decoration-none" href="{{ route('story.hot') }}">Xem tất cả <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Stories Grid -->
        <div id="storiesContainer" class="rounded-bottom-custom">
            <div class="row gx-0 gx-md-3">
                @forelse ($hotStories as $story)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 story-item bg-none my-0 mt-3">
                        @include('components.stories-grid', ['story' => $story])
                    </div>
                @empty
                    <div class="col-12">
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
            .story-item {
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.6s ease forwards;
            }

            .story-item:nth-child(1) {
                animation-delay: 0.1s;
            }

            .story-item:nth-child(2) {
                animation-delay: 0.2s;
            }

            .story-item:nth-child(3) {
                animation-delay: 0.3s;
            }

            .story-item:nth-child(4) {
                animation-delay: 0.4s;
            }

            .story-item:nth-child(5) {
                animation-delay: 0.5s;
            }

            .story-item:nth-child(6) {
                animation-delay: 0.6s;
            }

            .rounded-bottom-custom {
                border-bottom-left-radius: 1rem !important;
                border-bottom-right-radius: 1rem !important;
            }

            .rounded-top-custom {
                border-top-left-radius: 1rem !important;
                border-top-right-radius: 1rem !important;
            }

            /* Remaining styles that can't be replaced by Bootstrap */
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
