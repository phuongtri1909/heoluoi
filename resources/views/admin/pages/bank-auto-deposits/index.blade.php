@extends('admin.layouts.app')

@push('styles-admin')
    <style>
        /* Responsive filters */
        @media (max-width: 768px) {
            .d-flex.flex-wrap.gap-2 {
                flex-direction: column;
                gap: 1rem !important;
            }

            .flex-grow-1 {
                min-width: 100% !important;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.375rem 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .card-header h5 {
                font-size: 1rem;
            }

            .card-header p {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
            }

            .badge {
                font-size: 0.65rem;
            }
        }
    </style>
@endpush

@section('content-auth')
    <div class="d-flex flex-column mb-4">
        <h2 class="fw-bold mb-0">Quản lý Giao dịch Bank Auto</h2>
        <p class="mb-0 text-muted">Quản lý các giao dịch nạp tiền tự động</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card stats-card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng giao dịch</p>
                                <h5 class="font-weight-bolder mb-0">{{ number_format($stats['total']) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape icon-md bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-coins text-white opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card stats-card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Chờ duyệt</p>
                                <h5 class="font-weight-bolder mb-0 text-warning">{{ number_format($stats['pending']) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape icon-md bg-gradient-warning shadow text-center border-radius-md">
                                <i class="fas fa-clock text-white opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card stats-card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Thành công</p>
                                <h5 class="font-weight-bolder mb-0 text-success">{{ number_format($stats['success']) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape icon-md bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-check text-white opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stats-card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng tiền</p>
                                <h5 class="font-weight-bolder mb-0">{{ number_format($stats['total_amount']) }} VNĐ</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape icon-md bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-chart-bar text-white opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <h5 class="mb-0">Bộ lọc</h5>
                    <p class="text-sm mb-0">Tìm kiếm và lọc giao dịch</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bank-auto-deposits.index') }}" id="filterForm">
                <div class="d-flex flex-wrap gap-2 align-items-end">
                    <!-- Status filter -->
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <label class="form-label text-sm">Trạng thái</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt
                            </option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công
                            </option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                        </select>
                    </div>

                    <!-- Date from filter -->
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <label class="form-label text-sm">Từ ngày</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}">
                    </div>

                    <!-- Date to filter -->
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <label class="form-label text-sm">Đến ngày</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}">
                    </div>

                    <!-- Search input -->
                    <div class="flex-grow-1" style="min-width: 200px;">
                        <label class="form-label text-sm">Tìm kiếm</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Mã giao dịch, username...">
                            <button class="btn bg-gradient-primary btn-sm px-3 mb-0" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Clear filters button -->
                    <div>
                        <a href="{{ route('admin.bank-auto-deposits.index') }}"
                            class="btn btn-outline-secondary btn-sm mb-0">
                            <i class="fas fa-times me-1"></i>Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Deposits Table -->
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex align-items-center">
                <h6>Danh sách giao dịch</h6>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã giao dịch
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Người dùng</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Số tiền</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cám</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                            <th class="text-secondary opacity-7">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $deposit->transaction_code }}</h6>
                                            @if ($deposit->casso_transaction_id)
                                                <p class="text-xs text-secondary mb-0">Casso:
                                                    {{ $deposit->casso_transaction_id }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $deposit->user->username }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $deposit->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ number_format($deposit->amount) }} VNĐ</h6>
                                        @if ($deposit->fee_amount > 0)
                                            <p class="text-xs text-secondary mb-0">Phí:
                                                {{ number_format($deposit->fee_amount) }} VNĐ</p>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">
                                            {{ number_format($deposit->total_coins ?? $deposit->base_coins) }} cám</h6>
                                        @if ($deposit->bonus_coins > 0)
                                            <p class="text-xs text-success mb-0">
                                                +{{ number_format($deposit->bonus_coins) }} bonus</p>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($deposit->status === 'pending')
                                        <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                    @elseif($deposit->status === 'success')
                                        <span class="badge badge-sm bg-gradient-success">Thành công</span>
                                    @elseif($deposit->status === 'failed')
                                        <span class="badge badge-sm bg-gradient-danger">Thất bại</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $deposit->created_at->format('d/m/Y') }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $deposit->created_at->format('H:i:s') }}
                                        </p>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.bank-auto-deposits.show', $deposit) }}"
                                        class="btn btn-link text-dark px-3 mb-0">
                                        <i class="fas fa-eye text-dark me-2" aria-hidden="true"></i>Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted">Không có giao dịch nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $deposits->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit filters when changed
            const filterSelects = document.querySelectorAll('#filterForm select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });
        });
    </script>
@endpush
