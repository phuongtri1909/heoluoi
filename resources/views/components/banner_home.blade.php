@props(['banners'])

@if ($banners && $banners->count() > 0)
    <section class="banner-carousel-section container">
        <div class="slider" id="bannerSlider">
            @foreach ($banners as $index => $banner)
                <div class="slide">
                    <a href="{{ $banner->link ?? route('show.page.story', $banner->story->slug) }}"
                        rel="noopener noreferrer">
                        <img src="{{ asset('storage/' . $banner->image) ?? asset('assets/images/banner_default.jpg') }}"
                            alt="Banner Image" loading="lazy">
                        @if ($banner->story && $banner->story->is_18_plus === 1)
                            @include('components.tag18plus')
                        @endif
                    </a>
                </div>
            @endforeach
            <button class="nav prev pe-0">&#10094;</button>
            <button class="nav next pe-0">&#10095;</button>
        </div>
    </section>

    @once
        @push('styles')
            <style>
                .banner-carousel-section {
                    font-size: 3rem;
                    color: var(--primary);
                    padding: 2rem 0 0 0;
                    overflow: hidden;
                }

                .slider {
                    position: relative;
                    width: 100%;
                    height: 270px;
                    perspective: 1500px;
                    overflow: hidden;
                    user-select: none;
                }

                .slide {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 70%;
                    height: auto;
                    border-radius: 16px;
                    overflow: hidden;
                    background: #fff;
                    transform: translate(-50%, -50%);
                    transition: transform .45s cubic-bezier(.2, .8, .2, 1),
                        opacity .45s cubic-bezier(.2, .8, .2, 1),
                        z-index .45s;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 100%;
                    max-height: 100%;
                }

                .slide img {
                    width: 100%;
                    height: auto;
                    object-fit: contain;
                    display: block;
                    max-height: 350px;
                }

                .slide .title {
                    position: absolute;
                    bottom: 10px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: max-content;
                    text-align: center;
                    padding: 10px 15px;
                    background: rgba(46, 39, 39, 0.4);
                    border-radius: 6px;
                    border: 2px solid rgba(165, 117, 44, 0.4);
                    box-shadow: 0 3px 28px rgba(0, 0, 0, 0.2);
                    color: #fff;
                }

                .nav {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.9);
                    border: 2px solid rgba(0, 0, 0, 0.1);
                    cursor: pointer;
                    z-index: 100;
                    font-size: 22px;
                    font-weight: bold;
                    color: #333;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                .nav:hover {
                    background: rgba(255, 255, 255, 1);
                    transform: translateY(-50%) scale(1.1);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
                }

                .nav.prev {
                    left: 20px;
                }

                .nav.next {
                    right: 20px;
                }

                /* Dark mode styles */
                body.dark-mode .banner-carousel-section {
                    background-color: transparent;
                }

                body.dark-mode .slide .title {
                    background: rgba(45, 45, 45, 0.8) !important;
                    border-color: rgba(216, 107, 107, 0.6) !important;
                    color: #e0e0e0 !important;
                }

                body.dark-mode .nav {
                    background: rgba(45, 45, 45, 0.9);
                    color: #e0e0e0;
                    border-color: rgba(255, 255, 255, 0.2);
                }

                body.dark-mode .nav:hover {
                    background: rgba(45, 45, 45, 1);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
                }

                @media (max-width: 768px) {
                    .slider {
                        height: 200px;
                    }

                    .slide {
                        width: 80%;
                        max-width: 80%;
                    }

                    .slide img {
                        max-height: 300px;
                    }

                    .nav {
                        width: 40px;
                        height: 40px;
                        font-size: 18px;
                    }

                    .nav.prev {
                        left: 10px;
                    }

                    .nav.next {
                        right: 10px;
                    }

                    .slide .title {
                        font-size: 0.9rem;
                        padding: 8px 12px;
                        bottom: 8px;
                    }
                }

                @media (max-width: 576px) {
                    .slider {
                        height: 170px;
                    }

                    .slide {
                        width: 85%;
                        max-width: 85%;
                    }

                    .slide img {
                        max-height: 250px;
                    }

                    .nav {
                        width: 35px;
                        height: 35px;
                        font-size: 16px;
                    }

                    .nav.prev {
                        left: 5px;
                    }

                    .nav.next {
                        right: 5px;
                    }

                    .slide .title {
                        font-size: 0.8rem;
                        padding: 6px 10px;
                        bottom: 6px;
                    }
                }

                @media (max-width: 480px) {
                    .slider {
                        height: 150px;
                    }

                    .slide {
                        width: 90%;
                        max-width: 90%;
                    }

                    .slide img {
                        max-height: 220px;
                    }

                    .nav {
                        width: 30px;
                        height: 30px;
                        font-size: 14px;
                    }

                    .nav.prev {
                        left: 2px;
                    }

                    .nav.next {
                        right: 2px;
                    }

                    .slide .title {
                        font-size: 0.7rem;
                        padding: 4px 8px;
                        bottom: 4px;
                    }
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const slider = document.getElementById('bannerSlider');
                    const slides = Array.from(slider.querySelectorAll('.slide'));
                    const prevBtn = slider.querySelector('.prev');
                    const nextBtn = slider.querySelector('.next');
                    let active = Math.floor(slides.length / 2);

                    function update() {
                        const isMobile = window.innerWidth <= 768;
                        const isSmallMobile = window.innerWidth <= 480;
                        const gap = isSmallMobile ? 80 : isMobile ? 100 : 110;
                        const n = slides.length;

                        slides.forEach((slide, i) => {
                            let offset = i - active;
                            if (offset > n / 2) offset -= n;
                            if (offset < -n / 2) offset += n;

                            const abs = Math.abs(offset);
                            let scale, opacity, zIndex;

                            if (abs === 0) {
                                scale = 1;
                                opacity = 1;
                                zIndex = 10;
                            } else if (abs === 1) {
                                scale = 0.9;
                                opacity = 0.9;
                                zIndex = 9;
                            } else if (abs === 2) {
                                scale = 0.8;
                                opacity = 0.75;
                                zIndex = 8;
                            } else if (abs === 3) {
                                scale = 0.7;
                                opacity = 0.6;
                                zIndex = 7;
                            } else {
                                scale = 0.5;
                                opacity = 0;
                                zIndex = 0;
                            }

                            slide.style.zIndex = zIndex;
                            slide.style.opacity = opacity;
                            slide.style.transform =
                                `translate(-50%,-50%) translateX(${offset * gap}px) scale(${scale})`;
                        });
                    }

                    function prev() {
                        active = (active - 1 + slides.length) % slides.length;
                        update();
                    }

                    function next() {
                        active = (active + 1) % slides.length;
                        update();
                    }

                    prevBtn.addEventListener('click', prev);
                    nextBtn.addEventListener('click', next);

                    let startX = 0;
                    let isDown = false;

                    function down(e) {
                        isDown = true;
                        startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
                    }

                    function up(e) {
                        if (!isDown) return;
                        isDown = false;
                        const endX = e.type.includes('mouse') ? e.clientX : e.changedTouches[0].clientX;
                        const diff = endX - startX;
                        if (diff > 50) prev();
                        else if (diff < -50) next();
                    }

                    slider.addEventListener('mousedown', down);
                    slider.addEventListener('touchstart', down);
                    slider.addEventListener('mouseup', up);
                    slider.addEventListener('mouseleave', () => {
                        isDown = false;
                    });
                    slider.addEventListener('touchend', up);

                    window.addEventListener('resize', update);

                    let autoplay = setInterval(next, 5000);

                    slider.addEventListener('mouseenter', () => clearInterval(autoplay));
                    slider.addEventListener('mouseleave', () => autoplay = setInterval(next, 5000));

                    update();
                });
            </script>
        @endpush
    @endif
@endonce