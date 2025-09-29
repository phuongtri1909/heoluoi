<section>
    <div class="mt-4 bg-list rounded-4 px-0 p-md-2">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center p-3 rounded-top-custom">
            <h2 class="fs-5 m-0 text-dark fw-bold title-dark d-flex">
                <span class="new-release-tag">NEW</span>
                Mới Phát Hành
            </h2>
            <div>
                <a class="color-3 text-decoration-none" href="{{ route('story.new') }}">
                    Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Stories Slider -->
        <div id="storiesContainerNewSlide" class="rounded-bottom-custom">
            @if($newStories->count() > 0)
                <div class="position-relative">
                    <!-- Desktop Swiper Container -->
                    <div class="swiper newStoriesSwiper d-none d-md-block">
                        <div class="swiper-wrapper">
                            @foreach($newStories->chunk(6) as $slideIndex => $slideStories)
                                <div class="swiper-slide">
                                    <div class="row g-3">
                                        @foreach($slideStories as $index => $story)
                                            <div class="col-6">
                                                <div class="story-new-item story-item">
                                                    @include('components.item-story-new', ['story' => $story])
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($newStories->count() > 6)
                            <div class="swiper-pagination new-stories-pagination"></div>
                        @endif
                    </div>

                    <!-- Mobile Swiper Container -->
                    <div class="swiper newStoriesSwiper-mobile d-block d-md-none">
                        <div class="swiper-wrapper">
                            @foreach($newStories->chunk(3) as $slideIndex => $slideStories)
                                <div class="swiper-slide">
                                    <div class="mobile-story-container">
                                        @foreach($slideStories as $index => $story)
                                            <div class="mobile-story-item">
                                                <div class="story-new-item story-item">
                                                    @include('components.item-story-new', ['story' => $story])
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($newStories->count() > 3)
                            <div class="swiper-pagination new-stories-pagination-mobile mt-3"></div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center py-4 mb-4">
                    <i class="fas fa-book-open fa-2x mb-3 text-muted"></i>
                    <h5 class="mb-1">Không tìm thấy truyện nào</h5>
                    <p class="text-muted mb-0">Hiện không có truyện nào trong danh mục này.</p>
                </div>
            @endif
        </div>
    </div>
</section>

@once
    @push('styles')
        <style>
            /* NEW Release Tag - Style khác biệt */
            .new-release-tag {
                display: inline-block;
                background: linear-gradient(135deg, #b91c1c, #dc2626, #ef4444, #b91c1c);
                background-size: 200% 200%;
                color: white;
                padding: 4px 10px;
                border-radius: 16px;
                font-size: 0.75rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                margin-right: 8px;
                position: relative;
                overflow: hidden;
                animation: gradientMove 3s ease-in-out infinite, glow 2s ease-in-out infinite alternate;
                box-shadow: 0 2px 8px rgba(185, 28, 28, 0.3),
                           0 0 15px rgba(220, 38, 38, 0.2);
                border: 2px solid rgba(255, 255, 255, 0.4);
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            }

            .new-release-tag::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
                animation: sweep 2.5s infinite;
            }

            .new-release-tag::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                animation: ripple 3s infinite;
            }

            /* Animation keyframes cho new release tag */
            @keyframes gradientMove {
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

            @keyframes glow {
                0% {
                    box-shadow: 0 2px 8px rgba(185, 28, 28, 0.3),
                               0 0 15px rgba(220, 38, 38, 0.2);
                    transform: scale(1);
                }
                100% {
                    box-shadow: 0 3px 12px rgba(185, 28, 28, 0.5),
                               0 0 25px rgba(220, 38, 38, 0.4);
                    transform: scale(1.02);
                }
            }

            @keyframes sweep {
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

            @keyframes ripple {
                0% {
                    width: 0;
                    height: 0;
                    opacity: 1;
                }
                50% {
                    width: 20px;
                    height: 20px;
                    opacity: 0.5;
                }
                100% {
                    width: 30px;
                    height: 30px;
                    opacity: 0;
                }
            }

            /* Responsive cho new release tag */
            @media (max-width: 768px) {
                .new-release-tag {
                    font-size: 0.7rem;
                    padding: 3px 8px;
                    margin-right: 6px;
                }
            }

            @media (max-width: 575px) {
                .new-release-tag {
                    font-size: 0.65rem;
                    padding: 2px 6px;
                    margin-right: 4px;
                }
            }

            /* Existing styles */
            .newStoriesSwiper,
            .newStoriesSwiper-mobile {
                padding: 0 20px 50px 20px;
                overflow: hidden;
                cursor: grab;
            }

            .newStoriesSwiper:active,
            .newStoriesSwiper-mobile:active {
                cursor: grabbing;
            }

            .newStoriesSwiper .swiper-slide,
            .newStoriesSwiper-mobile .swiper-slide {
                height: auto;
                display: flex;
                align-items: stretch;
            }

            .newStoriesSwiper .swiper-slide .row {
                width: 100%;
                margin: 0;
            }

            /* Mobile Story Container - Flexbox Layout */
            .mobile-story-container {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                width: 100%;
                align-items: center;
            }

            .mobile-story-item {
                width: 100%;
                max-width: 400px;
            }

            /* Story Item Styles */
            .story-item {
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.6s ease forwards;
            }

            .story-item:nth-child(1) { animation-delay: 0.1s; }
            .story-item:nth-child(2) { animation-delay: 0.2s; }
            .story-item:nth-child(3) { animation-delay: 0.3s; }
            .story-item:nth-child(4) { animation-delay: 0.4s; }
            .story-item:nth-child(5) { animation-delay: 0.5s; }
            .story-item:nth-child(6) { animation-delay: 0.6s; }

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

            .story-new-item {
                border-radius: 8px;
                transition: all 0.3s ease;
                height: 100%;
                pointer-events: none;
            }

            .story-new-item a,
            .story-new-item button {
                pointer-events: auto;
            }

            /* Pagination - Đưa xuống thấp hơn */
            .new-stories-pagination,
            .new-stories-pagination-mobile {
                bottom: 10px !important;
                text-align: center;
                position: relative !important;
                margin-top: 15px;
            }

            .new-stories-pagination .swiper-pagination-bullet,
            .new-stories-pagination-mobile .swiper-pagination-bullet {
                width: 8px;
                height: 8px;
                background: #ccc;
                opacity: 1;
                transition: all 0.3s ease;
                margin: 0 3px;
            }

            .new-stories-pagination .swiper-pagination-bullet-active,
            .new-stories-pagination-mobile .swiper-pagination-bullet-active {
                background: #39cde0;
                transform: scale(1.2);
            }

            /* Responsive */
            @media (max-width: 767.98px) {
                .newStoriesSwiper-mobile {
                    padding: 0 15px 45px 15px;
                }

                .story-new-item {
                    padding: 12px;
                }

                .mobile-story-container {
                    gap: 0.75rem;
                }

                /* Pagination cho mobile */
                .new-stories-pagination-mobile {
                    bottom: 3px !important;
                    margin-top: 12px;
                }
            }

            @media (max-width: 575.98px) {
                .newStoriesSwiper-mobile {
                    padding: 0 10px 40px 10px;
                }

                .mobile-story-item {
                    max-width: 100%;
                }

                .mobile-story-container {
                    gap: 0.5rem;
                }

                /* Pagination cho mobile nhỏ */
                .new-stories-pagination-mobile {
                    bottom: 2px !important;
                    margin-top: 10px;
                }

                .new-stories-pagination-mobile .swiper-pagination-bullet {
                    width: 6px;
                    height: 6px;
                    margin: 0 2px;
                }
            }

            .swiper-container {
                padding-bottom: 30px !important;
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

            body.dark-mode .new-stories-pagination .swiper-pagination-bullet,
            body.dark-mode .new-stories-pagination-mobile .swiper-pagination-bullet {
                background: #666 !important;
            }

            body.dark-mode .new-stories-pagination .swiper-pagination-bullet-active,
            body.dark-mode .new-stories-pagination-mobile .swiper-pagination-bullet-active {
                background: var(--primary-color-3) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    const totalStories = {{ $newStories->count() }};

                    // Desktop Swiper
                    const desktopSlidesNeeded = Math.ceil(totalStories / 6);
                    if (desktopSlidesNeeded > 1) {
                        const desktopSwiperElement = document.querySelector('.newStoriesSwiper');
                        if (desktopSwiperElement) {
                            const desktopSwiper = new Swiper('.newStoriesSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        loop: true,
                        allowTouchMove: true,
                        grabCursor: true,
                        touchRatio: 1,
                        touchAngle: 45,
                        simulateTouch: true,
                        resistance: true,
                        resistanceRatio: 0.85,
                        threshold: 5,
                        longSwipes: true,
                        longSwipesRatio: 0.5,
                        longSwipesMs: 300,
                        followFinger: true,
                        keyboard: {
                            enabled: true,
                            onlyInViewport: true,
                        },
                        mousewheel: false,
                        autoplay: {
                            delay: 5000,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true,
                        },
                        pagination: {
                            el: '.new-stories-pagination',
                            clickable: true,
                        },
                        navigation: false,
                        on: {
                            init: function() {
                                setTimeout(() => {
                                    const pagination = document.querySelector('.new-stories-pagination');
                                    if (pagination) {
                                        pagination.style.position = 'relative';
                                        pagination.style.marginTop = '15px';
                                    }

                                    if (this.slides && this.slides.length > 0) {
                                        this.slides.forEach(slide => {
                                            const items = slide.querySelectorAll('.story-item');
                                            items.forEach((item, index) => {
                                                item.style.animationDelay = `${(index + 1) * 0.1}s`;
                                            });
                                        });
                                    }
                                }, 100);
                            },
                            slideChange: function() {
                                if (this.slides && this.slides.length > 0 && this.activeIndex !== undefined) {
                                    const activeSlide = this.slides[this.activeIndex];
                                    if (activeSlide) {
                                        const items = activeSlide.querySelectorAll('.story-item');
                                        items.forEach((item, index) => {
                                            item.style.animation = 'none';
                                            item.offsetHeight;
                                            item.style.animation = `fadeInUp 0.6s ease forwards`;
                                            item.style.animationDelay = `${(index + 1) * 0.1}s`;
                                        });
                                    }
                                }
                            }
                        }
                    });
                    console.log('Desktop Swiper initialized successfully');
                    } else {
                        console.log('Desktop Swiper element not found');
                    }
                } else {
                    console.log('Desktop Swiper not needed (only 1 slide)');
                }

                // Mobile Swiper
                const mobileSlidesNeeded = Math.ceil(totalStories / 3);
                if (mobileSlidesNeeded > 1) {
                    const mobileSwiperElement = document.querySelector('.newStoriesSwiper-mobile');
                    if (mobileSwiperElement) {
                        const mobileSwiper = new Swiper('.newStoriesSwiper-mobile', {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        loop: true,
                        autoplay: {
                            delay: 5000,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true,
                        },
                        pagination: {
                            el: '.new-stories-pagination-mobile',
                            clickable: true,
                        },
                        navigation: false,
                        on: {
                            init: function() {
                                setTimeout(() => {
                                    const pagination = document.querySelector('.new-stories-pagination-mobile');
                                    if (pagination) {
                                        pagination.style.position = 'relative';
                                        pagination.style.marginTop = '12px';
                                    }

                                    if (this.slides && this.slides.length > 0) {
                                        this.slides.forEach(slide => {
                                            const items = slide.querySelectorAll('.story-item');
                                            items.forEach((item, index) => {
                                                item.style.animationDelay = `${(index + 1) * 0.1}s`;
                                            });
                                        });
                                    }
                                }, 100);
                            },
                            slideChange: function() {
                                if (this.slides && this.slides.length > 0 && this.activeIndex !== undefined) {
                                    const activeSlide = this.slides[this.activeIndex];
                                    if (activeSlide) {
                                        const items = activeSlide.querySelectorAll('.story-item');
                                        items.forEach((item, index) => {
                                            item.style.animation = 'none';
                                            item.offsetHeight;
                                            item.style.animation = `fadeInUp 0.6s ease forwards`;
                                            item.style.animationDelay = `${(index + 1) * 0.1}s`;
                                        });
                                    }
                                }
                            }
                        }
                    });
                    }
                }
                } catch (error) {
                    console.error('Error initializing Swiper:', error);
                }
            });
        </script>
    @endpush
@endonce
