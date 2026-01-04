@extends('admin.layouts.app')
@push('styles-admin')
    <style>
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .toast {
            min-width: 250px;
        }
        
        .stats-card {
            transition: all 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .nav-tabs .nav-link {
            position: relative;
        }
        
        .nav-tabs .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #5e72e4 0%, #825ee4 100%);
        }
    </style>
@endpush
@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <h5 class="mb-0">Chi tiết người dùng</h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                class="rounded-circle img-fluid mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            @if ($user->avatar && auth()->user()->role === 'admin_main' || auth()->user()->role === 'admin_sub')
                                <button class="btn btn-danger btn-sm" id="delete-avatar">
                                    <i class="fas fa-trash"></i> Xóa ảnh
                                </button>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <h6 class="text-sm">Tên người dùng</h6>
                                <p class="text-dark mb-0">{{ $user->name }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-sm">Email</h6>
                                <p class="text-dark mb-0">{{ $user->email }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-sm">Ngày tham gia</h6>
                                <p class="text-dark mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-sm">IP Address</h6>
                                <p class="text-dark mb-0">{{ $user->ip_address ?: 'Không có' }}</p>
                            </div>

                            @php 
                                $superAdminEmails = array_map('trim', explode(',', env('SUPER_ADMIN_EMAILS', 'admin@gmail.com')));
                                $isSuperAdmin = in_array(strtolower(trim(auth()->user()->email)), array_map('strtolower', $superAdminEmails));
                                $canChangeRole = false;
                                $availableRoles = [];
                                
                                if ($user->id !== auth()->id() && !in_array(strtolower(trim($user->email)), array_map('strtolower', $superAdminEmails))) {
                                    if (auth()->user()->role === 'admin_main') {
                                        $canChangeRole = true;
                                        if ($isSuperAdmin) {
                                            // Super Admin có thể đổi tất cả role
                                            $availableRoles = ['user', 'admin_sub', 'admin_main'];
                                        } else {
                                            // admin_main thường chỉ có thể đổi user và admin_sub
                                            if ($user->role === 'admin_main') {
                                                $canChangeRole = false; // Không thể đổi admin_main khác
                                            } else {
                                                $availableRoles = ['user', 'admin_sub', 'admin_main'];
                                            }
                                        }
                                    } elseif (auth()->user()->role === 'admin_sub') {
                                        if ($user->role !== 'admin_main') {
                                            $canChangeRole = true;
                                            $availableRoles = ['user', 'admin_sub'];
                                        }
                                    }
                                }
                            @endphp
                            <div class="mb-3">
                                <h6 class="text-sm">Vai trò</h6>
                                @if ($canChangeRole)
                                    <select class="form-select form-select-sm w-auto" id="role-select">
                                        @foreach($availableRoles as $role)
                                            <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                                {{ $role === 'admin_main' ? 'Admin' : ($role === 'admin_sub' ? 'Admin Sub' : 'User') }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="text-dark mb-0">{{ ucfirst($user->role) }}</p>
                                @endif
                            </div>
                           
                            <div class="mb-3">
                                <h6 class="text-sm mb-3">Trạng thái hạn chế</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input ban-toggle" type="checkbox" data-type="login"
                                            {{ $user->userBan->login ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Cấm đăng nhập
                                            @if($user->userBan->login && $user->userBan->rate_limit_ban)
                                                <span class="badge bg-warning text-dark ms-1" title="Ban do rate limit">RL</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input ban-toggle" type="checkbox" data-type="comment"
                                            {{ $user->userBan->comment ? 'checked' : '' }}>
                                        <label class="form-check-label">Cấm bình luận</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input ban-toggle" type="checkbox" data-type="rate"
                                            {{ $user->userBan->rate ? 'checked' : '' }}>
                                        <label class="form-check-label">Cấm đánh giá</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input ban-toggle" type="checkbox" data-type="read"
                                            {{ $user->userBan->read ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            Cấm đọc truyện
                                            @if($user->userBan->read && $user->userBan->rate_limit_ban)
                                                <span class="badge bg-warning text-dark ms-1" title="Ban do rate limit">RL</span>
                                            @endif
                                        </label>
                                    </div>

                                    @if (auth()->user()->role === 'admin_main' || auth()->user()->role === 'admin_sub')
                                        <div class="form-check form-switch">
                                            <input class="form-check-input ban-toggle" type="checkbox" data-type="ip"
                                                {{ $user->banIps()->exists() ? 'checked' : '' }}>
                                            <label class="form-check-label">Cấm IP</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Financial Statistics -->
                    <div class="row mt-4 g-3">
                        <div class="col-12">
                            <h5 class="mb-3">Thống kê tài chính</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="text-white mb-0">{{ number_format($stats['balance']) }}</h5>
                                            <p class="mb-0 text-sm">Số cám hiện tại</p>
                                        </div>
                                        <div class="icon-shape bg-white text-center rounded-circle shadow">
                                            <i class="fas fa-coins text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card bg-gradient-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="text-white mb-0">{{ number_format($stats['total_deposits']) }}</h5>
                                            <p class="mb-0 text-sm">Tổng cám đã nạp</p>
                                        </div>
                                        <div class="icon-shape bg-white text-center rounded-circle shadow">
                                            <i class="fas fa-wallet text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card bg-gradient-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="text-white mb-0">{{ number_format($stats['total_spent']) }}</h5>
                                            <p class="mb-0 text-sm">Tổng cám đã chi</p>
                                        </div>
                                        <div class="icon-shape bg-white text-center rounded-circle shadow">
                                            <i class="fas fa-shopping-cart text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($user->role === 'admin_main' && auth()->user()->role === 'admin_main')
                        <div class="col-md-3">
                            <div class="card stats-card bg-gradient-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="text-white mb-0">{{ number_format($stats['total_spent']) }}</h5>
                                            <p class="mb-0 text-sm">Doanh thu</p>
                                            <small class="text-white-50">
                                                Từ việc mua truyện và chương
                                            </small>
                                        </div>
                                        <div class="icon-shape bg-white text-center rounded-circle shadow">
                                            <i class="fas fa-hand-holding-usd text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mt-4" role="tablist">
                        @if(auth()->user()->role === 'admin_main')
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#deposits" role="tab">
                                <i class="fas fa-wallet me-1"></i> Nạp cám (Bank)
                                <span class="badge bg-primary rounded-pill">{{ $counts['deposits'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bank-auto-deposits" role="tab">
                                <i class="fas fa-university me-1"></i> Nạp cám (Bank Auto)
                                <span class="badge bg-primary rounded-pill">{{ $counts['bank_auto_deposits'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#paypal-deposits" role="tab">
                                <i class="fab fa-paypal me-1"></i> Nạp PayPal
                                <span class="badge bg-primary rounded-pill">{{ $counts['paypal_deposits'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#card-deposits" role="tab">
                                <i class="fas fa-credit-card me-1"></i> Nạp thẻ
                                <span class="badge bg-primary rounded-pill">{{ $counts['card_deposits'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#story-purchases" role="tab">
                                <i class="fas fa-shopping-cart me-1"></i> Mua truyện
                                <span class="badge bg-primary rounded-pill">{{ $counts['story_purchases'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#chapter-purchases" role="tab">
                                <i class="fas fa-file-invoice-dollar me-1"></i> Mua chương
                                <span class="badge bg-primary rounded-pill">{{ $counts['chapter_purchases'] }}</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bookmarks" role="tab">
                                <i class="fas fa-bookmark me-1"></i> Theo dõi
                                <span class="badge bg-primary rounded-pill">{{ $counts['bookmarks'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#user-daily-tasks" role="tab">
                                <i class="fas fa-tasks me-1"></i> Nhiệm vụ
                                <span class="badge bg-primary rounded-pill">{{ $counts['user_daily_tasks'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#coin-transactions" role="tab">
                                <i class="fas fa-coins me-1"></i> Cộng/Trừ cám
                                <span class="badge bg-primary rounded-pill">{{ $counts['coin_transactions'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#coin-history" role="tab">
                                <i class="fas fa-history me-1"></i> Lịch sử cám
                                <span class="badge bg-info rounded-pill">{{ $counts['coin_histories'] ?? 0 }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab panes -->
                    <div class="tab-content">
                        @if(auth()->user()->role === 'admin_main')
                        <!-- Deposits Tab -->
                        <div class="tab-pane active" id="deposits" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ngân hàng</th>
                                            <th>Mã giao dịch</th>
                                            <th>Số tiền</th>
                                            <th>Số cám</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày nạp</th>
                                            <th>Ngày duyệt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($deposits as $deposit)
                                            <tr>
                                                <td>{{ $deposit->id }}</td>
                                                <td>{{ $deposit->bank->name ?? 'N/A' }}</td>
                                                <td>{{ $deposit->transaction_code }}</td>
                                                <td>{{ number_format($deposit->amount) }}đ</td>
                                                <td>{{ number_format($deposit->coins) }}</td>
                                                <td>
                                                    @if($deposit->status === 'approved')
                                                        <span class="badge bg-success">Đã duyệt</span>
                                                    @elseif($deposit->status === 'rejected')
                                                        <span class="badge bg-danger">Từ chối</span>
                                                    @else
                                                        <span class="badge bg-warning">Chờ duyệt</span>
                                                    @endif
                                                </td>
                                                <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $deposit->approved_at ? $deposit->approved_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Chưa có giao dịch nạp cám</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['deposits'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$deposits" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="deposits">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Bank Auto Deposits Tab -->
                        <div class="tab-pane" id="bank-auto-deposits" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ngân hàng</th>
                                            <th>Mã giao dịch</th>
                                            <th>Số tiền</th>
                                            <th>Số cám</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày nạp</th>
                                            <th>Ngày xử lý</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bankAutoDeposits as $deposit)
                                            <tr>
                                                <td>{{ $deposit->id }}</td>
                                                <td>{{ $deposit->bank->name ?? 'N/A' }}</td>
                                                <td>{{ $deposit->transaction_code }}</td>
                                                <td>{{ number_format($deposit->amount) }}đ</td>
                                                <td>{{ number_format($deposit->total_coins) }}</td>
                                                <td>
                                                    @if($deposit->status === 'success')
                                                        <span class="badge bg-success">Thành công</span>
                                                    @elseif($deposit->status === 'failed')
                                                        <span class="badge bg-danger">Thất bại</span>
                                                    @elseif($deposit->status === 'cancelled')
                                                        <span class="badge bg-secondary">Đã hủy</span>
                                                    @else
                                                        <span class="badge bg-warning">Đang xử lý</span>
                                                    @endif
                                                </td>
                                                <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $deposit->processed_at ? $deposit->processed_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Chưa có giao dịch nạp cám Bank Auto</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if(($counts['bank_auto_deposits'] ?? 0) > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$bankAutoDeposits" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="bank-auto-deposits">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- PayPal Deposits Tab -->
                        <div class="tab-pane" id="paypal-deposits" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Mã giao dịch</th>
                                            <th>Số tiền USD</th>
                                            <th>Số cám</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày nạp</th>
                                            <th>Ngày duyệt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paypalDeposits as $deposit)
                                            <tr>
                                                <td>{{ $deposit->id }}</td>
                                                <td>{{ $deposit->transaction_code }}</td>
                                                <td>{{ $deposit->usd_amount_formatted }}</td>
                                                <td>{{ $deposit->coins_formatted }}</td>
                                                <td>
                                                    <span class="badge {{ $deposit->status_badge }}">{{ $deposit->status_text }}</span>
                                                </td>
                                                <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $deposit->processed_at ? $deposit->processed_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Chưa có giao dịch nạp PayPal</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['paypal_deposits'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$paypalDeposits" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="paypal-deposits">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Card Deposits Tab -->
                        <div class="tab-pane" id="card-deposits" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Loại thẻ</th>
                                            <th>Serial</th>
                                            <th>Mệnh giá</th>
                                            <th>Số cám</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày nạp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cardDeposits as $deposit)
                                            <tr>
                                                <td>{{ $deposit->id }}</td>
                                                <td>{{ $deposit->card_type_name }}</td>
                                                <td>{{ $deposit->serial }}</td>
                                                <td>{{ $deposit->amount_formatted }}</td>
                                                <td>{{ $deposit->coins_formatted }}</td>
                                                <td>
                                                    <span class="badge {{ $deposit->status_badge }}">{{ $deposit->status_text }}</span>
                                                </td>
                                                <td>{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Chưa có giao dịch nạp thẻ</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['card_deposits'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$cardDeposits" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="card-deposits">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Story Purchases Tab -->
                        <div class="tab-pane" id="story-purchases" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Truyện</th>
                                            <th>Số cám</th>
                                            <th>Ngày mua</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($storyPurchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->id }}</td>
                                                <td>
                                                    <a href="{{ route('admin.stories.show', $purchase->story_id) }}">
                                                        {{ $purchase->story->title ?? 'Không xác định' }}
                                                    </a>
                                                </td>
                                                <td>{{ number_format($purchase->amount_paid) }}</td>
                                                <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Chưa có giao dịch mua truyện</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['story_purchases'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$storyPurchases" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="story-purchases">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Chapter Purchases Tab -->
                        <div class="tab-pane" id="chapter-purchases" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Truyện</th>
                                            <th>Chương</th>
                                            <th>Số cám</th>
                                            <th>Ngày mua</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($chapterPurchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->id }}</td>
                                                <td>
                                                    <a href="{{ route('admin.stories.show', $purchase->chapter->story_id) }}">
                                                        {{ $purchase->chapter->story->title ?? 'Không xác định' }}
                                                    </a>
                                                </td>
                                                <td>Chương {{ $purchase->chapter->number }}: {{ Str::limit($purchase->chapter->title, 30) }}</td>
                                                <td>{{ number_format($purchase->amount_paid) }}</td>
                                                <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Chưa có giao dịch mua chương</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['chapter_purchases'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$chapterPurchases" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="chapter-purchases">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @endif
                        
                        <!-- Bookmarks Tab -->
                        <div class="tab-pane {{ auth()->user()->role === 'admin_sub' ? 'active' : '' }}" id="bookmarks" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Truyện</th>
                                            <th>Chương đã đọc</th>
                                            <th>Thông báo</th>
                                            <th>Ngày theo dõi</th>
                                            <th>Đọc gần nhất</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookmarks as $bookmark)
                                            <tr>
                                                <td>{{ $bookmark->id }}</td>
                                                <td>
                                                    <a href="{{ route('admin.stories.show', $bookmark->story_id) }}">
                                                        {{ $bookmark->story->title ?? 'Không xác định' }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($bookmark->lastChapter)
                                                        Chương {{ $bookmark->lastChapter->number }}: {{ Str::limit($bookmark->lastChapter->title, 30) }}
                                                    @else
                                                        Chưa đọc
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($bookmark->notification_enabled)
                                                        <span class="badge bg-success">Bật</span>
                                                    @else
                                                        <span class="badge bg-secondary">Tắt</span>
                                                    @endif
                                                </td>
                                                <td>{{ $bookmark->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $bookmark->last_read_at ? $bookmark->last_read_at->format('d/m/Y H:i') : 'Chưa đọc' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Chưa có truyện nào được theo dõi</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['bookmarks'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$bookmarks" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="bookmarks">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- User Daily Tasks Tab -->
                        <div class="tab-pane" id="user-daily-tasks" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nhiệm vụ</th>
                                            <th>Ngày thực hiện</th>
                                            <th>Số lần hoàn thành</th>
                                            <th>Lần cuối</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($userDailyTasks as $task)
                                            <tr>
                                                <td>{{ $task->id }}</td>
                                                <td>{{ $task->dailyTask->name ?? 'N/A' }}</td>
                                                <td>{{ $task->task_date->format('d/m/Y') }}</td>
                                                <td>{{ $task->completed_count }}</td>
                                                <td>{{ $task->last_completed_at ? $task->last_completed_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Chưa có nhiệm vụ nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['user_daily_tasks'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$userDailyTasks" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm bg-gradient-primary load-more" data-type="user-daily-tasks">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        
                        <!-- Coin Transactions Tab -->
                        <div class="tab-pane" id="coin-transactions" role="tabpanel">
                            <div class="d-flex justify-content-end mt-3">
                                <a href="{{ route('admin.coins.create', $user->id) }}" class="btn bg-gradient-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Cộng/Trừ cám
                                </a>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Loại giao dịch</th>
                                            <th>Số cám</th>
                                            <th>Admin thực hiện</th>
                                            <th>Ghi chú</th>
                                            <th>Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($coinTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->id }}</td>
                                                <td>
                                                    @if($transaction->type === 'add')
                                                        <span class="badge bg-success">Cộng cám</span>
                                                    @else
                                                        <span class="badge bg-danger">Trừ cám</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($transaction->amount) }}</td>
                                                <td>{{ $transaction->admin->name ?? 'N/A' }}</td>
                                                <td>{{ $transaction->note ?? 'Không có ghi chú' }}</td>
                                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Chưa có giao dịch cám nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if($counts['coin_transactions'] > 5)
                                    <div class="d-flex justify-content-center mt-3">
                                        <x-pagination :paginator="$coinTransactions" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="coin-transactions">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Coin History Tab -->
                        <div class="tab-pane" id="coin-history" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Loại giao dịch</th>
                                            <th>Mô tả</th>
                                            <th>Số cám</th>
                                            <th>Số dư trước</th>
                                            <th>Số dư sau</th>
                                            <th>IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($coinHistories as $history)
                                            <tr>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ $history->created_at->format('d/m/Y') }}</span>
                                                        <small class="text-muted">{{ $history->created_at->format('H:i:s') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $history->type == 'add' ? 'success' : 'danger' }}">
                                                        {{ $history->transaction_type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span>{{ $history->description }}</span>
                                                        @if($history->reference)
                                                            <small class="text-muted">
                                                                Tham chiếu: {{ class_basename($history->reference_type) }} #{{ $history->reference_id }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-{{ $history->type == 'add' ? 'success' : 'danger' }}">
                                                        {{ $history->formatted_amount }} cám
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ number_format($history->balance_before) }} cám</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ number_format($history->balance_after) }} cám</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $history->ip_address }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                                        <p>Chưa có lịch sử cám nào</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                
                                <!-- Pagination -->
                                @if($coinHistories->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $coinHistories->links('components.pagination') }}
                                    </div>
                                    <div class="text-center mt-3">
                                        <button class="btn btn-sm btn-primary load-more" data-type="coin-histories">
                                            Xem thêm <i class="fas fa-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>
@endsection

@push('scripts-admin')
    <script>
        function showToast(message, type = 'success') {
            const toast = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

            $('.toast-container').append(toast);
            const toastElement = $('.toast-container .toast').last();
            const bsToast = new bootstrap.Toast(toastElement, {
                delay: 3000
            });
            bsToast.show();

            // Remove toast after it's hidden
            toastElement.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }

        $(document).ready(function() {
            // Handle tab navigation with URL hash
            const hash = window.location.hash;
            if (hash) {
                const triggerEl = document.querySelector(`a[href="${hash}"]`);
                if (triggerEl) {
                    triggerEl.click();
                }
            }
            
            // Update URL hash when tab changes
            const tabLinks = document.querySelectorAll('.nav-link');
            tabLinks.forEach(link => {
                link.addEventListener('click', function() {
                    window.location.hash = this.getAttribute('href');
                });
            });
            
            // Load more functionality
            $('.load-more').click(function() {
                const type = $(this).data('type');
                const currentPage = parseInt($(this).data('page') || 1);
                const nextPage = currentPage + 1;
                const button = $(this);
                
                button.html('<i class="fas fa-spinner fa-spin"></i> Đang tải...');
                button.prop('disabled', true);
                
                $.ajax({
                    url: '{{ route('admin.users.load-more', $user->id) }}',
                    type: 'GET',
                    data: {
                        type: type,
                        page: nextPage
                    },
                    success: function(response) {
                        // Append new rows to the table
                        $(`#${type} table tbody`).append(response.html);
                        
                        // Update pagination
                        $(`#${type} .justify-content-center`).html(response.pagination);
                        
                        // Update the load more button
                        button.data('page', nextPage);
                        button.html('Xem thêm <i class="fas fa-chevron-down ms-1"></i>');
                        button.prop('disabled', false);
                        
                        // Hide button if no more pages
                        if (!response.has_more) {
                            button.hide();
                        }
                    },
                    error: function() {
                        showToast('Có lỗi xảy ra khi tải dữ liệu', 'error');
                        button.html('Xem thêm <i class="fas fa-chevron-down ms-1"></i>');
                        button.prop('disabled', false);
                    }
                });
            });

            $('.ban-toggle').change(function() {
                const type = $(this).data('type');
                const value = $(this).prop('checked');
                const checkbox = $(this);

                if (type === 'ip') {
                    $.ajax({
                        url: '{{ route('admin.users.banip', $user->id) }}',
                        type: 'POST',
                        data: {
                            ban: value,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                showToast(res.message, 'success');
                            }
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                            checkbox.prop('checked', !value);
                        }
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.users.update', $user->id) }}',
                    type: 'PATCH',
                    data: {
                        [`ban_${type}`]: value,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            showToast(res.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                        checkbox.prop('checked', !value);
                    }
                });
            });
        });
    </script>


    {{-- edit role --}}
    <script>
        $(document).ready(function() {
            $('#role-select').change(function() {
                const newRole = $(this).val();
                const oldRole = $(this).find('option[selected]').val();

                if (confirm(
                        `Bạn có chắc muốn thay đổi quyền của người dùng thành ${newRole.toUpperCase()}?`)) {
                    $.ajax({
                        url: '{{ route('admin.users.update', $user->id) }}',
                        type: 'PATCH',
                        data: {
                            role: newRole,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                showToast(res.message, 'success');
                            }
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                            $(this).val(oldRole);
                        }
                    });
                } else {
                    $(this).val(oldRole);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#delete-avatar').click(function() {
                if (confirm('Bạn có chắc muốn xóa ảnh đại diện?')) {
                    $.ajax({
                        url: '{{ route('admin.users.update', $user->id) }}',
                        type: 'PATCH',
                        data: {
                            delete_avatar: true,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                showToast(res.message, 'success');
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
