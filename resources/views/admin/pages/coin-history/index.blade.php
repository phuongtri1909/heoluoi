@extends('admin.layouts.app')

@section('title', 'Quản lý dòng tiền')

@section('content-auth')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-coins me-2"></i>Quản lý dòng tiền
                    </h3>
                </div>

                <!-- Statistics Cards -->
                <div class="card-body">
                     <div class="row mb-4">
                         <div class="col-md-3 mb-3">
                             <div class="card border-0 shadow-sm h-100">
                                 <div class="card-body d-flex align-items-center">
                                     <div class="flex-shrink-0">
                                         <div class="bg-success bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                             <i class="fas fa-plus text-white fs-4"></i>
                                         </div>
                                     </div>
                                     <div class="flex-grow-1 ms-3">
                                         <h6 class="card-title text-muted mb-1">Tổng cộng</h6>
                                         <h4 class="mb-0 text-success fw-bold">{{ number_format($stats['total_added']) }} xu</h4>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-3 mb-3">
                             <div class="card border-0 shadow-sm h-100">
                                 <div class="card-body d-flex align-items-center">
                                     <div class="flex-shrink-0">
                                         <div class="bg-danger bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                             <i class="fas fa-minus text-white fs-4"></i>
                                         </div>
                                     </div>
                                     <div class="flex-grow-1 ms-3">
                                         <h6 class="card-title text-muted mb-1">Tổng trừ</h6>
                                         <h4 class="mb-0 text-danger fw-bold">{{ number_format($stats['total_subtracted']) }} xu</h4>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-3 mb-3">
                             <div class="card border-0 shadow-sm h-100">
                                 <div class="card-body d-flex align-items-center">
                                     <div class="flex-shrink-0">
                                         <div class="bg-info bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                             <i class="fas fa-exchange-alt text-white fs-4"></i>
                                         </div>
                                     </div>
                                     <div class="flex-grow-1 ms-3">
                                         <h6 class="card-title text-muted mb-1">Tổng giao dịch</h6>
                                         <h4 class="mb-0 text-info fw-bold">{{ number_format($stats['total_transactions']) }}</h4>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-3 mb-3">
                             <div class="card border-0 shadow-sm h-100">
                                 <div class="card-body d-flex align-items-center">
                                     <div class="flex-shrink-0">
                                         <div class="bg-warning bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                             <i class="fas fa-calculator text-white fs-4"></i>
                                         </div>
                                     </div>
                                     <div class="flex-grow-1 ms-3">
                                         <h6 class="card-title text-muted mb-1">Số dư thực tế</h6>
                                         <h4 class="mb-0 text-warning fw-bold">{{ number_format($stats['total_added'] - $stats['total_subtracted']) }} xu</h4>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" class="row g-3">
                                <div class="col-md-2">
                                    <select name="user_id" class="form-select">
                                        <option value="">Tất cả user</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="type" class="form-select">
                                        <option value="">Tất cả loại</option>
                                        <option value="add" {{ request('type') == 'add' ? 'selected' : '' }}>Cộng xu</option>
                                        <option value="subtract" {{ request('type') == 'subtract' ? 'selected' : '' }}>Trừ xu</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="transaction_type" class="form-select">
                                        <option value="">Tất cả giao dịch</option>
                                        @foreach($transactionTypes as $key => $label)
                                            <option value="{{ $key }}" {{ request('transaction_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ request('date_from') }}" placeholder="Từ ngày">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ request('date_to') }}" placeholder="Đến ngày">
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>User</th>
                                    <th>Loại giao dịch</th>
                                    <th>Mô tả</th>
                                    <th>Số xu</th>
                                    <th>Số dư trước</th>
                                    <th>Số dư sau</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $transaction->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $transaction->user->name }}</span>
                                                <small class="text-muted">{{ $transaction->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type == 'add' ? 'success' : 'danger' }}">
                                                {{ $transaction->transaction_type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $transaction->description }}</span>
                                                @if($transaction->reference)
                                                    <small class="text-muted">
                                                        Tham chiếu: {{ class_basename($transaction->reference_type) }} #{{ $transaction->reference_id }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-{{ $transaction->type == 'add' ? 'success' : 'danger' }}">
                                                {{ $transaction->formatted_amount }} xu
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($transaction->balance_before) }} xu</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($transaction->balance_after) }} xu</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $transaction->ip_address }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Chưa có giao dịch nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($transactions->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links('components.pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
