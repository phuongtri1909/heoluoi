@if($story->user->facebook_link && ($story->user->role === 'author' || $story->user->role === 'admin'))
    <!-- Debug: Facebook link found: {{ $story->user->facebook_link }} -->
    <div class="author-facebook-widget mb-4">
        <div class="widget-header d-flex align-items-center mb-3">
            <i class="fab fa-facebook-f me-2 color-3"></i>
            <h6 class="mb-0 text-dark fw-bold">Facebook của tác giả</h6>
        </div>
        
        <div class="facebook-page-container">
            @php
                $isGroup = strpos($story->user->facebook_link, '/groups/') !== false;
            @endphp
            
            @if($isGroup)
                <!-- Hiển thị button cho Facebook Group -->
                <div class="facebook-group-container">
                    <a href="{{ $story->user->facebook_link }}" target="_blank" rel="noopener noreferrer" 
                       class="facebook-link-btn d-flex align-items-center justify-content-center text-decoration-none">
                        <i class="fab fa-facebook-f me-2"></i>
                        <span>Tham gia nhóm Facebook</span>
                        <i class="fas fa-external-link-alt ms-2"></i>
                    </a>
                    <div class="facebook-group-info mt-2">
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            Nhóm Facebook của tác giả
                        </small>
                    </div>
                </div>
            @else
                <!-- Hiển thị Facebook Page Plugin cho Fanpage (không có bài viết) -->
                <div class="fb-page author-fb-page" 
                     data-href="{{ $story->user->facebook_link }}" 
                     data-small-header="false"
                     data-adapt-container-width="true" 
                     data-hide-cover="false" 
                     data-show-facepile="true"
                     data-width="300"
                     data-tabs="">
                </div>
                
                <!-- Fallback link nếu Facebook Page Plugin không load -->
                <div class="facebook-fallback mt-3" style="display: none;">
                    <a href="{{ $story->user->facebook_link }}" target="_blank" rel="noopener noreferrer" 
                       class="facebook-link-btn d-flex align-items-center justify-content-center text-decoration-none">
                        <i class="fab fa-facebook-f me-2"></i>
                        <span>Tham gia Fanpage</span>
                        <i class="fas fa-external-link-alt ms-2"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

@push('styles')
    <style>
        .author-facebook-widget {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .author-facebook-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .widget-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.75rem;
        }

        .facebook-page-container {
            width: 100%;
            overflow: hidden;
        }

        .facebook-page-container .fb-page {
            width: 100% !important;
        }

        .facebook-link-btn {
            background: linear-gradient(135deg, #1877f2 0%, #42a5f5 100%);
            color: white !important;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
        }

        .facebook-link-btn:hover {
            background: linear-gradient(135deg, #166fe5 0%, #3a9ae8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
        }

        .facebook-link-btn i {
            font-size: 1.1rem;
        }

        /* Dark mode styles */
        body.dark-mode .author-facebook-widget {
            background: #2d2d2d !important;
            border-color: #404040 !important;
        }

        body.dark-mode .widget-header h6 {
            color: #e0e0e0 !important;
        }

        /* Facebook Page Plugin sẽ tự động adapt với dark mode */
    </style>
@endpush

@push('scripts')
    <script>
        // Đảm bảo Facebook SDK render Page Plugin cho author (chỉ cho Facebook Pages)
        function renderAuthorFacebookPage() {
            if (typeof FB !== 'undefined') {
                // Chỉ parse author Facebook page (không phải group), không parse toàn bộ
                const authorFbPage = document.querySelector('.author-fb-page');
                if (authorFbPage) {
                    // Parse chỉ element này
                    FB.XFBML.parse(authorFbPage);
                    
                    // Kiểm tra sau 3 giây xem Facebook Page Plugin có load không
                    setTimeout(function() {
                        const fallback = document.querySelector('.facebook-fallback');
                        
                        if (authorFbPage.children.length === 0 && fallback) {
                            // Nếu Facebook Page Plugin không load, hiển thị fallback
                            fallback.style.display = 'block';
                        }
                    }, 3000);
                }
            } else {
                // Đợi Facebook SDK load
                setTimeout(renderAuthorFacebookPage, 100);
            }
        }

        // Chạy khi DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            renderAuthorFacebookPage();
        });

        // Chạy khi window load (backup)
        window.addEventListener('load', function() {
            renderAuthorFacebookPage();
        });

        // Override fbAsyncInit nếu chưa có
        if (typeof window.fbAsyncInit === 'undefined') {
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v17.0'
                });
                FB.XFBML.parse();
            };
        }
    </script>
@endpush
