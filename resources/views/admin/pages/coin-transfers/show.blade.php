@extends('admin.layouts.app')

@section('content-auth')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Chi tiết chuyển khoản</h6>
                            <a href="{{ route('admin.coin-transfers.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-sm mb-3 text-primary">Thông tin chuyển khoản</h6>

                                <div class="info-item mb-3">
                                    <strong>ID:</strong> #{{ $transfer->id }}
                                </div>

                                <div class="info-item mb-3">
                                    <strong>Số Cám:</strong>
                                    <span class="text-success font-weight-bold">{{ number_format($transfer->amount) }}
                                        Cám</span>
                                </div>

                                <div class="info-item mb-3">
                                    <strong>Trạng thái:</strong>
                                    @if ($transfer->status === 'pending')
                                        <span class="badge bg-warning">Đang chờ</span>
                                    @elseif($transfer->status === 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($transfer->status === 'rejected')
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </div>

                                <div class="info-item mb-3">
                                    <strong>Thời gian:</strong>
                                    <div class="text-sm">
                                        {{ $transfer->created_at->format('d/m/Y H:i:s') }}<br>
                                        <small>{{ $transfer->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>

                                @if ($transfer->note)
                                    <div class="info-item mb-3">
                                        <strong>Ghi chú:</strong>
                                        <p class="text-sm">{{ $transfer->note }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                @if (Auth::user()->role === 'admin_main')
                                    <h6 class="text-sm mb-3 text-primary">Admin_sub thực hiện</h6>
                                    <div class="d-flex align-items-center mb-4">
                                        <img src="{{ $transfer->fromAdmin->avatar ? Storage::url($transfer->fromAdmin->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                            class="rounded-circle me-3"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1">{{ $transfer->fromAdmin->name }}</h6>
                                            <p class="text-muted mb-0">{{ $transfer->fromAdmin->email }}</p>
                                        </div>
                                    </div>
                                @endif

                                <h6 class="text-sm mb-3 text-primary">Người nhận</h6>
                                <div class="d-flex align-items-center mb-4">
                                    <img src="{{ $transfer->toUser->avatar ? Storage::url($transfer->toUser->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                        class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1">{{ $transfer->toUser->name }}</h6>
                                        <p class="text-muted mb-0">{{ $transfer->toUser->email }}</p>
                                        <small class="text-success">Số Cám hiện tại:
                                            {{ number_format($transfer->toUser->coins) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center">
                            <a href="{{ route('admin.coin-transfers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list me-1"></i> Danh sách chuyển khoản
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles-admin')
    <style>
        .info-item {
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush
