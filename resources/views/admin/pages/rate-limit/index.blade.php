@extends('admin.layouts.app')

@section('content-auth')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Quản lý Rate Limit - Vi phạm</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        {{-- Search & Filter --}}
                        <div class="px-4 pt-3">
                            <form method="GET" action="{{ route('admin.rate-limit.index') }}" class="mb-3">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline">
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="search" 
                                                   placeholder="Tìm kiếm theo tên hoặc email..." 
                                                   value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="ban_type" class="form-select">
                                            <option value="">Tất cả</option>
                                            <option value="permanent" {{ request('ban_type') == 'permanent' ? 'selected' : '' }}>Khóa vĩnh viễn</option>
                                            <option value="temporary" {{ request('ban_type') == 'temporary' ? 'selected' : '' }}>Khóa tạm thời</option>
                                            <option value="no_ban" {{ request('ban_type') == 'no_ban' ? 'selected' : '' }}>Chưa bị khóa</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-search"></i> Tìm kiếm
                                        </button>
                                        @if(request('search') || request('ban_type'))
                                            <a href="{{ route('admin.rate-limit.index') }}" class="btn btn-secondary">
                                                <i class="fa-solid fa-times"></i> Xóa
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vi phạm hôm nay</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tổng vi phạm</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lần vi phạm gần nhất</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loại khóa</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian hết hạn</th>
                                        <th class="text-secondary opacity-7"></th>
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
                                                @if($user->rateLimitViolations->count() > 0)
                                                    <span class="text-xs text-secondary">
                                                        {{ $user->rateLimitViolations->first()->violated_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-secondary">N/A</span>
                                                @endif
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
                                            <td class="align-middle">
                                                @if($user->ban_type === 'permanent' || $user->ban_type === 'temporary')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success unlock-btn" 
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
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted">Không có user nào có vi phạm rate limit</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($users->hasPages())
                            <div class="px-4 pb-3">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.unlock-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');

                    Swal.fire({
                        title: 'Xác nhận mở khóa',
                        text: `Bạn có chắc chắn muốn mở khóa cho tài khoản "${userName}"?`,
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
                                    // Reload page to refresh the list
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

