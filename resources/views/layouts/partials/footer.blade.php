<footer id="donate" class="mt-80">
    <div class="border-top-custom-2 bg-site">
        <div class="container text-center py-4">

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="social-icons mb-3 py-3">
                        @foreach($socials as $social)
                            <a href="{{ $social->url }}" target="_blank" class="social-icon" aria-label="{{ $social->name }}">
                                @if(strpos($social->icon, 'custom-') === 0)
                                    <span class="{{ $social->icon }}"></span>
                                @else
                                    <i class="{{ $social->icon }}"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    <div class="footer-links">
                        <a href="{{ route('home') }}" class="text-decoration-none fw-semibold fs-4 font-FzAstila">Trang Chủ</a>
                        <a href="" class="text-decoration-none fw-semibold fs-4 font-FzAstila">Điều Khoản</a>
                        <a href="{{ route('guide.show') }}" class="text-decoration-none fw-semibold fs-4 font-FzAstila">Hướng Dẫn</a>
                    </div>

                    <div class="py-3">
                        <span class="copyright color-text text-sm fw-semibold fs-4 font-FzAstila">
                            © {{ date('Y') }} - {{ config('app.name') }} Bảo Lưu Mọi Quyền
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <!-- Facebook Page Plugin -->
                    <div class="mt-4">
                        <div class="w-100">
                            <div class="fb-page" data-href="{{ \App\Models\Config::getConfig('facebook_page_url', 'https://www.facebook.com/profile.php?id=61572454674711') }}" data-small-header="false"
                                data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>


        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@vite('resources/assets/frontend/js/script.js')
@stack('scripts')
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0"
    nonce="random_nonce"></script>
</body>

</html>
