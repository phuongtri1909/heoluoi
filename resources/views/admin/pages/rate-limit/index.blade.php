@extends('admin.layouts.app')

@push('styles-admin')
<style>
    @media (max-width: 1100px) {
        .rate-limit-filter .w-md-auto {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        
        .rate-limit-filter {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .rate-limit-filter form {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }
        
        .rate-limit-filter .d-flex.flex-column.flex-md-row {
            flex-direction: column !important;
            width: 100% !important;
        }
        
        .rate-limit-filter .d-flex.flex-column.flex-md-row > .d-flex.flex-column.flex-md-row {
            width: 100% !important;
            flex-direction: column !important;
            display: flex !important;
            flex-wrap: nowrap !important;
        }
        
        .rate-limit-filter .d-flex.flex-column.flex-md-row > .d-flex.flex-column.flex-md-row > * {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 0.5rem !important;
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
        }
        
        .rate-limit-filter .d-flex.flex-column.flex-md-row > .d-flex.flex-column.flex-md-row > *:last-child {
            margin-bottom: 0 !important;
        }
        
        .rate-limit-filter .input-group.input-group-sm {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            flex-shrink: 0 !important;
        }
        
        .rate-limit-filter .form-control,
        .rate-limit-filter .form-select {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
    }
    
    .violation-history-btn {
        white-space: nowrap;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0 rate-limit-filter">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Quản lý Rate Limit - Vi phạm</h5>
                            <p class="text-sm mb-0">
                                Danh sách người dùng có vi phạm rate limit
                            </p>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('admin.rate-limit.index') }}" class="mt-3">
                        <div class="d-flex flex-column flex-md-row gap-2 mb-2 mb-md-0">
                            <input type="text" 
                                   class="form-control form-control-sm w-100 w-md-auto" 
                                   name="search" 
                                   placeholder="Tìm kiếm..." 
                                   value="{{ request('search') }}">
                            
                            <select name="ban_type" class="form-select form-select-sm w-100 w-md-auto">
                                <option value="">Tất cả loại khóa</option>
                                <option value="permanent" {{ request('ban_type') == 'permanent' ? 'selected' : '' }}>Khóa vĩnh viễn</option>
                                <option value="temporary" {{ request('ban_type') == 'temporary' ? 'selected' : '' }}>Khóa tạm thời</option>
                                <option value="no_ban" {{ request('ban_type') == 'no_ban' ? 'selected' : '' }}>Chưa bị khóa</option>
                            </select>
                            
                            <input type="number" 
                                   class="form-control form-control-sm w-100 w-md-auto" 
                                   name="violation_today_min" 
                                   placeholder="Vi phạm hôm nay: Từ" 
                                   value="{{ request('violation_today_min') }}"
                                   min="0">
                            
                            <input type="number" 
                                   class="form-control form-control-sm w-100 w-md-auto" 
                                   name="total_violations_min" 
                                   placeholder="Tổng vi phạm: Từ" 
                                   value="{{ request('total_violations_min') }}"
                                   min="0">
                            
                            <input type="number" 
                                   class="form-control form-control-sm w-100 w-md-auto" 
                                   name="temp_ban_count_min" 
                                   placeholder="Khóa tạm thời: Từ" 
                                   value="{{ request('temp_ban_count_min') }}"
                                   min="0">
                            
                            <input type="number" 
                                   class="form-control form-control-sm w-100 w-md-auto" 
                                   name="permanent_ban_count_min" 
                                   placeholder="Khóa vĩnh viễn: Từ" 
                                   value="{{ request('permanent_ban_count_min') }}"
                                   min="0">

                            <div class="input-group input-group-sm w-100 w-md-auto">
                                <button class="btn bg-gradient-primary btn-sm px-2 mb-0" type="submit">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                @if(request('search') || request('ban_type') || request('violation_today_min') || request('total_violations_min') || request('temp_ban_count_min') || request('permanent_ban_count_min'))
                                    <a href="{{ route('admin.rate-limit.index') }}" class="btn btn-outline-secondary btn-sm px-2 mb-0">
                                        <i class="fa-solid fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">User</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Email</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Vi phạm hôm nay</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Tổng vi phạm</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Lịch sử vi phạm</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Lần vi phạm gần nhất</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Khóa tạm thời</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Khóa vĩnh viễn</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Loại khóa</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Thời gian hết hạn</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    @if($user->avatar)
                                                        <img src="{{ Storage::url($user->avatar) }}" 
                                                             class="avatar avatar-sm me-3 border-radius-lg" 
                                                             alt="user image">
                                                    @else
                                                        <div class="avatar avatar-sm me-3 bg-gradient-primary border-radius-lg text-white d-flex align-items-center justify-content-center">
                                                            {{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $user->name ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">ID: {{ $user->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-danger">{{ $user->violation_count_today }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-warning">{{ $user->total_violations }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if(isset($user->violations_by_date) && $user->violations_by_date->count() > 0)
                                                <button type="button" 
                                                        class="btn btn-sm btn-info violation-history-btn" 
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name ?? $user->email }}"
                                                        data-violations='@json($user->violations_by_date)'
                                                        title="Xem lịch sử vi phạm">
                                                    <i class="fa-solid fa-history"></i> <span class="d-none d-md-inline">Lịch sử</span> ({{ $user->violations_by_date->count() }})
                                                </button>
                                            @else
                                                <span class="text-xs text-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($user->rateLimitViolations->count() > 0)
                                                <span class="text-xs text-secondary">
                                                    {{ $user->rateLimitViolations->first()->violated_at->format('d/m/Y H:i') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-warning">{{ $user->temp_ban_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-danger">{{ $user->permanent_ban_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($user->ban_type === 'permanent')
                                                <span class="badge bg-danger">Khóa vĩnh viễn</span>
                                            @elseif($user->ban_type === 'temporary')
                                                <span class="badge bg-warning">Khóa tạm thời</span>
                                            @elseif($user->ban_type === 'no_ban')
                                                <span class="badge bg-info">Chưa bị khóa</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($user->ban_type === 'temporary' && isset($user->banned_until))
                                                <span class="text-xs text-secondary">
                                                    {{ $user->banned_until->format('d/m/Y H:i') }}
                                                    <br>
                                                    <small class="text-muted">
                                                        (Còn {{ $user->banned_until->diffForHumans() }})
                                                    </small>
                                                </span>
                                            @elseif($user->ban_type === 'permanent')
                                                <span class="text-xs text-muted">Vô thời hạn</span>
                                            @else
                                                <span class="text-xs text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($user->ban_type === 'permanent' || $user->ban_type === 'temporary')
                                                <button class="btn btn-sm btn-success unlock-rate-limit-btn" 
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name ?? $user->email }}">
                                                    <i class="fa-solid fa-unlock"></i> Mở khóa
                                                </button>
                                            @else
                                                <span class="text-xs text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <p class="text-muted mb-0">Không có user nào có vi phạm rate limit</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($users->hasPages())
                        <div class="px-4 py-3">
                            <x-pagination :paginator="$users" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show violation history modal
            document.querySelectorAll('.violation-history-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    const violationsData = JSON.parse(this.getAttribute('data-violations'));
                    
                    // Build modal content
                    let modalContent = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                    modalContent += '<thead><tr><th>Ngày</th><th>Số lần vi phạm</th></tr></thead><tbody>';
                    
                    Object.keys(violationsData).forEach(date => {
                        const count = violationsData[date];
                        const dateObj = new Date(date + 'T00:00:00');
                        const today = new Date();
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);
                        
                        const isToday = dateObj.toDateString() === today.toDateString();
                        const isYesterday = dateObj.toDateString() === yesterday.toDateString();
                        
                        let dateLabel = dateObj.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        if (isToday) dateLabel = 'Hôm nay';
                        else if (isYesterday) dateLabel = 'Hôm qua';
                        
                        const badgeClass = isToday ? 'bg-danger' : (isYesterday ? 'bg-warning text-dark' : 'bg-secondary');
                        modalContent += `<tr><td>${dateLabel}</td><td><span class="badge ${badgeClass}">${count}</span></td></tr>`;
                    });
                    
                    modalContent += '</tbody></table></div>';
                    
                    Swal.fire({
                        title: `Lịch sử vi phạm - ${userName}`,
                        html: modalContent,
                        width: '600px',
                        showCloseButton: true,
                        showConfirmButton: true,
                        confirmButtonText: 'Đóng',
                        confirmButtonColor: '#6c757d'
                    });
                });
            });
            
            // Unlock rate limit
            document.querySelectorAll('.unlock-rate-limit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    
                    Swal.fire({
                        title: 'Xác nhận mở khóa',
                        text: `Bạn có chắc chắn muốn mở khóa cho "${userName}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Mở khóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Không tìm thấy CSRF token'
                            });
                            return;
                        }

                        // Disable button
                        button.disabled = true;
                        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

                        fetch(`/admin/rate-limit/${userId}/unlock`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: data.message || 'Đã mở khóa tài khoản thành công',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: data.message || 'Có lỗi xảy ra'
                                });
                                button.disabled = false;
                                button.innerHTML = '<i class="fa-solid fa-unlock"></i> Mở khóa';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            let errorMessage = 'Có lỗi xảy ra khi mở khóa';
                            if (error.message) {
                                errorMessage = error.message;
                            } else if (typeof error === 'object' && error.message) {
                                errorMessage = error.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: errorMessage
                            });
                            button.disabled = false;
                            button.innerHTML = '<i class="fa-solid fa-unlock"></i> Mở khóa';
                        });
                    });
                });
            });
        });
    </script>
@endpush
