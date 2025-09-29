<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @php
        // Get the logo and favicon from LogoSite model
        $logoSite = \App\Models\LogoSite::first();
        $logoPath =
            $logoSite && $logoSite->logo ? Storage::url($logoSite->logo) : asset('images/logo/logo-site.png');
        $faviconPath =
            $logoSite && $logoSite->favicon
                ? Storage::url($logoSite->favicon)
                : asset('favicon.ico');
    @endphp

    <title>@yield('title', 'Trang chủ - ' . config('app.name'))</title>
    <meta name="description" content="@yield('description', 'Truyện ' . config('app.name') . ' - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
    <meta name="keywords" content="@yield('keywords', 'truyện, tiểu thuyết, truyện tranh, đọc truyện online')">
    <meta name="robots" content="index, follow">
    
    @hasSection('meta')
        @yield('meta')
    @else
        <meta property="og:type" content="website">
        <meta property="og:title" content="@yield('title', 'Trang chủ - ' . config('app.name'))">
        <meta property="og:description" content="@yield('description', 'Truyện ' . config('app.name') . ' - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
        <meta property="og:url" content="{{ url()->full() }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:locale" content="vi_VN">
        <meta property="og:image" content="{{ $logoPath }}">
        <meta property="og:image:secure_url" content="{{ $logoPath }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="@yield('title', 'Trang chủ - ' . config('app.name'))">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('title', 'Trang chủ - ' . config('app.name'))">
        <meta name="twitter:description" content="@yield('description', 'Truyện ' . config('app.name') . ' - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
        <meta name="twitter:image" content="{{ $logoPath }}">
        <meta name="twitter:image:alt" content="@yield('title', 'Trang chủ - ' . config('app.name'))">
    @endif
    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta name="author" content="Truyện " . config('app.name') . ">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->

    {{-- styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    @vite('resources/assets/frontend/css/styles.css')

    @stack('styles')

    {{-- end styles --}}
</head>

<body>
    <header>
        <nav
            class="navbar navbar-expand-lg fixed-top transition-header chapter-header scrolled bg-site shadow-sm py-2 d-block">
            <div class="">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <!-- Logo -->
                    <a class="navbar-brand p-0" href="{{ route('home') }}">
                        <img height="70" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
                    </a>

                    <div class="list-menu d-none d-lg-block">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                            <li class="nav-item dropdown">
                                <a class="text-white nav-link dropdown-toggle fw-bold" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Thể loại
                                </a>
                                <ul class="dropdown-menu category-menu">
                                    <div class="row px-2">
                                        @foreach ($categories->chunk(ceil($categories->count() / 3)) as $categoryGroup)
                                            <div class="col-4">
                                                @foreach ($categoryGroup as $category)
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('categories.story.show', $category->slug) }}">
                                                            {{ $category->name }}
                                                            <span
                                                                class="badge bg-secondary float-end">{{ $category->stories_count }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="text-white nav-link fw-bold" href="{{ route('story.hot') }}">
                                    Truyện hot
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="text-white nav-link fw-bold" href="{{ route('story.new') }}">
                                    Truyện mới
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="text-white nav-link fw-bold" href="{{ route('story.completed') }}">
                                    Truyện full
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="text-white nav-link fw-bold" href="{{ route('guide.show') }}">
                                    Hướng dẫn
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="search-container d-flex align-items-center me-2 d-none d-md-block">
                            <div class="position-relative">
                                <form action="{{ route('searchHeader') }}" method="GET">
                                    <input type="text" name="query" class="form-control search-input"
                                        placeholder="Tìm kiếm truyện..." value="{{ request('query') }}">
                                    <button type="submit" class="btn search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @auth
                            <a class="text-white fw-bold nav-link d-none d-lg-block" href="{{ route('login') }}">
                                <div class="dropdown">
                                    <a href="#"
                                        class="d-none d-lg-block d-flex align-items-center text-decoration-none dropdown-toggle text-white fw-bold"
                                        data-bs-toggle="dropdown">
                                        <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                            class="rounded-circle" width="40" height="40" alt="avatar"
                                            style="object-fit: cover;">

                                        <span class="ms-2">{{ auth()->user()->name }}</span>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end animate slideIn border-cl-shopee">
                                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'mod')
                                            <li>
                                                <a class="dropdown-item fw-semibold"
                                                    href="{{ route('admin.dashboard') }}">
                                                    <i class="fas fa-tachometer-alt me-2 color-3"></i> Quản trị
                                                </a>
                                            </li>
                                        @endif

                                        @if (auth()->user()->role === 'author')
                                            <li>
                                                <a class="dropdown-item fw-semibold"
                                                    href="{{ route('user.author.index') }}">
                                                    <i class="fa-solid fa-user-pen me-2 color-3"></i> Khu vực tác giả
                                                </a>
                                            </li>
                                        @endif

                                        <li>
                                            <a class="dropdown-item fw-semibold" href="{{ route('user.profile') }}">
                                                <i class="fa-regular fa-circle-user me-2 color-3"></i> Trang cá nhân
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item fw-semibold" href="{{ route('logout') }}">
                                                <i class="fas fa-sign-out-alt me-2 color-3"></i> Đăng xuất
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn d-none d-lg-block fw-bold text-white"> <i
                                    class="fa-regular fa-circle-user fa-lg"></i> Đăng nhập</a>
                        @endauth
                    </div>

                    <div class="d-flex">
                        <button type="button" class="btn border rounded-pill bg-primary-1 d-md-none"
                            style="width: 40px; height: 40px;" id="mobileSearchToggle">
                            <i class="fas fa-search"></i>
                        </button>

                        <!-- Mobile Menu Toggle Button - Visible on screens smaller than lg -->
                        <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasExample">
                            <i class="fa-solid fa-bars fa-xl"></i>
                        </button>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Mobile Menu - Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample">
            <div class="offcanvas-header">

                <a class="navbar-brand" href="{{ route('home') }}">
                    <img height="50" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
                </a>

                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <!-- Navigation Links -->
                <div class="mobile-section">
                    <div class="mobile-nav-links d-flex flex-column">

                        <div class="search-container d-flex align-items-center d-md-none">
                            <div class="position-relative">
                                <form action="{{ route('searchHeader') }}" method="GET">
                                    <input type="text" name="query" class="form-control search-input"
                                        placeholder="Tìm kiếm truyện..." value="{{ request('query') }}">
                                    <button type="submit" class="btn search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <hr class="divider my-3">

                        <div class="accordion" id="categoryAccordion">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="categoryHeading">
                                    <button class="accordion-button collapsed mobile-menu-item p-0 fw-semibold"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#categoryCollapse">
                                        <i class="fa-solid fa-layer-group fa-lg me-2 color-3"></i> Thể loại
                                    </button>
                                </h2>
                                <div id="categoryCollapse" class="accordion-collapse collapse"
                                    data-bs-parent="#categoryAccordion">
                                    <div class="accordion-body p-0 mt-2">
                                        <div class="row g-0">
                                            @foreach ($categories->chunk(ceil($categories->count() / 2)) as $categoryGroup)
                                                <div class="col-6">
                                                    @foreach ($categoryGroup as $category)
                                                        <a class="mobile-menu-item ps-3 py-2 d-block"
                                                            href="{{ route('categories.story.show', $category->slug) }}">
                                                            {{ $category->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="divider my-3">

                        <a href="{{ route('story.hot') }}" class="mobile-menu-item fw-semibold">
                            <i class="fa-solid fa-fire fa-lg me-2 color-3"></i> Truyện hot
                        </a>

                        <hr class="divider my-3">

                        <a href="{{ route('story.new') }}" class="mobile-menu-item fw-semibold">
                            <i class="fa-solid fa-clock fa-lg me-2 color-3"></i> Truyện mới
                        </a>

                        <hr class="divider my-3">

                        <a href="{{ route('story.completed') }}" class="mobile-menu-item fw-semibold">
                            <i class="fa-solid fa-check fa-lg me-2 color-3"></i> Truyện full
                        </a>

                        <hr class="divider my-3">

                        <a href="{{ route('guide.show') }}" class="mobile-menu-item fw-semibold">
                            <i class="fa-solid fa-circle-info fa-lg me-2 color-3"></i> Hướng dẫn
                        </a>

                        <hr class="divider my-3">

                        @auth
                            <div class="accordion" id="userAccordion">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header" id="userHeading">
                                        <button class="accordion-button collapsed mobile-menu-item p-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#userCollapse">
                                            <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                                class="rounded-circle me-2" width="40" height="40" alt="avatar"
                                                style="object-fit: cover;">
                                            <span class="fw-semibold">{{ auth()->user()->name }}</span>
                                        </button>
                                    </h2>
                                    <div id="userCollapse" class="accordion-collapse collapse"
                                        data-bs-parent="#userAccordion">
                                        <div class="accordion-body p-0 mt-2">
                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'mod')
                                                <a class="mobile-menu-item ps-3 py-2 d-block fw-semibold"
                                                    href="{{ route('admin.dashboard') }}">
                                                    <i class="fas fa-tachometer-alt me-2 color-3"></i> Quản trị
                                                </a>
                                            @endif

                                            @if (auth()->user()->role === 'author')
                                                <a class="mobile-menu-item ps-3 py-2 d-block fw-semibold"
                                                    href="{{ route('user.author.index') }}">
                                                    <i class="fa-solid fa-user-pen me-2 color-3"></i> Khu vực tác giả
                                                </a>
                                            @endif

                                            <a class="mobile-menu-item ps-3 py-2 d-block fw-semibold"
                                                href="{{ route('user.profile') }}">
                                                <i class="fas fa-user me-2 color-3"></i> Trang cá nhân
                                            </a>

                                            <a class="mobile-menu-item ps-3 py-2 d-block fw-semibold"
                                                href="{{ route('logout') }}">
                                                <i class="fas fa-sign-out-alt me-2 color-3"></i> Đăng xuất
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="mobile-menu-item fw-semibold">
                                <i class="fa-regular fa-circle-user fa-lg me-2 color-3"></i> Đăng nhập
                            </a>
                        @endauth

                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.transition-header');
            const scrollThreshold = 50;

            function handleScroll() {
                if (window.scrollY > scrollThreshold) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }

            // Listen for scroll events
            window.addEventListener('scroll', handleScroll);

            // Initial check
            handleScroll();
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Existing header transition code...

            // Search form handling
            const searchForm = document.querySelector('.search-container form');
            const searchInput = searchForm.querySelector('input[name="query"]');

            searchForm.addEventListener('submit', function(e) {
                if (searchInput.value.trim() === '') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            // Auto-focus search input when clicking on the search container
            document.querySelector('.search-container').addEventListener('click', function() {
                searchInput.focus();
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Existing header transition code...
            const header = document.querySelector(".transition-header");
            const scrollThreshold = 300;

            function handleScroll() {
                if (window.scrollY > scrollThreshold) {
                    header.classList.add("scrolled");
                } else {
                    header.classList.remove("scrolled");
                }
            }

            window.addEventListener("scroll", handleScroll);
            handleScroll();

            // Search form handling for desktop
            const searchForm = document.querySelector(".search-container form");
            if (searchForm) {
                const searchInput = searchForm.querySelector('input[name="query"]');

                searchForm.addEventListener("submit", function(e) {
                    if (searchInput.value.trim() === "") {
                        e.preventDefault();
                        searchInput.focus();
                    }
                });

                document.querySelector(".search-container").addEventListener("click", function() {
                    searchInput.focus();
                });
            }

            // Mobile search toggle functionality
            const mobileSearchToggle = document.getElementById("mobileSearchToggle");
            const mobileSearchContainer = document.getElementById("mobileSearchContainer");
            const mobileSearchInput = document.getElementById("mobileSearchInput");

            if (mobileSearchToggle && mobileSearchContainer) {
                mobileSearchToggle.addEventListener("click", function() {
                    // Toggle visibility
                    if (mobileSearchContainer.style.display === "none" || mobileSearchContainer.style
                        .display === "") {
                        mobileSearchContainer.style.display = "block";

                        // Scroll to top of page first
                        window.scrollTo({
                            top: 0,
                            behavior: "smooth"
                        });

                        // Focus the input after scroll completes
                        setTimeout(() => {
                            if (mobileSearchInput) {
                                mobileSearchInput.focus();
                            }
                        }, 500);
                    } else {
                        mobileSearchContainer.style.display = "none";
                    }
                });
            }
        });
    </script>
</body>

</html>
