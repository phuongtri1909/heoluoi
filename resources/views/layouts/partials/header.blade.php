<!DOCTYPE html>
<html lang="vi" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="google" content="notranslate">

    @php
        // Logo được load từ View Composer (đã cache)
        $logoPath = isset($logoSite) && $logoSite && $logoSite->logo ? Storage::url($logoSite->logo) : asset('images/logo/logo-site.png');
        $faviconPath = isset($logoSite) && $logoSite && $logoSite->favicon ? Storage::url($logoSite->favicon) : asset('favicon.ico');
    @endphp

    <title>@yield('title', 'Trang chủ - ' . config('app.name'))</title>
    <meta name="description" content="@yield('description', 'Truyện ' . config('app.name') . ' - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
    <meta name="keywords" content="@yield('keywords', 'truyện, tiểu thuyết, truyện tranh, đọc truyện online')">
    <meta name="robots" content="noindex, nofollow">

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
    <meta name="author" content="Truyện {{ config('app.name') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    @vite('resources/assets/frontend/css/styles.css')

    @stack('styles')

</head>

<body translate="no">
    <header>
        <nav
            class="navbar navbar-expand-lg fixed-top transition-header chapter-header scrolled bg-site shadow-sm py-0 d-block">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between w-100 header-row-modern">
                    <a class="navbar-brand p-0 header-brand" href="{{ route('home') }}">
                        <img height="80" class="header-logo-img" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
                    </a>

                    <div class="d-flex align-items-center header-icons gap-1 gap-sm-2">
                        <a href="{{ route('searchHeader') }}" class="btn header-icon-btn text-dark rounded-circle d-flex align-items-center justify-content-center bg-4" title="Tìm kiếm">
                            <i class="fas fa-search"></i>
                        </a>

                        @auth
                            @php
                                $userNotifications = \App\Models\Notification::forUserWithReadStatus(auth()->id())->latest('notifications.created_at')->limit(15)->get();
                                $userNotificationsUnread = \App\Models\Notification::unreadCountForUser(auth()->id());
                            @endphp
                            <div class="dropdown position-relative">
                                <a href="#" class="btn header-icon-btn rounded-circle d-flex align-items-center justify-content-center text-decoration-none color-2 position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                                    <i class="fa-regular fa-bell fa-xl"></i>
                                    @if($userNotificationsUnread > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="font-size: 0.65rem;">{{ $userNotificationsUnread > 9 ? '9+' : $userNotificationsUnread }}</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end animate slideIn border-cl-shopee notification-dropdown" style="min-width: 320px; max-width: 380px;">
                                    <li class="px-3 py-2 border-bottom"><strong>Thông báo</strong></li>
                                    @forelse($userNotifications as $notif)
                                        <li>
                                            <a href="#" class="notification-item d-block px-3 py-2 text-decoration-none text-dark {{ $notif->user_read_at ? '' : 'bg-light' }}"
                                               data-notification-id="{{ $notif->id }}">
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 100%;" title="{{ $notif->title }}">{{ $notif->title }}</div>
                                                <div class="small text-muted text-truncate" style="max-width: 100%;" title="{{ Str::limit(strip_tags($notif->body), 100) }}">{{ Str::limit(strip_tags($notif->body), 50) }}</div>
                                                <div class="small text-muted mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                                            </a>
                                        </li>
                                    @empty
                                        <li><div class="dropdown-item-text text-muted px-3 py-3">Chưa có thông báo nào.</div></li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="modal fade notification-modal-modern" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content notification-modal-content">
                                        <div class="modal-header notification-modal-header">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="notification-modal-icon"><i class="fas fa-bell"></i></div>
                                                <h5 class="modal-title mb-0" id="notificationModalLabel">Thông báo</h5>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body notification-modal-body">
                                            <div class="notification-modal-title fw-bold mb-2"></div>
                                            <div class="notification-modal-meta small mb-3"></div>
                                            <div class="notification-modal-body-text"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="btn header-icon-btn rounded-circle p-0 d-flex align-items-center justify-content-center text-decoration-none" data-bs-toggle="dropdown" title="Tài khoản">
                                    <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                        class="rounded-circle header-avatar" width="36" height="36" alt="avatar"
                                        style="object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end animate slideIn border-cl-shopee">
                                    @if (auth()->user()->role === 'admin_main' || auth()->user()->role === 'admin_sub')
                                        <li>
                                            <a class="dropdown-item fw-semibold color-4"
                                                href="{{ route('admin.dashboard') }}">
                                                <i class="fas fa-tachometer-alt me-2 color-4"></i> Quản trị
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item fw-semibold color-2"
                                            href="{{ route('user.profile') }}">
                                            <i class="fa-regular fa-circle-user me-2 color-2"></i> {{ auth()->user()->name }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item fw-semibold color-3" href="{{ route('logout') }}">
                                            <i class="fas fa-sign-out-alt me-2 color-3"></i> Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn header-icon-btn rounded-circle d-flex align-items-center justify-content-center text-decoration-none color-2" title="Đăng nhập">
                                <i class="fa-regular fa-circle-user"></i>
                            </a>
                        @endauth
                    </div>
                </div>
        </nav>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.transition-header');
            const scrollThreshold = 50;

            function handleScroll() {
                if (header) {
                    if (window.scrollY > scrollThreshold) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }
            }

            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('.search-container form');
            if (searchForm) {
                const searchInput = searchForm.querySelector('input[name="query"]');
                if (searchInput) {
                    searchForm.addEventListener('submit', function(e) {
                        if (searchInput.value.trim() === '') {
                            e.preventDefault();
                            searchInput.focus();
                        }
                    });
                    const container = document.querySelector('.search-container');
                    if (container) {
                        container.addEventListener('click', function() {
                            searchInput.focus();
                        });
                    }
                }
            }

            document.querySelectorAll('.notification-item').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    var id = this.getAttribute('data-notification-id');
                    if (!id) return;
                    var showUrl = '{{ url("/user/notifications") }}/' + id;
                    var markReadUrl = '{{ url("/user/notifications") }}/' + id + '/mark-read';
                    var modal = document.getElementById('notificationModal');
                    if (!modal) return;
                    var modalTitle = modal.querySelector('.notification-modal-title');
                    var modalMeta = modal.querySelector('.notification-modal-meta');
                    var modalBody = modal.querySelector('.notification-modal-body-text');
                    modalTitle.textContent = '';
                    modalMeta.textContent = '';
                    modalBody.textContent = 'Đang tải...';
                    var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
                    bsModal.show();
                    fetch(showUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            modalTitle.textContent = data.title || '';
                            modalMeta.textContent = data.created_at || '';
                            modalBody.innerHTML = (data.body || '').replace(/\n/g, '<br>');
                            fetch(markReadUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(function() {
                                el.classList.remove('bg-light');
                                var badge = document.querySelector('.notification-badge');
                                if (badge) {
                                    var n = parseInt(badge.textContent, 10) || 0;
                                    if (n > 1) badge.textContent = (n - 1) > 9 ? '9+' : (n - 1);
                                    else { badge.remove(); }
                                }
                            });
                        })
                        .catch(function() { modalBody.textContent = 'Không tải được nội dung.'; });
                });
            });
        });
    </script>
