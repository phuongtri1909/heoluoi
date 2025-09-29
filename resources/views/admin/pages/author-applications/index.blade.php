@extends('admin.layouts.app')

@section('title', 'Quản lý đơn đăng ký tác giả')

@push('styles-admin')
<style>
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
    .tab-count {
        background-color: rgba(0,0,0,0.1);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        margin-left: 5px;
    }
    .nav-link.active .tab-count {
        background-color: rgba(255,255,255,0.2);
    }
</style>
@endpush

@section('content-auth')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Đơn đăng ký tác giả</h4>
                </div>
                <div class="card-body">
                    <!-- Filter tabs -->
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('status') || request('status') == 'pending' ? 'active' : '' }}" 
                               href="{{ route('admin.author-applications.index', ['status' => 'pending']) }}">
                                Chờ duyệt <span class="tab-count">{{ $pendingCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" 
                               href="{{ route('admin.author-applications.index', ['status' => 'approved']) }}">
                                Đã duyệt <span class="tab-count">{{ $approvedCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" 
                               href="{{ route('admin.author-applications.index', ['status' => 'rejected']) }}">
                                Từ chối <span class="tab-count">{{ $rejectedCount }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Search form -->
                    <form action="{{ route('admin.author-applications.index') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Tìm kiếm theo tên hoặc email người dùng" 
                                   value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                            <button class="btn btn-outline-secondary mb-0" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                    
                    <!-- Applications table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người dùng</th>
                                    <th>Đường dẫn liên hệ</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applications as $application)
                                <tr>
                                    <td>{{ $application->id }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $application->user->name }}</strong>
                                            <small>{{ $application->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ $application->facebook_link }}" target="_blank" class="d-block mb-1">
                                            <i class="fab fa-facebook text-primary"></i> Facebook
                                        </a>
                                        @if ($application->telegram_link)
                                        <a href="{{ $application->telegram_link }}" target="_blank" class="d-block mb-1">
                                            <i class="fab fa-telegram text-info"></i> Telegram
                                        </a>
                                        @endif
                                    </td>
                                    <td>{{ $application->submitted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($application->status == 'pending')
                                            <span class="status-badge status-pending">Chờ duyệt</span>
                                        @elseif ($application->status == 'approved')
                                            <span class="status-badge status-approved">Đã duyệt</span>
                                        @elseif ($application->status == 'rejected')
                                            <span class="status-badge status-rejected">Từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.author-applications.show', $application) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có đơn đăng ký nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <x-pagination :paginator="$applications" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 