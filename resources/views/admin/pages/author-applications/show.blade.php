@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn đăng ký tác giả')

@push('styles-admin')
<style>
    .application-detail {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .application-info-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .application-info-item:last-child {
        border-bottom: none;
    }
    
    .label {
        font-weight: 600;
        color: #495057;
    }
    
    .value {
        color: #212529;
    }
    
    .user-info {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        border-left: 5px solid #6c757d;
        margin-bottom: 20px;
    }
    
    .link-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .link-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .introduction-text {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        border-left: 3px solid #17a2b8;
    }
    
    .actions-container {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content-auth')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Chi tiết đơn đăng ký tác giả #{{ $application->id }}</h4>
                    <a href="{{ route('admin.author-applications.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
                <div class="card-body">
                    <!-- Status information -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5>Trạng thái: 
                                @if ($application->status == 'pending')
                                    <span class="status-badge status-pending">Chờ duyệt</span>
                                @elseif ($application->status == 'approved')
                                    <span class="status-badge status-approved">Đã duyệt</span>
                                @elseif ($application->status == 'rejected')
                                    <span class="status-badge status-rejected">Từ chối</span>
                                @endif
                            </h5>
                            <p class="text-muted">
                                Ngày gửi: {{ $application->submitted_at->format('d/m/Y H:i') }}
                                @if ($application->published_at)
                                <br>Ngày xét duyệt: {{ $application->published_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5">
                            <!-- User information -->
                            <div class="user-info mb-4">
                                <h5 class="mb-3"><i class="fas fa-user me-2"></i> Thông tin người dùng</h5>
                                <div class="application-info-item">
                                    <div class="label">Tên người dùng:</div>
                                    <div class="value">{{ $application->user->name }}</div>
                                </div>
                                <div class="application-info-item">
                                    <div class="label">Email:</div>
                                    <div class="value">{{ $application->user->email }}</div>
                                </div>
                                <div class="application-info-item">
                                    <div class="label">Vai trò hiện tại:</div>
                                    <div class="value">{{ ucfirst($application->user->role) }}</div>
                                </div>
                                <div class="application-info-item">
                                    <div class="label">Ngày đăng ký:</div>
                                    <div class="value">{{ $application->user->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            
                            <!-- Contact links -->
                            <div class="application-detail">
                                <h5 class="mb-3"><i class="fas fa-link me-2"></i> Thông tin liên hệ</h5>
                                <div class="link-item">
                                    <i class="fab fa-facebook text-primary"></i>
                                    <a href="{{ $application->facebook_link }}" target="_blank">
                                        {{ $application->facebook_link }}
                                    </a>
                                </div>
                                
                                @if ($application->telegram_link)
                                <div class="link-item">
                                    <i class="fab fa-telegram text-info"></i>
                                    <a href="{{ $application->telegram_link }}" target="_blank">
                                        {{ $application->telegram_link }}
                                    </a>
                                </div>
                                @endif
                                
                                @if ($application->other_platform)
                                <div class="link-item">
                                    <i class="fas fa-globe text-success"></i>
                                    <div>
                                        <strong>{{ $application->other_platform }}</strong>
                                        @if ($application->other_platform_link)
                                        <br>
                                        <a href="{{ $application->other_platform_link }}" target="_blank">
                                            {{ $application->other_platform_link }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-7">
                            <!-- Introduction text -->
                            <div class="application-detail">
                                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> Giới thiệu</h5>
                                <div class="introduction-text">
                                    {!! nl2br(e($application->introduction)) !!}
                                </div>
                            </div>
                            
                            @if ($application->status != 'pending')
                                <!-- Admin note -->
                                <div class="application-detail">
                                    <h5 class="mb-3"><i class="fas fa-comment-alt me-2"></i> Phản hồi từ quản trị viên</h5>
                                    <div class="introduction-text">
                                        {!! nl2br(e($application->admin_note)) !!}
                                    </div>
                                </div>
                            @endif
                            
                            @if ($application->status == 'pending')
                                <!-- Actions for pending applications -->
                                <div class="actions-container">
                                    <h5 class="mb-3">Hành động</h5>
                                    
                                    <div class="row">
                                        <!-- Approve form -->
                                        <div class="col-md-6">
                                            <form action="{{ route('admin.author-applications.approve', $application) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="approve_note" class="form-label">Ghi chú khi duyệt (không bắt buộc)</label>
                                                    <textarea class="form-control" id="approve_note" name="admin_note" rows="3" placeholder="Ghi chú khi duyệt đơn..."></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="fas fa-check-circle me-2"></i> Duyệt đơn
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Reject form -->
                                        <div class="col-md-6">
                                            <form action="{{ route('admin.author-applications.reject', $application) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="reject_note" class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="reject_note" name="admin_note" rows="3" required placeholder="Lý do từ chối đơn..."></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fas fa-times-circle me-2"></i> Từ chối đơn
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 