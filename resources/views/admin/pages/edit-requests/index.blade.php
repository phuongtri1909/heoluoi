@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu chỉnh sửa truyện')

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
                    <h4 class="mb-0">Quản lý yêu cầu chỉnh sửa truyện</h4>
                </div>
                <div class="card-body">
                    <!-- Filter tabs -->
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('status') || request('status') == 'pending' ? 'active' : '' }}" 
                               href="{{ route('admin.edit-requests.index', ['status' => 'pending']) }}">
                                Chờ duyệt <span class="tab-count">{{ $pendingCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" 
                               href="{{ route('admin.edit-requests.index', ['status' => 'approved']) }}">
                                Đã duyệt <span class="tab-count">{{ $approvedCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" 
                               href="{{ route('admin.edit-requests.index', ['status' => 'rejected']) }}">
                                Từ chối <span class="tab-count">{{ $rejectedCount }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Search form -->
                    <form action="{{ route('admin.edit-requests.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Tìm kiếm theo tiêu đề hoặc tên tác giả" 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-control" name="author_id">
                                    <option value="">-- Chọn tác giả --</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }} ({{ ucfirst($author->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="date" class="form-control" name="submitted_date" 
                                       placeholder="Ngày gửi" 
                                       value="{{ request('submitted_date') }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                                <button class="btn btn-outline-secondary w-100 mb-0" type="submit">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Edit requests table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ảnh bìa</th>
                                    <th>Tiêu đề truyện</th>
                                    <th>Người yêu cầu</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($editRequests as $editRequest)
                                <tr>
                                    <td>{{ $editRequest->id }}</td>
                                    <td>
                                        @if ($editRequest->cover_thumbnail)
                                            <img src="{{ Storage::url($editRequest->cover_thumbnail) }}" 
                                                 alt="{{ $editRequest->title }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 60px;">
                                        @else
                                            <img src="{{ asset('assets/images/story_default.png') }}" 
                                                 alt="default cover" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 60px;">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $editRequest->title }}</strong>
                                            <small>{{ Str::limit($editRequest->title, 30) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $editRequest->user->name }}</span>
                                            <small class="text-muted">{{ $editRequest->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $editRequest->submitted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($editRequest->status == 'pending')
                                            <span class="status-badge status-pending">Chờ duyệt</span>
                                        @elseif ($editRequest->status == 'approved')
                                            <span class="status-badge status-approved">Đã duyệt</span>
                                        @elseif ($editRequest->status == 'rejected')
                                            <span class="status-badge status-rejected">Từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.edit-requests.show', $editRequest) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có yêu cầu chỉnh sửa nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <x-pagination :paginator="$editRequests" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 