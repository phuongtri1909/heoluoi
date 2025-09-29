@push('styles')
    <style>
        #banner-video {
            height: 100vh;
            width: 100%;
        }

        .banner-video {
            height: 100%;
        }

        .video-container {
            height: 100%;
            width: 100%;
            position: relative;
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        /* conten banner */
        .content-banner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            z-index: 2;
        }

        .title-banner {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
    </style>

@endpush

@push('scripts')
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let video = document.getElementById("video-banner");
                let source = video.querySelector('source');

                let observer = new IntersectionObserver(function(entries) {
                    if (entries[0].isIntersecting) {
                        source.src = source.getAttribute('data-src');
                        video.load();
                        video.play();
                        observer.disconnect();
                    }
                }, {
                    rootMargin: '200px 0px'
                });

                observer.observe(video);
            });
        </script>
    @endpush

    <section id="banner-video">
        <div class="banner-video position-relative overflow-hidden">
            <div class="video-container">
                <video id="video-banner" loading="lazy" preload="auto" playsinline muted loop
                    poster="{{ asset('assets/images/banner_conduongbachu.webp') }}">
                    <source data-src="{{ asset('assets/videos/video_conduongbachu.mp4') }}" type="video/mp4">
                </video>
                <div class="overlay"></div>
            </div>

            <div class="content-banner text-center">
                <div class="container ">
                    <h2 class="title-banner text-white mb-4">CON ĐƯỜNG BÁ CHỦ</h2>

                    <div class="search-wrapper">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm kiếm chương...">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="first-chapter text-white text-center mt-3">
                    <p class="fs-5 mb-0">Đọc từ đầu</p>
                    <h4>CHƯƠNG 1: HAI SỐ PHẬN</h4>
                </div>

            </div>
        </div>
    </section>
