@extends('admin.layouts.app')

@section('content-auth')
<div class="d-flex flex-column mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.bank-auto-deposits.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <div>
            <h2 class="fw-bold mb-0">Chi tiết giao dịch</h2>
            <p class="mb-0 text-muted">{{ $bankAutoDeposit->transaction_code }}</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Transaction Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Thông tin giao dịch</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Mã giao dịch</label>
                            <p class="text-sm mb-0">{{ $bankAutoDeposit->transaction_code }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Mã Casso</label>
                            <p class="text-sm mb-0">{{ $bankAutoDeposit->casso_transaction_id ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Số tiền</label>
                            <p class="text-sm mb-0 font-weight-bold text-success">{{ number_format($bankAutoDeposit->amount) }} VNĐ</p>
                        </div>
                        
                        @if($bankAutoDeposit->fee_amount > 0)
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Phí</label>
                            <p class="text-sm mb-0">{{ number_format($bankAutoDeposit->fee_amount) }} VNĐ</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Cám cộng</label>
                            <p class="text-sm mb-0">{{ number_format($bankAutoDeposit->base_coins) }} cám</p>
                        </div>
                        
                        @if($bankAutoDeposit->bonus_coins > 0)
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Cám tặng</label>
                            <p class="text-sm mb-0 text-success">+{{ number_format($bankAutoDeposit->bonus_coins) }} cám</p>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Tổng cám</label>
                            <p class="text-sm mb-0 font-weight-bold text-primary">{{ number_format($bankAutoDeposit->total_coins ?? $bankAutoDeposit->base_coins) }} cám</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Trạng thái</label>
                            <div>
                                @if($bankAutoDeposit->status === 'pending')
                                    <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                @elseif($bankAutoDeposit->status === 'success')
                                    <span class="badge badge-sm bg-gradient-success">Thành công</span>
                                @elseif($bankAutoDeposit->status === 'failed')
                                    <span class="badge badge-sm bg-gradient-danger">Thất bại</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Thời gian tạo</label>
                            <p class="text-sm mb-0">{{ $bankAutoDeposit->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        
                        @if($bankAutoDeposit->approved_at)
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Thời gian duyệt</label>
                            <p class="text-sm mb-0">{{ $bankAutoDeposit->approved_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                        
                        @if($bankAutoDeposit->rejected_at)
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Thời gian từ chối</label>
                            <p class="text-sm mb-0">{{ $bankAutoDeposit->rejected_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                        
                        @if($bankAutoDeposit->rejection_reason)
                        <div class="mb-3">
                            <label class="form-label text-sm font-weight-bold">Lý do từ chối</label>
                            <p class="text-sm mb-0">{{ $bankAutoDeposit->rejection_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User & Bank Info -->
    <div class="col-lg-4">
        <!-- User Info -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Thông tin người dùng</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-3">
                        <img src="{{ $bankAutoDeposit->user->avatar ? Storage::url($bankAutoDeposit->user->avatar) : asset('images/defaults/avatar.jpg') }}" alt="Avatar" class="avatar-sm rounded-circle">
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $bankAutoDeposit->user->username }}</h6>
                        <p class="text-sm text-secondary mb-0">{{ $bankAutoDeposit->user->email }}</p>
                    </div>
                </div>
                
                <div class="mb-2">
                    <span class="text-sm font-weight-bold">ID:</span>
                    <span class="text-sm">{{ $bankAutoDeposit->user->id }}</span>
                </div>
                
                <div class="mb-2">
                    <span class="text-sm font-weight-bold">Cám hiện tại:</span>
                    <span class="text-sm">{{ number_format($bankAutoDeposit->user->coins) }} cám</span>
                </div>
                
                <div class="mb-2">
                    <span class="text-sm font-weight-bold">Trạng thái:</span>
                    @if($bankAutoDeposit->user->active === 'active')
                        <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                    @else
                        <span class="badge badge-sm bg-gradient-danger">Không hoạt động</span>
                    @endif
                </div>
                
                <div class="mb-0">
                    <span class="text-sm font-weight-bold">Tham gia:</span>
                    <span class="text-sm">{{ $bankAutoDeposit->user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Bank Info -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Thông tin ngân hàng</h6>
            </div>
            <div class="card-body">
                @if($bankAutoDeposit->bankAuto)
                    <div class="d-flex align-items-center mb-3">
                        @if($bankAutoDeposit->bankAuto->logo)
                            <img src="{{ Storage::url($bankAutoDeposit->bankAuto->logo) }}" alt="Bank Logo" class="avatar-sm me-3">
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $bankAutoDeposit->bankAuto->name }}</h6>
                            <p class="text-sm text-secondary mb-0">{{ $bankAutoDeposit->bankAuto->account_number }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-sm font-weight-bold">Chủ tài khoản:</span>
                        <span class="text-sm">{{ $bankAutoDeposit->bankAuto->account_name }}</span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="text-sm font-weight-bold">Chi nhánh:</span>
                        <span class="text-sm">{{ $bankAutoDeposit->bankAuto->branch }}</span>
                    </div>
                    
                    <div class="mb-0">
                        <span class="text-sm font-weight-bold">Trạng thái:</span>
                        @if($bankAutoDeposit->bankAuto->status === 'active')
                            <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                        @else
                            <span class="badge badge-sm bg-gradient-danger">Không hoạt động</span>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-muted">Không có thông tin ngân hàng</p>
                @endif
            </div>
        </div>
        
        <!-- Actions -->
        @if($bankAutoDeposit->status === 'pending')
        <div class="card">
            <div class="card-header pb-0">
                <h6>Thông tin</h6>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Giao dịch này đang chờ xử lý tự động bởi hệ thống Casso.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
