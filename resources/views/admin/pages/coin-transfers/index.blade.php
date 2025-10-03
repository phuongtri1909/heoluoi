@extends('admin.layouts.app')

@section('content-auth')
    <div class="container-fluid py-4">
        @if (Auth::user()->role === 'admin_main')
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng chuyển khoản</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ number_format($stats['total_transfers']) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="fas fa-exchange-alt text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng cám đã chuyển</p>
                                        <h5 class="font-weight-bolder mb-0 text-success">
                                            {{ number_format($stats['total_amount_transferred']) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                        <i class="fas fa-coins text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Đang chờ</p>
                                        <h5 class="font-weight-bolder mb-0 text-warning">
                                            {{ number_format($stats['pending_transfers']) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                        <i class="fas fa-clock text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Hôm nay</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ number_format($stats['today_transfers']) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="fas fa-calendar-day text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Transfers Table -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>
                                @if (Auth::user()->role === 'admin_main')
                                    Quản lý chuyển khoản cám từ admin_sub
                                @else
                                    Lịch sử chuyển cám của tôi
                                @endif
                            </h6>
                            @if (Auth::user()->role === 'admin_sub')
                                <a href="{{ route('admin.coin-transfers.create') }}" class="btn bg-gradient-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Chuyển cám
                                </a>
                            @endif
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.coin-transfers.index') }}" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" name="search"
                                        placeholder="Tìm kiếm người dùng..." value="{{ request('search') }}">
                                </div>
                                @if (Auth::user()->role === 'admin_main')
                                    <div class="col-md-2">
                                        <select name="admin_id" class="form-select form-select-sm">
                                            <option value="">Tất cả admin_sub</option>
                                            @foreach ($admins as $admin)
                                                <option value="{{ $admin->id }}"
                                                    {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                @if (Auth::user()->role === 'admin_main')
                                    <div class="col-md-2">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                                Đang chờ</option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành
                                            </option>
                                            <option value="rejected"
                                                {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                        </select>
                                    </div>
                                @else
                                    <div class="col-md-2">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành
                                            </option>
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-{{ Auth::user()->role === 'admin_main' ? '5' : '7' }}">
                                    <button type="submit" class="btn bg-gradient-primary btn-sm me-2">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.coin-transfers.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-refresh"></i> Làm mới
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                        @if (Auth::user()->role === 'admin_main')
                                            <th class="text-uppercase text-xxs font-weight-bolder ps-2">Admin_sub</th>
                                        @endif
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Người nhận</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Số cám</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Trạng thái</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Ghi chú</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Thời gian</th>
                                        <th class="text-uppercase text-xxs font-weight-bolder ps-2">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transfers as $transfer)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">#{{ $transfer->id }}</p>
                                            </td>
                                            @if (Auth::user()->role === 'admin_main')
                                                <td>
                                                    <a href="{{ route('admin.users.show', $transfer->fromAdmin->id) }}" class="d-flex align-items-center">
                                                        <img src="{{ $transfer->fromAdmin->avatar ? Storage::url($transfer->fromAdmin->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                                            class="avatar avatar-xs me-2">
                                                        <p class="text-sm font-weight-bold mb-0">
                                                            {{ $transfer->fromAdmin->name }}</p>
                                                    </a>
                                                </td>
                                            @endif
                                            <td>
                                                <a href="{{ route('admin.users.show', $transfer->toUser->id) }}" class="d-flex px-2 py-1">
                                                    <div>
                                                        <img src="{{ $transfer->toUser->avatar ? Storage::url($transfer->toUser->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                                            class="avatar avatar-sm me-3">
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $transfer->toUser->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $transfer->toUser->email }}</p>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0 text-success">
                                                    {{ number_format($transfer->amount) }}
                                                </p>
                                            </td>
                                            <td>
                                                @if ($transfer->status === 'pending')
                                                    <span class="badge bg-warning">Đang chờ</span>
                                                @elseif($transfer->status === 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($transfer->status === 'rejected')
                                                    <span class="badge bg-danger">Từ chối</span>
                                                @endif
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">
                                                    {{ $transfer->note ?: 'Không có ghi chú' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">
                                                    {{ $transfer->created_at->format('d/m/Y H:i') }}</p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $transfer->created_at->diffForHumans() }}</p>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.coin-transfers.show', $transfer->id) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Chi tiết">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Auth::user()->role === 'admin_main' ? '7' : '6' }}"
                                                class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-inbox text-muted mb-2" style="font-size: 3rem;"></i>
                                                    <p class="text-muted mb-0">Chưa có chuyển khoản nào</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <x-pagination :paginator="$transfers" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles-admin')
    <style>
        .avatar-xs {
            width: 24px;
            height: 24px;
        }
    </style>
@endpush
