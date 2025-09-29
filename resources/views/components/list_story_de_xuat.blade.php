<section class="container">
    <div class="mt-4 bg-list rounded-4 px-0 p-md-4 pb-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center pt-1 pb-3 rounded-top-custom">
            <h2 class="fs-5 m-0 text-dark fw-bold title-dark"><i class="fa-solid fa-check-circle" style="color: #57f17d"></i> Đề xuất</h2>
            <div>
                <a class="color-3 text-decoration-none" href="">Xem tất cả <i class="fa-regular fa-square-plus"></i></a>
            </div>
        </div>

        <!-- Stories Slider -->
        <div id="storiesContainerNewSlide" class="rounded-bottom-custom">
            <div class="swiper storyFullSwiper">
                <div class="swiper-wrapper">
                    @forelse ($featuredStories as $story)
                        <div class="swiper-slide">
                            <div class="story-item">
                                @include('components.item-story-full', ['story' => $story])
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="alert alert-info text-center py-4 mb-4">
                                <i class="fas fa-book-open fa-2x mb-3 text-muted"></i>
                                <h5 class="mb-1">Không tìm thấy truyện nào</h5>
                                <p class="text-muted mb-0">Hiện không có truyện nào trong danh mục này.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>

@once
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
        <style>
            .storyFullSwiper .swiper-button-next,
            .storyFullSwiper .swiper-button-prev {
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 50%;
                color: var(--primary-color-3);
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }

            .storyFullSwiper .swiper-button-next:hover,
            .storyFullSwiper .swiper-button-prev:hover {
                background: var(--primary-color-3);
                color: white;
                transform: scale(1.1);
            }

            .storyFullSwiper .swiper-button-next:after,
            .storyFullSwiper .swiper-button-prev:after {
                font-size: 18px;
                font-weight: bold;
            }

            .storyFullSwiper .swiper-pagination-bullet {
                width: 10px;
                height: 10px;
                transition: all 0.3s ease;
            }

            .storyFullSwiper .swiper-pagination-bullet-active {
                background: var(--primary-color-3);
                width: 24px;
                border-radius: 5px;
            }

            .story-item {
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.6s ease forwards;
            }

            /* Dark mode styles for list_story_de_xuat component */
            body.dark-mode .bg-list {
                background-color: #2d2d2d !important;
            }

            body.dark-mode .alert-info {
                background-color: rgba(13, 202, 240, 0.2) !important;
                border-color: #0dcaf0 !important;
                color: #0dcaf0 !important;
            }


            body.dark-mode .storyFullSwiper .swiper-button-next,
            body.dark-mode .storyFullSwiper .swiper-button-prev {
                background: rgba(45, 45, 45, 0.9) !important;
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .storyFullSwiper .swiper-button-next:hover,
            body.dark-mode .storyFullSwiper .swiper-button-prev:hover {
                background: var(--primary-color-3) !important;
                color: white !important;
            }
        </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
        <script>
            new Swiper('.storyFullSwiper', {
                slidesPerView: 1,
                spaceBetween: 10,
                loop: true,
                loopFillGroupWithBlank: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    540: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },

                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 20,
                    },
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                speed: 800,
                effect: "slide",
            });
        </script>
    @endpush
@endonce
