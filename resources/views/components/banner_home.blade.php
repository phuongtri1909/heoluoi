@props(['banners'])

@if ($banners && $banners->count() > 0)
    <section class="banner-carousel-section py-4 container">
        <div class="swiper-container">
            <div class="swiper banner-home-swiper">
                <div class="swiper-wrapper">
                    @foreach ($banners as $banner)
                        <div class="swiper-slide">
                            <div class="slide-content">
                                <a href="{{ $banner->link ?? route('show.page.story', $banner->story->slug) }}"
                                    rel="noopener noreferrer">
                                    <img src="{{ asset('storage/' . $banner->image) ?? asset('assets/images/banner_default.jpg') }}"
                                        alt="{{ $banner->alt_text ?? 'Banner Image' }}" loading="lazy">
                                    @if ($banner->story && $banner->story->is_18_plus === 1)
                                        @include('components.tag18plus')
                                    @endif
                                </a>
                                @if ($banner->title)
                                    <div class="title">
                                        <span>{{ $banner->title }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>

    @once
        @push('styles')
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
            <style>
                .banner-carousel-section {
                    font-size: 3rem;
                    color: var(--primary);
                    padding: 2rem 0;
                }

                .swiper-container {
                    position: relative;
                }

                .banner-home-swiper {
                    width: 100%;
                    padding: -70px 0;
                }

                .banner-home-swiper .swiper-slide {
                    width: 300px;
                    height: 404px;
                    position: relative;
                    border-radius: 10px;
                }

                .banner-home-swiper .swiper-slide img {
                    width: 300px;
                    height: 404px;
                    border-radius: 15px;
                    object-fit: cover;
                }

                .banner-home-swiper .title {
                    position: absolute;
                    bottom: 5px;
                    left: 50%;
                    transform: translate(-50%, -20%);
                    width: max-content;
                    text-align: center;
                    padding: 10px 15px;
                    background: rgba(46, 39, 39, 0.4);
                    border-radius: 6px;
                    border: 2px solid rgba(165, 117, 44, 0.4);
                    box-shadow: 0 3px 28px rgba(0, 0, 0, 0.2);
                    color: #fff;
                }

                .banner-home-swiper .swiper-slide-active .title {
                    box-shadow: 0 20px 30px 2px rgba(165, 117, 44, 0.4);
                }

                @media (min-width: 760px) {

                    .swiper-button-prev,
                    .swiper-button-next {
                        display: flex;
                    }
                }

                .swiper-pagination-bullet-active {
                    background: #facece;
                }

                .swiper-slide-active .title {
                    display: initial;
                }

                .swiper-button-next,
                .swiper-button-prev {
                    color: #facece !important;
                    transition: all .2s ease;
                }

                .swiper-button-next:hover,
                .swiper-button-prev:hover {
                    color: #900 !important;
                }

                /* Dark mode styles */
                body.dark-mode .banner-carousel-section {
                    background-color: transparent;
                }

                body.dark-mode .banner-home-swiper .title {
                    background: rgba(45, 45, 45, 0.8) !important;
                    border-color: rgba(216, 107, 107, 0.6) !important;
                    color: #e0e0e0 !important;
                }

                body.dark-mode .banner-home-swiper .swiper-slide-active .title {
                    box-shadow: 0 20px 30px 2px rgba(216, 107, 107, 0.4) !important;
                }

                body.dark-mode .swiper-pagination-bullet-active {
                    background: var(--primary-color-3) !important;
                }

                body.dark-mode .swiper-button-next,
                body.dark-mode .swiper-button-prev {
                    color: var(--primary-color-3) !important;
                }

                body.dark-mode .swiper-button-next:hover,
                body.dark-mode .swiper-button-prev:hover {
                    color: var(--primary-color-1) !important;
                }
            </style>
        @endpush

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const bannerSwiper = new Swiper('.banner-home-swiper', {
                        autoplay: {
                            delay: 25000000,
                            disableOnInteraction: false,
                        },
                        effect: 'coverflow',
                        grabCursor: true,
                        centeredSlides: true,
                        loop: true,
                        slidesPerView: 'auto',
                        coverflowEffect: {
                            rotate: 0,
                            stretch: 0,
                            depth: 100,
                            modifier: 2.5,
                            slideShadows: false
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        }
                    });
                });
            </script>
        @endpush
    @endif
@endonce
