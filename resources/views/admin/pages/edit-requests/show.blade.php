@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu chỉnh sửa')

@push('styles-admin')
<style>
    .story-detail {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .story-info-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .story-info-item:last-child {
        border-bottom: none;
    }
    
    .label {
        font-weight: 600;
        color: #495057;
    }
    
    .value {
        color: #212529;
    }
    
    .author-info {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        border-left: 5px solid #6c757d;
        margin-bottom: 20px;
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
    
    .description-text {
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
    
    .cover-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
    
    .comparison-container {
        display: flex;
        margin-bottom: 20px;
    }
    
    .comparison-column {
        flex: 1;
        padding: 10px;
    }
    
    .comparison-column:first-child {
        border-right: 1px solid #dee2e6;
    }
    
    .comparison-header {
        font-weight: 600;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 10px;
        text-align: center;
    }
    
    .changed {
        background-color: #fff8e1;
    }
    
    .added {
        background-color: #e8f5e9;
    }
    
    .removed {
        background-color: #ffebee;
        text-decoration: line-through;
    }
</style>
@endpush

@section('content-auth')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Chi tiết yêu cầu chỉnh sửa #{{ $editRequest->id }}</h4>
                    <a href="{{ route('admin.edit-requests.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
                <div class="card-body">
                    <!-- Status information -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5>Trạng thái: 
                                @if ($editRequest->status == 'pending')
                                    <span class="status-badge status-pending">Chờ duyệt</span>
                                @elseif ($editRequest->status == 'approved')
                                    <span class="status-badge status-approved">Đã duyệt</span>
                                @elseif ($editRequest->status == 'rejected')
                                    <span class="status-badge status-rejected">Từ chối</span>
                                @endif
                            </h5>
                            <p class="text-muted">
                                Ngày gửi: {{ $editRequest->submitted_at->format('d/m/Y H:i') }}
                                @if ($editRequest->published_at)
                                <br>Ngày xét duyệt: {{ $editRequest->published_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Author information -->
                    <div class="author-info mb-4">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i> Thông tin người yêu cầu</h5>
                        <div class="story-info-item">
                            <div class="label">Tên người dùng:</div>
                            <div class="value">{{ $editRequest->user->name }}</div>
                        </div>
                        <div class="story-info-item">
                            <div class="label">Email:</div>
                            <div class="value">{{ $editRequest->user->email }}</div>
                        </div>
                        <div class="story-info-item">
                            <div class="label">Vai trò:</div>
                            <div class="value">{{ ucfirst($editRequest->user->role) }}</div>
                        </div>
                    </div>
                    
                    <!-- Comparison sections -->
                    <h5 class="mb-3">So sánh thay đổi</h5>
                    
                    <!-- Title comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Tiêu đề</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->title != $editRequest->title ? 'changed' : '' }}">
                                    {{ $story->title }}
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->title != $editRequest->title ? 'changed' : '' }}">
                                    {{ $editRequest->title }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Mô tả</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->description != $editRequest->description ? 'changed' : '' }}">
                                    {!! $story->description !!}
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->description != $editRequest->description ? 'changed' : '' }}">
                                    {!! $editRequest->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Author name comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Tên tác giả</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->author_name != $editRequest->author_name ? 'changed' : '' }}">
                                    {{ $story->author_name }}
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->author_name != $editRequest->author_name ? 'changed' : '' }}">
                                    {{ $editRequest->author_name }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Translator name comparison (if applicable) -->
                    @if ($story->translator_name || $editRequest->translator_name)
                    <div class="story-detail">
                        <h6 class="mb-3">Người dịch</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->translator_name != $editRequest->translator_name ? 'changed' : '' }}">
                                    {{ $story->translator_name ?: 'Không có' }}
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->translator_name != $editRequest->translator_name ? 'changed' : '' }}">
                                    {{ $editRequest->translator_name ?: 'Không có' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Story type comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Loại truyện</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->story_type != $editRequest->story_type ? 'changed' : '' }}">
                                    @if ($story->story_type == 'original')
                                        Sáng tác
                                    @elseif ($story->story_type == 'translated')
                                        Dịch
                                    @elseif ($story->story_type == 'collected')
                                        Sưu tầm
                                    @else
                                        {{ $story->story_type }}
                                    @endif
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->story_type != $editRequest->story_type ? 'changed' : '' }}">
                                    @if ($editRequest->story_type == 'original')
                                        Sáng tác
                                    @elseif ($editRequest->story_type == 'translated')
                                        Dịch
                                    @elseif ($editRequest->story_type == 'collected')
                                        Sưu tầm
                                    @else
                                        {{ $editRequest->story_type }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Categories comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Thể loại</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value">
                                    @foreach($story->categories as $category)
                                        <span class="badge bg-info me-1 mb-1">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value">
                                    @php
                                        $categoryData = json_decode($editRequest->categories_data, true) ?? [];
                                    @endphp
                                    @foreach($categoryData as $category)
                                        <span class="badge bg-info me-1 mb-1">{{ $category['name'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cover image comparison -->
                    @if ($editRequest->cover)
                    <div class="story-detail">
                        <h6 class="mb-3">Ảnh bìa</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <img src="{{ Storage::url($story->cover) }}" alt="{{ $story->title }}" class="cover-image">
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <img src="{{ Storage::url($editRequest->cover) }}" alt="{{ $editRequest->title }}" class="cover-image">
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Monopoly status comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Độc quyền</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->is_monopoly != $editRequest->is_monopoly ? 'changed' : '' }}">
                                    {{ $story->is_monopoly ? 'Có' : 'Không' }}
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->is_monopoly != $editRequest->is_monopoly ? 'changed' : '' }}">
                                    {{ $editRequest->is_monopoly ? 'Có' : 'Không' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 18+ status comparison -->
                    <div class="story-detail">
                        <h6 class="mb-3">Giới hạn độ tuổi</h6>
                        <div class="comparison-container">
                            <div class="comparison-column">
                                <div class="comparison-header">Hiện tại</div>
                                <div class="value {{ $story->is_18_plus != $editRequest->is_18_plus ? 'changed' : '' }}">
                                    {{ $story->is_18_plus ? '18+' : 'Không' }}
                                </div>
                            </div>
                            <div class="comparison-column">
                                <div class="comparison-header">Yêu cầu thay đổi</div>
                                <div class="value {{ $story->is_18_plus != $editRequest->is_18_plus ? 'changed' : '' }}">
                                    {{ $editRequest->is_18_plus ? '18+' : 'Không' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if ($editRequest->status == 'pending')
                        <!-- Actions for pending edit requests -->
                        <div class="actions-container">
                            <h5 class="mb-3">Hành động</h5>
                            
                            <div class="row">
                                <!-- Approve form -->
                                <div class="col-md-6">
                                    <form action="{{ route('admin.edit-requests.approve', $editRequest) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="approve_note" class="form-label">Ghi chú khi duyệt (không bắt buộc)</label>
                                            <textarea class="form-control" id="approve_note" name="admin_note" rows="3" placeholder="Ghi chú khi duyệt yêu cầu..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check-circle me-2"></i> Duyệt yêu cầu
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Reject form -->
                                <div class="col-md-6">
                                    <form action="{{ route('admin.edit-requests.reject', $editRequest) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="reject_note" class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="reject_note" name="admin_note" rows="3" required placeholder="Lý do từ chối yêu cầu..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-times-circle me-2"></i> Từ chối yêu cầu
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif ($editRequest->admin_note)
                        <!-- Admin note for approved/rejected edit requests -->
                        <div class="story-detail">
                            <h5 class="mb-3"><i class="fas fa-comment-alt me-2"></i> Phản hồi từ quản trị viên</h5>
                            <div class="description-text">
                                {!! nl2br(e($editRequest->admin_note)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 