@extends('layouts.app')

@section('title')
    @yield('info_title', 'Thông tin cá nhân')
@endsection

@section('description')
    @yield('info_description', 'Thông tin cá nhân của bạn')
@endsection

@section('keywords')
    @yield('info_keyword', 'Thông tin cá nhân, thông tin tài khoản')
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/information.css')
@endpush

@section('content')
    @include('components.toast')

    <div class="container mt-80 mb-5 user-container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-12 col-lg-3">
                <div class="user-sidebar">
                    <div class="user-header rounded-4 mb-3 py-2">
                        <div class="user-header-bg"></div>
                        <div class="user-header-content text-center">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="user-avatar-wrapper">
                                    @if (!empty(Auth::user()->avatar))
                                        <img class="user-avatar" src="{{ Storage::url(Auth::user()->avatar) }}"
                                            alt="Avatar">
                                    @else
                                        <div class="user-avatar d-flex align-items-center justify-content-center bg-light">
                                            <i class="fa-solid fa-user user-avatar-icon"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ms-3">
                                    <h5 class="user-info-name color-1">{{ Auth::user()->name }}</h5>
                                    <div class="user-info-email color-text fw-semibold">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                            <div class="text-white text-shadow-custom px-4 mt-3 fs-24 fw-bold d-flex align-items-center justify-content-center">
                                <img class="me-2" src="{{ asset('images/d/cam.png') }}" alt="Coin" style="width: 20px; height: 20px;">
                                <span>{{ number_format(Auth::user()->coins) }} Cám </span>
                            </div>
                        </div>
                    </div>

                    <div class="user-nav box-shadow-custom rounded-4">
                        <div class="user-nav-item">
                            <a href="{{ route('user.profile') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                                <i class="fa-solid fa-user user-nav-icon"></i>
                                <span class="user-nav-text">Thông tin cá nhân</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.reading.history') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.reading.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-book-open user-nav-icon"></i>
                                <span class="user-nav-text">Lịch sử đọc truyện</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.purchases') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.purchases*') ? 'active' : '' }}">
                                <i class="fa-solid fa-shopping-cart user-nav-icon"></i>
                                <span class="user-nav-text">Truyện đã mua</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.bookmarks') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.bookmarks') ? 'active' : '' }}">
                                <i class="fa-solid fa-bookmark user-nav-icon"></i>
                                <span class="user-nav-text">Truyện đã lưu</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.daily-tasks') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.daily-tasks') ? 'active' : '' }}">
                                <i class="fa-solid fa-tasks user-nav-icon"></i>
                                <span class="user-nav-text">Nhiệm vụ hàng ngày</span>
                                @php
                                    $uncompletedTasks = 0;
                                    if (Auth::check()) {
                                        $tasks = \App\Models\DailyTask::active()->get();
                                        foreach ($tasks as $task) {
                                            if (!$task->isCompletedByUserToday(Auth::id())) {
                                                $uncompletedTasks++;
                                            }
                                        }
                                    }
                                @endphp
                                @if($uncompletedTasks > 0)
                                    <span class="badge bg-danger rounded-pill ms-1">{{ $uncompletedTasks }}</span>
                                @endif
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.bank.auto.deposit') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.bank.auto.deposit*') ? 'active' : '' }}">
                                <i class="fa-solid fa-coins user-nav-icon"></i>
                                <span class="user-nav-text">Nạp cám</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.coin-history') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.coin-history') ? 'active' : '' }}">
                                <i class="fa-solid fa-history user-nav-icon"></i>
                                <span class="user-nav-text">Lịch sử cám</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.chapter.reports') }}"
                                class="user-nav-link text-decoration-none hover-color-7 {{ request()->routeIs('user.chapter.reports') ? 'active' : '' }}">
                                <i class="fa-solid fa-flag user-nav-icon"></i>
                                <span class="user-nav-text">Báo cáo của tôi</span>
                            </a>
                        </div>

                        <div class="user-nav-item user-nav-logout">
                            <a href="{{ route('logout') }}" class="user-nav-link text-danger text-decoration-none">
                                <i class="fa-solid fa-arrow-right-from-bracket user-nav-icon"></i>
                                <span class="user-nav-text">Đăng xuất</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-12 col-lg-9">
                <div class="user-content">
                    <div class="content-header">
                        <h4 class="content-title">@yield('info_section_title', 'Thông tin cá nhân')</h4>
                        @hasSection('info_section_desc')
                            <p class="content-desc">@yield('info_section_desc')</p>
                        @endif
                    </div>

                    <div class="content-body">
                        @yield('info_content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function isMobile() {
                return window.innerWidth < 992;
            }

            function scrollToContent() {
                if (isMobile()) {
                    const hasScrolled = sessionStorage.getItem('hasScrolledToContent');

                    if (!hasScrolled) {
                        const contentOffset = $('.user-content').offset().top;

                        $('html, body').animate({
                            scrollTop: contentOffset - 20
                        }, 500);

                        sessionStorage.setItem('hasScrolledToContent', 'true');
                    }
                }
            }

            setTimeout(scrollToContent, 300);

            $('.user-nav-link').on('click', function() {
                sessionStorage.removeItem('hasScrolledToContent');
            });

        });
    </script>
    @stack('info_scripts')
@endpush
