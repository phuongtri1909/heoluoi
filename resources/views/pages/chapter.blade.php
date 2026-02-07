@extends('layouts.app')

@section('title', " Truyện {$story->title} | Chương {$chapter->number}: {$chapter->title} | " . config('app.name'))
@section('description', Str::limit(html_entity_decode(strip_tags($chapter->content)), 160))
@section('keyword', "chương {$chapter->number}, {$chapter->title}")

@section('meta')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $story->title }}">
    <meta property="og:description" content="{{ Str::limit(html_entity_decode(strip_tags($chapter->content)), 100) }}">
    <meta property="og:image"
        content="{{ $story->cover ? url(Storage::url($story->cover)) : url(asset('images/logo/logo-site.png')) }}">
    <meta property="og:image:secure_url"
        content="{{ $story->cover ? url(Storage::url($story->cover)) : url(asset('images/logo/logo-site.png')) }}">
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
    @foreach ($story->categories as $category)
        <meta property="article:tag" content="{{ $category->name }}">
    @endforeach

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $story->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($chapter->content), 160) }}">
    <meta name="twitter:image"
        content="{{ $story->cover ? url(Storage::url($story->cover)) : url(asset('images/logo/logo-site.png')) }}">
    <meta name="twitter:image:alt" content="Ảnh bìa truyện {{ $story->title }} - Chương {{ $chapter->number }}">
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section id="chapter" class="mt-80 mb-5 py-5">
        <div class="container">

            <div class="pt-md-0 pb-1">

                <div class="p-md-5">
                    <div class="pt-md-0">
                        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                            aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a class="color-7 text-decoration-none" href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item d-none d-sm-block"><a
                                        class="color-7 text-decoration-none" href="{{ route('show.page.story', $story->slug) }}">{{ $story->title }}</a>
                                </li>
                                <li class="breadcrumb-item d-block d-sm-none"><a
                                        class="color-7 text-decoration-none" href="{{ route('show.page.story', $story->slug) }}" title="{{ $story->title }}">{{ Str::limit($story->title, 15) }}</a>
                                </li>
                                <li class="breadcrumb-item active color-7 d-none d-sm-block" aria-current="page">Chương {{ $chapter->number }}</li>
                                <li class="breadcrumb-item active color-7 d-block d-sm-none" aria-current="page" title="{{ $chapter->number }}">Chương {{ Str::limit($chapter->number, 18) }}</li>
                            </ol>
                        </nav>
                        <div class="chapter-header text-center mb-4 animate__animated animate__fadeIn">
                            <h1 class="chapter-title fw-bold color-7">

                                {{ $chapter->title && trim($chapter->title) !== 'Chương ' . $chapter->number
                                    ? $chapter->title
                                    : 'Chương ' . $chapter->number }}

                            </h1>

                            <div class="chapter-meta d-flex justify-content-center align-items-center flex-wrap gap-2 mt-2">

                                <span class="fw-bold fs-4 text-muted">Chương {{ $chapter->number }}</span>
                            </div>
                        </div>

                        <div class="chapter-nav my-4 animate__animated animate__fadeIn animate__delay-1s">
                            <div class="d-flex w-100 gap-2">
                                @if ($prevChapter)
                                    <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $prevChapter->slug]) }}"
                                        class="btn bg-7 rounded-1 btn-prev text-dark d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Trước</span>
                                    </a>
                                @else
                                    <button disabled
                                        class="btn btn-outline-secondary rounded-1 btn-prev d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Trước</span>
                                    </button>
                                @endif

                                <div class="dropdown chapter-list-dropdown" style="flex: 1;">
                                    <button
                                        class="btn dropdown-toggle rounded-1 bg-7 text-dark fw-bold py-2 w-100 d-flex align-items-center justify-content-center h-100"
                                        type="button" id="chapterListDropdown" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-file-lines"></i>
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
                                        class="btn bg-7 text-dark btn-next rounded-1 d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <span class="d-none d-sm-inline">Sau</span> <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                @else
                                    <button disabled
                                        class="btn btn-outline-secondary btn-next rounded-1 d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <span class="d-none d-sm-inline">Sau</span> <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Chapter Content -->
                        <div id="chapter-content" class="rounded-4 chapter-content mb-4">
                            @if (isset($hasAccess) && $hasAccess)
                                <input type="hidden" id="chapter-id" value="{{ $chapter->id }}">
                                <div id="chapter-canvas-content" style="line-height: 2;"></div>
                            @else
                                <div class="chapter-preview">
                                    <!-- Hiển thị thông báo mua chương -->
                                    <div class="purchase-notice p-4 rounded-3 text-center my-4">
                                        <div class="mb-3">
                                            <p class="mb-0">Bạn cần ủng hộ <span class="fw-semibold color-7">{{ $chapter->price }} Cám</span>  để đọc
                                                chương
                                                này</p>
                                            @if ($story->has_combo && $story->combo_price > 0 && $story->total_chapter_price > 0 && $story->combo_price < $story->total_chapter_price)
                                                @php
                                                    $discountPercent = round((($story->total_chapter_price - $story->combo_price) / $story->total_chapter_price) * 100);
                                                @endphp
                                                <p class="mb-0 mt-2">Hoặc ủng hộ <span class="fw-semibold color-7">{{ number_format($story->combo_price) }} Cám</span> để đọc trọn bộ (<span class="text-danger fw-bold">Giảm {{ $discountPercent }}%</span> so với mua lẻ từng chương)</p>
                                            @endif
                                            @auth
                                                <p class="mb-0">
                                                    Hiện bạn đang có <span class="fw-semibold color-7">{{ auth()->user()->coins }} Cám</span>
                                                </p>
                                            @endauth

                                            @guest
                                                <p class="mb-0">
                                                    Bạn cần <a class="fw-semibold color-7" href="{{ route('login') }}">Đăng nhập</a> để
                                                    đọc
                                                    truyện
                                                </p>
                                            @endguest

                                            <div class="dots">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </div>

                                            <div class="d-flex justify-content-center">
                                                <ul class="text-start">
                                                    <li>
                                                        Sau khi mua, bạn có thể <span class="fw-semibold color-7">[Đọc chương]</span> này không giới hạn số lần.
                                                    </li>
                                                    <li>
                                                        Bạn chỉ bị trừ Cám khi <span class="fw-semibold color-7">[Đọc chương]</span> này lần đầu tiên.
                                                    </li>
                                                    <li>
                                                        Kiểm tra Cám hiện tại <a href="{{ route('user.profile') }}"
                                                            class="fw-semibold color-7">Tài khoản</a>. Nạp thêm <span class="fw-semibold color-7">Cám</span> tại <a
                                                            href="{{ route('user.bank.auto.deposit') }}" class="fw-semibold color-7">Nạp
                                                            Cám</a>.
                                                    </li>
                                                    <li>
                                                        Nếu có thắc mắc hoặc cần hỗ trợ nạp liên hệ <a
                                                            href="{{ \App\Models\Config::getConfig('facebook_page_url', 'https://www.facebook.com/profile.php?id=61572454674711') }}"
                                                            class="fw-semibold color-7">Facebook</a>.
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>



                                        @if ($chapter->price > 0)
                                            @guest
                                                <a href="{{ route('login') }}"
                                                    class="btn bg-7 fw-bold rounded-1 px-2 py-3">ĐĂNG NHẬP</a>
                                            @else
                                                <form action="{{ route('purchase.chapter') }}" method="POST"
                                                    class="purchase-form" id="purchase-chapter-form">
                                                    @csrf
                                                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                                    <button type="button"
                                                        class="btn bg-7 purchase-chapter-btn fw-bold rounded-1 px-2 py-3"
                                                        onclick="showPurchaseModal('chapter', {{ $chapter->id }}, 'Chương {{ $chapter->number }}: {{ $chapter->title }}', {{ $chapter->price }}, {{ $story->id }}, '{{ addslashes($story->title) }}', {{ $story->has_combo ? ($story->combo_price ?? 0) : 0 }}, {{ $story->total_chapter_price ?? 0 }})">
                                                        <i class="fas fa-shopping-cart me-1"></i> ĐỌC CHƯƠNG
                                                    </button>
                                                </form>
                                            @endguest
                                        @endif

                                        {{-- @if ($story->combo_price > 0)
                                        <div class="story-purchase-option p-3 border rounded bg-light">
                                            <h5 class="fw-bold">Mua trọn bộ truyện</h5>
                                            <p class="price mb-2"><i class="fas fa-coins text-warning"></i>
                                                {{ number_format($story->combo_price) }} cám</p>
                                            <p class="text-success small">

                                                <i class="fas fa-check-circle"></i> Truy cập tất cả
                                                {{ $story->chapters_count ?? 0 }} chương
                                            </p>
                                            @guest
                                                <a href="{{ route('login') }}" class="btn btn-success">Đăng nhập để
                                                    mua</a>
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
                                    @endif --}}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Chapter Navigation Bottom -->
                        <div class="chapter-nav my-4 animate__animated animate__fadeIn animate__delay-1s">
                            <div class="d-flex w-100 gap-2">
                                @if ($prevChapter)
                                    <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $prevChapter->slug]) }}"
                                        class="btn bg-7 rounded-1 btn-prev text-dark d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <i class="fas fa-arrow-left me-1"></i> <span
                                            class="d-none d-sm-inline">Trước</span>
                                    </a>
                                @else
                                    <button disabled
                                        class="btn btn-outline-secondary rounded-1 btn-prev d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <i class="fas fa-arrow-left me-1"></i> <span
                                            class="d-none d-sm-inline">Trước</span>
                                    </button>
                                @endif

                                <div class="dropdown chapter-list-dropdown" style="flex: 1;">
                                    <button
                                        class="btn dropdown-toggle rounded-1 bg-7 text-dark fw-bold py-2 w-100 d-flex align-items-center justify-content-center h-100"
                                        type="button" id="chapterListDropdown" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </button>
                                    <div class="dropdown-menu chapter-dropdown-menu"
                                        aria-labelledby="chapterListDropdown">
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
                                        class="btn bg-7 text-dark btn-next rounded-1 d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <span class="d-none d-sm-inline">Sau</span> <i
                                            class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                @else
                                    <button disabled
                                        class="btn btn-outline-secondary btn-next rounded-1 d-flex align-items-center justify-content-center fw-bold py-2"
                                        style="flex: 2;">
                                        <span class="d-none d-sm-inline">Sau</span> <i
                                            class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="text-center">
                            @auth
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#reportChapterModal">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Báo lỗi chương
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Báo lỗi chương
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="container">

        @if (!Auth()->check() || (Auth()->check() && Auth()->user()->ban_comment == false))
            @include('components.comment', [
                'story' => $story,
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

    {{-- @include('components.list_story_de_xuat', ['newStories' => $newStories]) --}}

    @auth
        @include('components.modals.chapter-purchase-modal')
        @include('components.modals.chapter-report-modal')
    @endauth
@endsection



@push('styles')
    <style>
        .dots span {
            width: 10px;
            height: 10px;
            background: var(--primary-color-7);
            border-radius: 50%;
            display: inline-block;
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

    <!-- Script render nội dung bằng canvas để chống copy -->
    <script>
        (function() {
            window.canvasChapterRenderer = {
                cachedContent: null,
                isRendering: false,
                updateTimer: null
            };

            function renderContentWithCanvas() {
                const chapterIdInput = document.getElementById('chapter-id');
                const canvasContent = document.getElementById('chapter-canvas-content');
                
                if (!chapterIdInput || !canvasContent) return;
                
                const chapterId = chapterIdInput.value;
                
                fetch(`/api/chapter/${chapterId}/content`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Không thể tải nội dung chapter.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const fullText = data.content;
                        window.canvasChapterRenderer.cachedContent = fullText;
                        updateCanvasContent();
                    })
                    .catch(error => {
                        console.error('Error loading chapter content:', error);
                        const canvasContent = document.getElementById('chapter-canvas-content');
                        if (canvasContent) {
                            canvasContent.innerHTML = '<p class="text-danger">Không thể tải nội dung chapter. Vui lòng thử lại.</p>';
                        }
                    });
            }
            
            function renderTextToCanvas(fullText, canvasContent, preserveScroll = false) {
                if (!fullText || !canvasContent) return;
                
                let scrollPosition = 0;
                let scrollElement = null;
                if (preserveScroll) {
                    scrollPosition = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
                    scrollElement = window;
                }
                
                const chapterContentContainer = canvasContent.closest('.chapter-content');
                if (!chapterContentContainer) return;
                
                canvasContent.innerHTML = '';
                
                const paragraphs = fullText.split(/\n\s*\n|<br\s*\/?>/i).filter(p => p.trim().length > 0);
                
                requestAnimationFrame(() => {
                    const computedStyle = window.getComputedStyle(chapterContentContainer);
                    let textColor = computedStyle.color || '#212529';
                    if (!textColor || textColor === 'rgba(0, 0, 0, 0)' || textColor === 'transparent') {
                        textColor = '#212529';
                    }
                    const fontSize = parseFloat(computedStyle.fontSize) || 16;
                    const fontFamily = computedStyle.fontFamily || 'Arial, sans-serif';
                    let fontWeight = computedStyle.fontWeight || 'normal';
                    console.log(fontWeight);
                    if (fontWeight === '400' || fontWeight === 400) {
                        fontWeight = 'normal';
                    } else if (fontWeight === '700' || fontWeight === 700) {
                        fontWeight = 'bold';
                    } else if (typeof fontWeight === 'number') {
                        fontWeight = fontWeight.toString();
                    }
                    const fontStyle = computedStyle.fontStyle || 'normal';
                    const lineHeight = parseFloat(computedStyle.lineHeight) || fontSize * 2;
                    
                    const containerStyle = window.getComputedStyle(chapterContentContainer);
                    const containerPaddingLeft = parseFloat(containerStyle.paddingLeft) || 0;
                    const containerPaddingRight = parseFloat(containerStyle.paddingRight) || 0;
                    const containerWidth = chapterContentContainer.offsetWidth;
                    const maxWidth = containerWidth - containerPaddingLeft - containerPaddingRight;
                    
                    const fragment = document.createDocumentFragment();
                    const canvasElements = [];
                    
                    paragraphs.forEach((paragraph, index) => {
                        if (index % 2 === 0) {
                            const canvas = document.createElement('canvas');
                            canvas.className = 'canvas-text-paragraph';
                            canvas.style.width = '100%';
                            canvas.style.maxWidth = '100%';
                            canvas.style.height = 'auto';
                            canvas.style.display = 'block';
                            canvas.style.userSelect = 'none';
                            canvas.style.pointerEvents = 'none';
                            canvas.style.boxSizing = 'border-box';
                            
                            canvas.addEventListener('contextmenu', e => e.preventDefault());
                            canvas.addEventListener('copy', e => e.preventDefault());
                            canvas.addEventListener('cut', e => e.preventDefault());
                            canvas.addEventListener('selectstart', e => e.preventDefault());
                            
                            fragment.appendChild(canvas);
                            canvasElements.push({ canvas, paragraph, index });
                        } else {
                            const p = document.createElement('p');
                            p.style.marginBottom = '1rem';
                            p.style.whiteSpace = 'pre-wrap';
                            p.textContent = paragraph.trim();
                            fragment.appendChild(p);
                        }
                    });
                    
                    canvasContent.appendChild(fragment);
                    
                    requestAnimationFrame(() => {
                        canvasElements.forEach(({ canvas, paragraph }) => {
                            const actualCanvasWidth = canvas.offsetWidth || maxWidth;
                            
                            const ctx = canvas.getContext('2d', { alpha: true });
                            
                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            
                            if (ctx.textRenderingOptimization !== undefined) {
                                ctx.textRenderingOptimization = 'optimizeQuality';
                            }
                            
                            ctx.font = `${fontStyle} ${fontWeight} ${fontSize}px ${fontFamily}`;
                            ctx.fillStyle = textColor;
                            ctx.textBaseline = 'top';
                            ctx.textAlign = 'left';
                            
                            const words = paragraph.trim().split(/\s+/);
                            const lines = [];
                            let currentLine = '';
                            
                            words.forEach(word => {
                                const testLine = currentLine + (currentLine ? ' ' : '') + word;
                                const metrics = ctx.measureText(testLine);
                                
                                if (metrics.width > actualCanvasWidth && currentLine) {
                                    lines.push(currentLine);
                                    currentLine = word;
                                } else {
                                    currentLine = testLine;
                                }
                            });
                            if (currentLine) {
                                lines.push(currentLine);
                            }
                            
                            const dpr = window.devicePixelRatio || 1;
                            canvas.width = actualCanvasWidth * dpr;
                            canvas.height = (lines.length * lineHeight + 20) * dpr;
                            
                            canvas.style.height = (lines.length * lineHeight + 20) + 'px';
                            
                            ctx.scale(dpr, dpr);
                            
                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            
                            if (ctx.textRenderingOptimization !== undefined) {
                                ctx.textRenderingOptimization = 'optimizeQuality';
                            }
                            
                            ctx.font = `${fontStyle} ${fontWeight} ${fontSize}px ${fontFamily}`;
                            ctx.fillStyle = textColor;
                            ctx.textBaseline = 'top';
                            ctx.textAlign = 'left';
                            
                            lines.forEach((line, lineIndex) => {
                                ctx.fillText(line, 0.5, (lineIndex * lineHeight) + 10.5);
                            });
                        });
                        
                        if (preserveScroll && scrollElement) {
                            requestAnimationFrame(() => {
                                window.scrollTo(0, scrollPosition);
                            });
                        }
                        
                        window.canvasChapterRenderer.isRendering = false;
                    });
                });
            }
            
            window.updateCanvasContent = function(preserveScroll = true) {
                const canvasContent = document.getElementById('chapter-canvas-content');
                if (!canvasContent || !window.canvasChapterRenderer.cachedContent) return;
                
                if (window.canvasChapterRenderer.updateTimer) {
                    clearTimeout(window.canvasChapterRenderer.updateTimer);
                }
                
                if (window.canvasChapterRenderer.isRendering) {
                    window.canvasChapterRenderer.updateTimer = setTimeout(() => {
                        window.updateCanvasContent(preserveScroll);
                    }, 10);
                    return;
                }
                
                requestAnimationFrame(() => {
                    window.canvasChapterRenderer.isRendering = true;
                    renderTextToCanvas(window.canvasChapterRenderer.cachedContent, canvasContent, preserveScroll);
                });
            };
            
            function initCanvasRender() {
                if (document.getElementById('chapter-id') && document.getElementById('chapter-canvas-content')) {
                    setTimeout(() => {
                        renderContentWithCanvas();
                    }, 100);
                }
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                initCanvasRender();
                
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if (window.canvasChapterRenderer.cachedContent) {
                            window.updateCanvasContent(true);
                        }
                    }, 250);
                });
            });
        })();
    </script>

    <!-- Script xử lý đánh dấu trang (bookmark) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                const bookmarkBtn = document.querySelector('.bookmark-btn');
                @auth
                checkBookmarkStatus();

                bookmarkBtn.addEventListener('click', toggleBookmark);
            @else
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

                    const submitBtn = passwordForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang kiểm tra...';
                    submitBtn.disabled = true;

                    fetch('{{ route('chapter.check-password', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
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
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }

            const dropdownButtons = document.querySelectorAll('.chapter-list-dropdown .btn');
            dropdownButtons.forEach(button => {
                button.addEventListener('click', function() {
                    setTimeout(() => {
                        const dropdown = this.nextElementSibling;
                        if (dropdown && dropdown.classList.contains('show')) {
                            const rect = dropdown.getBoundingClientRect();
                            const windowHeight = window.innerHeight;

                            if (rect.bottom > windowHeight) {
                                dropdown.style.top = 'auto';
                                dropdown.style.bottom = 'calc(100% + 5px)';
                            } else {
                                dropdown.style.top = 'calc(100% + 5px)';
                                dropdown.style.bottom = 'auto';
                            }

                            dropdown.style.zIndex = '9999';
                        }
                    }, 0);
                });
            });
        });
    </script>
@endpush
