<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        @php
          
            $logoPath =
                $logoSite && $logoSite->logo
                    ? Storage::url($logoSite->logo)
                    : asset('images/logo/logo-site.png');
        @endphp
        <a class="d-flex m-0 justify-content-center text-wrap" href="{{ route('home') }}">
            <img height="70" class="logof_site" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
        </a>
    </div>
    <hr class="horizontal dark mt-0">

    <div class="docs-info">
        <a href="{{ route('home') }}" class="btn btn-white btn-sm w-100 mb-0">Trang chủ</a>
    </div>

    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                    <g transform="translate(1716.000000, 291.000000)">
                                        <g transform="translate(0.000000, 148.000000)">
                                            <path class="color-background opacity-6"
                                                d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z">
                                            </path>
                                            <path class="color-background"
                                                d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z">
                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('dashboard') }}</span>
                </a>
            </li>


            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Chức năng</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-book text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Danh sách thể loại</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.stories.*', 'admin.stories.chapters.*') ? 'active' : '' }}"
                    href="{{ route('admin.stories.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-layer-group text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Danh sách truyện</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.banners.*') ? 'active' : '' }}"
                    href="{{ route('admin.banners.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-image text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Banners</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.banks.*') ? 'active' : '' }}"
                    href="{{ route('admin.banks.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-university text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Quản lý Ngân hàng</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.deposits.*', 'admin.request.payments.*') ? 'active' : '' }}"
                    href="{{ route('admin.deposits.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-university text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Nạp xu - Bank</span>
                    @php
                        $pendingDepositsCount = \App\Models\Deposit::where('status', 'pending')->count();
                    @endphp
                    @if ($pendingDepositsCount > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingDepositsCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.paypal-deposits.*') ? 'active' : '' }}"
                    href="{{ route('admin.paypal-deposits.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fab fa-paypal text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Nạp xu - PayPal
                        @php
                            $pendingPaypalCount = \App\Models\PaypalDeposit::where('status', 'processing')->count();
                        @endphp
                        @if ($pendingPaypalCount > 0)
                            <span class="badge bg-danger ms-2">{{ $pendingPaypalCount }}</span>
                        @endif
                    </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.card-deposits.*') ? 'active' : '' }}"
                    href="{{ route('admin.card-deposits.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-credit-card text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Nạp xu - Thẻ cào</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.comments.all') ? 'active' : '' }}"
                    href="{{ route('admin.comments.all') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-comments text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Quản lý Bình luận
                        @php
                            $pendingCommentsCount = \App\Models\Comment::where('approval_status', 'pending')
                                ->whereHas('user', function($q) {
                                    $q->where('role', '!=', 'admin');
                                })
                                ->whereDoesntHave('story', function($q) {
                                    $q->whereColumn('stories.user_id', 'comments.user_id');
                                })
                                ->count();
                        @endphp
                        @if ($pendingCommentsCount > 0)
                            <span class="badge bg-danger ms-2">{{ $pendingCommentsCount }}</span>
                        @endif
                    </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-users text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Danh sách User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.coins.*') ? 'active' : '' }}"
                    href="{{ route('admin.coins.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-coins text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Quản lý xu</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.coin.transactions') ? 'active' : '' }}"
                    href="{{ route('admin.coin.transactions') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-history text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Kiểm soát xu thủ công</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.coin-history.index') ? 'active' : '' }}"
                    href="{{ route('admin.coin-history.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-history text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Lịch sử xu</span>
                </a>
            </li>

            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Cài đặt</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.configs.*') ? 'active' : '' }}"
                    href="{{ route('admin.configs.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-gears text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Cấu hình hệ thống</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.logo-site.*') ? 'active' : '' }}"
                    href="{{ route('admin.logo-site.edit') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-images text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Logo</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.daily-tasks.*') ? 'active' : '' }}"
                    href="{{ route('admin.daily-tasks.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-tasks text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Quản lý nhiệm vụ</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteNamed('admin.socials.*') ? 'active' : '' }}"
                    href="{{ route('admin.socials.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-share-nodes text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Liên hệ</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.guide.edit') }}"
                    class="nav-link {{ request()->routeIs('admin.guide.*') ? 'active' : '' }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-book text-dark icon-sidebar"></i>
                    </div>
                    <span class="nav-link-text ms-1">Quản lý Hướng dẫn</span>
                </a>
            </li>



            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Tài khoản</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-right-from-bracket text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Đăng xuất</span>
                </a>
            </li>
        </ul>
    </div>

</aside>
