<section class="mt-4">
    <h2 class="fs-5 m-0 text-dark title-dark fs-2 text-center"> TRUYỆN ĐỀ CỬ</h2>
    <div class="mt-4 bg-list rounded-4 px-0 p-md-4 pb-4 border-5 border border-color-7 position-relative">
        <!-- Navigation Buttons -->
        <button class="slider-nav-btn slider-nav-prev" id="prevBtn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-nav-btn slider-nav-next" id="nextBtn">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div id="storiesContainer" class="rounded-bottom-custom">
            <div class="slider-wrapper">
                <div class="slider-track" id="sliderTrack">
                    @forelse ($hotStories as $story)
                        <div class="slider-item">
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
    </div>
</section>

@once
    @push('styles')
        <style>
            /* Slider Styles */
            .slider-wrapper {
                overflow: hidden;
                position: relative;
            }

            .slider-track {
                display: flex;
                transition: transform 0.5s ease-in-out;
                gap: 1rem;
            }

            .slider-item {
                flex: 0 0 auto;
                width: calc(50% - 0.5rem);
                min-width: 120px;
            }

            @media (min-width: 576px) {
                .slider-item {
                    width: calc(25% - 0.75rem);
                }
            }

            @media (min-width: 768px) {
                .slider-item {
                    width: calc(20% - 0.8rem);
                }
            }

            @media (min-width: 992px) {
                .slider-item {
                    width: calc(14.28% - 0.86rem);
                }
            }

            /* Navigation Buttons */
            .slider-nav-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: var(--primary-color-7);
                color: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 10;
                transition: all 0.3s ease;
                font-size: 14px;
            }

            .slider-nav-btn:hover {
                background: var(--primary-color-3);
                transform: translateY(-50%) scale(1.1);
            }

            .slider-nav-prev {
                left: 0px;
            }

            .slider-nav-next {
                right: 0px;
            }

            /* Hide buttons on mobile */
            @media (max-width: 576px) {
                .slider-nav-btn {
                    display: none;
                }
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

            body.dark-mode .slider-nav-btn {
                background: rgba(255, 255, 255, 0.2);
                color: #fff;
            }

            body.dark-mode .slider-nav-btn:hover {
                background: rgba(255, 255, 255, 0.3);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sliderTrack = document.getElementById('sliderTrack');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const sliderItems = document.querySelectorAll('.slider-item');
                
                if (!sliderTrack || !prevBtn || !nextBtn || sliderItems.length === 0) {
                    return;
                }

                let currentIndex = 0;
                let itemsPerView = getItemsPerView();
                let maxIndex = Math.max(0, sliderItems.length - itemsPerView);

                function getItemsPerView() {
                    const width = window.innerWidth;
                    if (width >= 992) return 7;
                    if (width >= 768) return 5;
                    if (width >= 576) return 4;
                    return 2;
                }

                function updateSlider() {
                    if (sliderItems.length === 0) return;
                    
                    if (sliderItems.length <= itemsPerView) {
                        prevBtn.style.display = 'none';
                        nextBtn.style.display = 'none';
                        return;
                    } else {
                        prevBtn.style.display = 'flex';
                        nextBtn.style.display = 'flex';
                    }
                    
                    const itemWidth = sliderItems[0].offsetWidth + 16;
                    const translateX = -currentIndex * itemWidth;
                    sliderTrack.style.transform = `translateX(${translateX}px)`;
                    
                    prevBtn.style.opacity = '1';
                    nextBtn.style.opacity = '1';
                    prevBtn.disabled = false;
                    nextBtn.disabled = false;
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % (maxIndex + 1);
                    console.log('Next - currentIndex:', currentIndex, 'maxIndex:', maxIndex, 'items:', sliderItems.length, 'itemsPerView:', itemsPerView);
                    updateSlider();
                }

                function prevSlide() {
                    currentIndex = currentIndex === 0 ? maxIndex : currentIndex - 1;
                    console.log('Prev - currentIndex:', currentIndex, 'maxIndex:', maxIndex, 'items:', sliderItems.length, 'itemsPerView:', itemsPerView);
                    updateSlider();
                }

                nextBtn.addEventListener('click', nextSlide);
                prevBtn.addEventListener('click', prevSlide);

                window.addEventListener('resize', function() {
                    itemsPerView = getItemsPerView();
                    maxIndex = Math.max(0, sliderItems.length - itemsPerView);
                    currentIndex = Math.min(currentIndex, maxIndex);
                    updateSlider();
                });

                let startX = 0;
                let isDragging = false;

                sliderTrack.addEventListener('touchstart', function(e) {
                    startX = e.touches[0].clientX;
                    isDragging = true;
                });

                sliderTrack.addEventListener('touchmove', function(e) {
                    if (!isDragging) return;
                    e.preventDefault();
                });

                sliderTrack.addEventListener('touchend', function(e) {
                    if (!isDragging) return;
                    isDragging = false;
                    
                    const endX = e.changedTouches[0].clientX;
                    const diffX = startX - endX;
                    
                    if (Math.abs(diffX) > 50) {
                        if (diffX > 0) {
                            nextSlide();
                        } else {
                            prevSlide();
                        }
                    }
                });

                setTimeout(function() {
                    updateSlider();
                }, 100);
            });
        </script>
    @endpush
@endonce
