@extends('layouts.app')

@section('title', " Truyện {$story->title} | Chương {$chapter->number}: {$chapter->title} | " . config('app.name'))
@section('description', Str::limit(html_entity_decode(strip_tags($chapter->content)), 160))
@section('keyword', "chương {$chapter->number}, {$chapter->title}")

@section('meta')
    <meta property="og:type" content="article">
    <meta property="og:title" content="Chương {{ $chapter->number }}: {{ $chapter->title }} - {{ $story->title }}">
    <meta property="og:description" content="{{ Str::limit(html_entity_decode(strip_tags($chapter->content)), 100) }}">
    <meta property="og:image" content="{{ $story->cover_jpeg ? url(Storage::url($story->cover_jpeg)) : url(asset('images/logo/logo-site.png')) }}">
    <meta property="og:image:secure_url" content="{{ $story->cover_jpeg ? url(Storage::url($story->cover_jpeg)) : url(asset('images/logo/logo-site.png')) }}">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="800">
    <meta property="og:image:alt" content="Ảnh bìa truyện {{ $story->title }} - Chương {{ $chapter->number }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:updated_time" content="{{ $chapter->updated_at->format('c') }}">
    <meta property="article:modified_time" content="{{ $chapter->updated_at->format('c') }}">

    {{-- Article specific meta tags --}}
    <meta property="article:author" content="{{ $story->author_name ?? ($story->user->name ?? 'Unknown') }}">
    <meta property="article:published_time" content="{{ $chapter->created_at->format('c') }}">
    <meta property="article:section" content="{{ $story->categories->first()->name ?? 'Truyện' }}">
    @foreach($story->categories as $category)
        <meta property="article:tag" content="{{ $category->name }}">
    @endforeach

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Chương {{ $chapter->number }}: {{ $chapter->title }} - {{ $story->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($chapter->content), 160) }}">
    <meta name="twitter:image" content="{{ $story->cover_jpeg ? url(Storage::url($story->cover_jpeg)) : url(asset('images/logo/logo-site.png')) }}">
    <meta name="twitter:image:alt" content="Ảnh bìa truyện {{ $story->title }} - Chương {{ $chapter->number }}">
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section id="chapter" class="mt-80 mb-5">
        <div class="container-md">
            <div>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-center">
                    <nav aria-label="breadcrumb " class="pt-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-decoration-none color-3"
                                    href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item story-title-breadcrumb text-truncate"><a
                                    class="text-decoration-none color-3 "
                                    href="{{ route('show.page.story', $story->slug) }}">{{ $story->title }}</a></li>
                            <li class="breadcrumb-item active color-3" aria-current="page">Chương {{ $chapter->number }}:
                                {{ $chapter->title }}</li>
                        </ol>
                    </nav>
                </div>

                <div class="chapter-header text-center mb-4 animate__animated animate__fadeIn">
                    <h2 class="chapter-title h3 fw-bold">

                        {{ $chapter->title && trim($chapter->title) !== 'Chương ' . $chapter->number
                            ? 'Chương ' . $chapter->number . ': ' . $chapter->title
                            : 'Chương ' . $chapter->number }}

                    </h2>
                    <div class="chapter-meta d-flex justify-content-center align-items-center flex-wrap gap-2 mt-2">

                        <span class="badge text-dark p-2">
                            <i class="fa-regular fa-clock me-1 color-3"></i>
                            Đăng lúc
                            @if ($chapter->schedule_publish_at)
                                {{ $chapter->schedule_publish_at->format('H:i d/m/Y') }}
                            @else
                                {{ $chapter->created_at->format('H:i d/m/Y') }}
                            @endif
                        </span>
                        <span class="badge text-dark p-2">
                            <i class="fa-regular fa-eye color-pp-1"></i> {{ $chapter->views }}
                        </span>
                        <span class="badge text-dark p-2">
                            <a href="#comments" class="text-decoration-none text-dark"><i
                                    class="fa-regular fa-comments color-success-custom"></i>
                                {{ $chapter->comments_count }}</a>

                        </span>
                    </div>
                </div>

                <div
                    class="chapter-nav d-flex justify-content-center align-items-center my-4 animate__animated animate__fadeIn animate__delay-1s">
                    @if ($prevChapter)
                        <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $prevChapter->slug]) }}"
                            class="btn bg-1 rounded-5 btn-prev me-2 text-white d-sm-flex align-items-center">
                            <i class="fas fa-arrow-left me-1 h-100"></i> <span class="d-none d-sm-block">Chương
                                trước</span>
                        </a>
                    @else
                        <button disabled
                            class="btn btn-outline-secondary rounded-5 btn-prev me-2 d-sm-flex align-items-center">
                            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-block">Chương trước</span>
                        </button>
                    @endif

                    <div class="dropdown chapter-list-dropdown">
                        <button class="btn dropdown-toggle rounded-0 bg-1 text-white" type="button"
                            id="chapterListDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bars me-2"></i>Chương {{ $chapter->number }}
                        </button>
                        <div class="dropdown-menu chapter-dropdown-menu" aria-labelledby="chapterListDropdown">
                            <div class="chapter-dropdown-header">
                                <h6>Danh sách chương</h6>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="chapter-dropdown-body">
                                @foreach ($story->chapters->sortBy('number') as $chap)
                                    <a class="dropdown-item {{ $chap->id === $chapter->id ? 'active' : '' }}"
                                        href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chap->slug]) }}">
                                        Chương {{ $chap->number }}: {{ $chap->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if ($nextChapter)
                        <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $nextChapter->slug]) }}"
                            class="btn bg-1 text-white btn-next rounded-5 ms-2 d-sm-flex align-items-center">
                            <span class="d-none d-sm-block">Chương tiếp</span> <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    @else
                        <button disabled
                            class="btn btn-outline-secondary btn-next rounded-5 ms-2 d-sm-flex align-items-center">
                            <span class="d-none d-sm-block ">Chương tiếp</span> <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    @endif
                </div>

                <!-- Chapter Content -->
                <div id="chapter-content" class="rounded-4 chapter-content mb-4">
                    @if (isset($hasAccess) && $hasAccess && isset($hasPasswordAccess) && $hasPasswordAccess)
                        <div style="line-height: 2;">
                            {!! nl2br(e($chapter->content)) !!}
                        </div>
                    @elseif (isset($hasAccess) && $hasAccess && isset($hasPasswordAccess) && !$hasPasswordAccess && $chapter->is_free && !empty($chapter->password))
                        <!-- Modal nhập mật khẩu cho chương miễn phí -->
                        <div class="password-notice bg-light p-4 rounded-3 text-center my-4">
                            <div class="mb-3">
                                <i class="fas fa-key fa-3x text-primary mb-3"></i>
                                <h4 class="fw-bold">Chương này có mật khẩu</h4>
                                @if (!empty($chapter->password_hint))
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <strong>Hướng dẫn:</strong> {{ $chapter->password_hint }}
                                    </div>
                                @endif
                                <p class="text-muted">Vui lòng nhập mật khẩu để xem nội dung chương</p>
                            </div>

                            <form id="passwordForm" class="password-form">
                                @csrf
                                <div class="input-group mb-3" style="max-width: 400px; margin: 0 auto;">
                                    <input type="password" class="form-control" id="chapterPassword" 
                                           placeholder="Nhập mật khẩu..." required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-unlock me-1"></i> Xác nhận
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="chapter-preview">
                            <!-- Hiển thị thông báo mua chương -->
                            <div class="purchase-notice bg-light p-4 rounded-3 text-center my-4">
                                <div class="mb-3">
                                    <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                                    <h4 class="fw-bold">Nội dung này yêu cầu mua để đọc</h4>
                                    <p class="text-muted">Bạn cần mua chương này hoặc toàn bộ truyện để tiếp tục đọc</p>
                                </div>

                                <div class="purchase-options d-flex flex-column flex-md-row justify-content-center gap-3">
                                    @if ($chapter->price > 0)
                                        <div class="chapter-purchase-option p-3 border rounded">
                                            <h5 class="fw-bold">Mua chương này</h5>
                                            <p class="price mb-2"><i class="fas fa-coins text-warning"></i>
                                                {{ number_format($chapter->price) }} xu</p>
                                            @guest
                                                <a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập để mua</a>
                                            @else
                                                <form action="{{ route('purchase.chapter') }}" method="POST"
                                                    class="purchase-form" id="purchase-chapter-form">
                                                    @csrf
                                                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                                    <button type="button" class="btn btn-primary purchase-chapter-btn"
                                                        onclick="showPurchaseModal('chapter', {{ $chapter->id }}, 'Chương {{ $chapter->number }}: {{ $chapter->title }}', {{ $chapter->price }})">
                                                        <i class="fas fa-shopping-cart me-1"></i> Mua ngay
                                                    </button>
                                                </form>
                                            @endguest
                                        </div>
                                    @endif

                                    @if ($story->combo_price > 0)
                                        <div class="story-purchase-option p-3 border rounded bg-light">
                                            <h5 class="fw-bold">Mua trọn bộ truyện</h5>
                                            <p class="price mb-2"><i class="fas fa-coins text-warning"></i>
                                                {{ number_format($story->combo_price) }} xu</p>
                                            <p class="text-success small">

                                                <i class="fas fa-check-circle"></i> Truy cập tất cả
                                                {{ $story->chapters->count() ?? 0 }} chương
                                            </p>
                                            @guest
                                                <a href="{{ route('login') }}" class="btn btn-success">Đăng nhập để mua</a>
                                            @else
                                                <form action="{{ route('purchase.story.combo') }}" method="POST"
                                                    class="purchase-form" id="purchase-story-form">
                                                    @csrf
                                                    <input type="hidden" name="story_id" value="{{ $story->id }}">
                                                    <button type="button" class="btn btn-success purchase-story-btn"
                                                        onclick="showPurchaseModal('story', {{ $story->id }}, '{{ $story->title }}', {{ $story->combo_price }})">
                                                        <i class="fas fa-shopping-cart me-1"></i> Mua trọn bộ
                                                    </button>
                                                </form>
                                            @endguest
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Chapter Navigation Bottom -->
                <div
                    class="chapter-nav d-flex justify-content-center align-items-center my-4 animate__animated animate__fadeIn animate__delay-1s">
                    @if ($prevChapter)
                        <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $prevChapter->slug]) }}"
                            class="btn bg-1 rounded-5 btn-prev me-2 text-white d-sm-flex align-items-center">
                            <i class="fas fa-arrow-left me-1 h-100"></i> <span class="d-none d-sm-block">Chương
                                trước</span>
                        </a>
                    @else
                        <button disabled
                            class="btn btn-outline-secondary rounded-5 btn-prev me-2 d-sm-flex align-items-center">
                            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-block">Chương trước</span>
                        </button>
                    @endif

                    <div class="dropdown chapter-list-dropdown">
                        <button class="btn dropdown-toggle rounded-0 bg-1 text-white" type="button"
                            id="chapterListDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bars me-2"></i>Chương {{ $chapter->number }}
                        </button>
                        <div class="dropdown-menu chapter-dropdown-menu" aria-labelledby="chapterListDropdown">
                            <div class="chapter-dropdown-header">
                                <h6>Danh sách chương</h6>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="chapter-dropdown-body">
                                @foreach ($story->chapters->sortByDesc('number') as $chap)
                                    <a class="dropdown-item {{ $chap->id === $chapter->id ? 'active' : '' }}"
                                        href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chap->slug]) }}">
                                        Chương {{ $chap->number }}: {{ $chap->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if ($nextChapter)
                        <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $nextChapter->slug]) }}"
                            class="btn bg-1 text-white btn-next rounded-5 ms-2 d-sm-flex align-items-center">
                            <span class="d-none d-sm-block">Chương tiếp</span> <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    @else
                        <button disabled
                            class="btn btn-outline-secondary btn-next rounded-5 ms-2 d-sm-flex align-items-center">
                            <span class="d-none d-sm-block ">Chương tiếp</span> <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>


    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8">
                @if (!Auth()->check() || (Auth()->check() && Auth()->user()->ban_comment == false))
                    @include('components.comment', [
                        'pinnedComments' => $pinnedComments,
                        'regularComments' => $regularComments,
                    ])
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-sad-tear fa-4x text-muted mb-3 animate__animated animate__shakeX"></i>
                        <h5 class="text-danger">Bạn đã bị cấm bình luận!</h5>
                    </div>
                @endif

            </div>
            <div class="col-12 col-lg-4 mt-3 mt-sm-0">
                <div class="mt-4">
                    {{-- hot stories --}}
                    @include('components.hot_stories')
                </div>
            </div>
        </div>
    </div>

    {{-- @include('components.list_story_de_xuat', ['newStories' => $newStories]) --}}

    @auth
        @include('components.modals.chapter-purchase-modal')
    @endauth
@endsection



@push('styles')
    <style>
        .btn-prev {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .btn-next {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .story-title-breadcrumb {
            max-width: 100%;
        }

        @media (max-width: 576px) {
            .story-title-breadcrumb {
                max-width: 100px;
            }
        }


        /* Chapter dropdown styles */
        .chapter-dropdown-menu {
            max-height: 350px;
            overflow-y: auto;
            width: 300px;
            z-index: 9999 !important;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 0;
            animation: dropdown-fade 0.3s ease;
            position: absolute !important;
            top: calc(100% + 5px) !important;
            left: -75px !important;
            right: auto !important;
            bottom: auto !important;
            transform: none !important;
            margin: 0 !important;
        }

        @keyframes dropdown-fade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chapter-dropdown-menu.show {
            display: block;
        }

        .chapter-dropdown-header {
            position: sticky;
            top: 0;
            background: var(--primary-color-3);
            color: white;
            padding: 12px 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            z-index: 1;
        }

        .chapter-dropdown-header h6 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }

        .chapter-dropdown-body {
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 10px 15px;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(var(--primary-color-3-rgb), 0.1);
            border-left: 3px solid var(--primary-color-3);
        }

        .dropdown-item.active {
            background-color: rgba(var(--primary-color-3-rgb), 0.2);
            border-left: 3px solid var(--primary-color-3);
            font-weight: 600;
            color: var(--primary-color-3);
        }

        .dropdown-divider {
            margin: 0;
            opacity: 0;
        }

        /* Custom scrollbar for dropdown */
        .chapter-dropdown-body::-webkit-scrollbar {
            width: 6px;
        }

        .chapter-dropdown-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chapter-dropdown-body::-webkit-scrollbar-thumb {
            background: var(--primary-color-3);
            border-radius: 10px;
        }

        .chapter-dropdown-body::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color-4);
        }

        /* Dark mode dropdown styles */
        body.dark-mode .chapter-dropdown-menu {
            background-color: #333;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        body.dark-mode .chapter-dropdown-header {
            background: var(--primary-color-1);
        }

        body.dark-mode .dropdown-item {
            color: #fff;
            border-left: 3px solid transparent;
        }

        body.dark-mode .dropdown-item:hover {
            background-color: #444;
            border-left: 3px solid var(--primary-color-1);
        }

        body.dark-mode .dropdown-item.active {
            background-color: #3a3a3a;
            border-left: 3px solid var(--primary-color-1);
        }

        body.dark-mode .chapter-dropdown-body::-webkit-scrollbar-track {
            background: #2a2a2a;
        }

        /* For the bottom navigation dropdown, if it would go off-screen, show it above */
        .chapter-nav:last-of-type .chapter-dropdown-menu {
            top: auto !important;
            bottom: calc(100% + 5px) !important;
        }

        /* Purchase notice styles */
        .purchase-notice {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .purchase-options {
            max-width: 800px;
            margin: 0 auto;
        }

        .chapter-purchase-option,
        .story-purchase-option {
            flex: 1;
            transition: all 0.3s ease;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .chapter-purchase-option:hover,
        .story-purchase-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .story-purchase-option {
            background-color: #f0f8ff !important;
            border-color: #d1e7ff !important;
        }

        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ff9800;
        }

        .preview-content {
            position: relative;
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color-3);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .preview-content::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
        }

        /* Bookmark button styles */
        .bookmark-btn.active {
            background-color: var(--primary-color-3);
            color: white;
        }

        .bookmark-btn.active:hover {
            background-color: var(--primary-color-4);
            color: white;
        }

        /* Animation for bookmark button */
        @keyframes bookmark-pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .bookmark-btn.active i {
            animation: bookmark-pulse 0.3s ease-in-out;
        }

        /* Bookmark button styles in dark mode */
        body.dark-mode .bookmark-btn.active {
            background-color: var(--primary-color-1);
            color: white;
        }

        body.dark-mode .bookmark-btn.active:hover {
            background-color: var(--primary-color-2);
        }

        /* Dark mode styles for chapter page */
        body.dark-mode .chapter-content {
            background-color: #2d2d2d !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .chapter-title {
            color: #e0e0e0 !important;
        }

        body.dark-mode .chapter-meta .badge {
            background-color: #404040 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .breadcrumb {
            background-color: transparent !important;
        }

        body.dark-mode .breadcrumb-item.active {
            color: #e0e0e0 !important;
        }

        body.dark-mode .password-notice {
            background-color: #404040 !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .purchase-notice {
            background-color: #404040 !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .chapter-purchase-option,
        body.dark-mode .story-purchase-option {
            background-color: #2d2d2d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .story-purchase-option {
            background-color: #1a2332 !important;
            border-color: #2c5282 !important;
        }

        body.dark-mode .alert-success {
            background-color: rgba(25, 135, 84, 0.2) !important;
            border-color: #198754 !important;
            color: #75b798 !important;
        }

        body.dark-mode .alert-danger {
            background-color: rgba(220, 53, 69, 0.2) !important;
            border-color: #dc3545 !important;
            color: #f1aeb5 !important;
        }

        body.dark-mode .btn-outline-secondary {
            border-color: #666 !important;
            color: #ccc !important;
        }

        body.dark-mode .btn-outline-secondary:hover {
            background-color: #666 !important;
            color: white !important;
        }

        body.dark-mode .text-danger {
            color: #f1aeb5 !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script xử lý đánh dấu trang (bookmark) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                const bookmarkBtn = document.querySelector('.bookmark-btn');
                @auth
                // Kiểm tra trạng thái bookmark khi tải trang
                checkBookmarkStatus();

                // Xử lý sự kiện click bookmark
                bookmarkBtn.addEventListener('click', toggleBookmark);
            @else
                // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
                bookmarkBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Cần đăng nhập',
                        text: 'Bạn cần đăng nhập để sử dụng tính năng đánh dấu',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng nhập',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('login') }}';
                        }
                    });
                });
            @endauth

            function checkBookmarkStatus() {
                @auth
                fetch('{{ route('user.bookmark.status') }}?story_id={{ $story->id }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_bookmarked) {
                            bookmarkBtn.classList.add('active');
                            bookmarkBtn.title = 'Bỏ đánh dấu';

                        } else {
                            bookmarkBtn.classList.remove('active');
                            bookmarkBtn.title = 'Đánh dấu trang';
                        }
                    })
                    .catch(error => console.error('Error checking bookmark status:', error));
            @endauth
        }

        function toggleBookmark() {
            @auth
            // CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route('user.bookmark.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        story_id: {{ $story->id }},
                        chapter_id: {{ $chapter->id }}
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'added') {
                        bookmarkBtn.classList.add('active');
                        bookmarkBtn.title = 'Bỏ đánh dấu';


                        // Hiển thị thông báo thành công
                        Swal.fire({
                            title: 'Thành công!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else if (data.status === 'removed') {
                        bookmarkBtn.classList.remove('active');
                        bookmarkBtn.title = 'Đánh dấu trang';


                        // Hiển thị thông báo đã xóa
                        Swal.fire({
                            title: 'Đã xóa!',
                            text: data.message,
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Error toggling bookmark:', error);

                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi khi thực hiện đánh dấu truyện',
                        icon: 'error'
                    });
                });
        @endauth
        }
        });
    </script>

    <!-- Script xử lý cài đặt đọc truyện -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý form nhập mật khẩu chương
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const password = document.getElementById('chapterPassword').value;
                    if (!password.trim()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Vui lòng nhập mật khẩu',
                            text: 'Bạn cần nhập mật khẩu để xem chương này.'
                        });
                        return;
                    }
                    
                    // Hiển thị loading
                    const submitBtn = passwordForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang kiểm tra...';
                    submitBtn.disabled = true;
                    
                    // Gửi request kiểm tra mật khẩu
                    fetch('{{ route("chapter.check-password", ["storySlug" => $story->slug, "chapterSlug" => $chapter->slug]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            password: password
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload trang để hiển thị nội dung
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Mật khẩu không đúng!',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi kiểm tra mật khẩu. Vui lòng thử lại.'
                        });
                    })
                    .finally(() => {
                        // Khôi phục button
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                });
            }

            // Fix dropdown positioning
            const dropdownButtons = document.querySelectorAll('.chapter-list-dropdown .btn');
            dropdownButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Đợi Bootstrap mở dropdown
                    setTimeout(() => {
                        const dropdown = this.nextElementSibling;
                        if (dropdown && dropdown.classList.contains('show')) {
                            // Kiểm tra xem dropdown có bị tràn ra khỏi viewport không
                            const rect = dropdown.getBoundingClientRect();
                            const windowHeight = window.innerHeight;

                            // Nếu dropdown sẽ tràn ra khỏi màn hình
                            if (rect.bottom > windowHeight) {
                                dropdown.style.top = 'auto';
                                dropdown.style.bottom = 'calc(100% + 5px)';
                            } else {
                                dropdown.style.top = 'calc(100% + 5px)';
                                dropdown.style.bottom = 'auto';
                            }

                            // Đảm bảo dropdown không bị che khuất bởi nội dung
                            dropdown.style.zIndex = '9999';
                        }
                    }, 0);
                });
            });
        });
    </script>
@endpush
