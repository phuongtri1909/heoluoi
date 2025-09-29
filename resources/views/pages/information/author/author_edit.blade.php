@extends('layouts.information')

@section('info_title', 'Chỉnh sửa truyện')
@section('info_description', 'Chỉnh sửa thông tin truyện ' . $story->title)
@section('info_keyword', 'sửa truyện, đăng truyện, tác giả, ' . request()->getHost())
@section('info_section_title', 'Chỉnh sửa truyện')
@section('info_section_desc', 'Cập nhật thông tin cho truyện của bạn')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/category-tags.css') }}" rel="stylesheet" />
    <style>
        /* Cover upload styles... (same as before) */
        .cover-upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .cover-upload-area:hover {
            border-color: var(--primary-color-3);
            background-color: rgba(var(--primary-rgb), 0.05);
        }

        .cover-upload-area.dragover {
            border-color: var(--primary-color-3);
            background-color: rgba(var(--primary-rgb), 0.1);
        }

        .cover-preview-container {
            position: relative;
            display: inline-block;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .cover-preview-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .preview-cover {
            display: block;
            max-width: 200px;
            height: auto;
            border: none;
            border-radius: 10px;
        }

        .cover-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 10px;
        }

        .cover-preview-container:hover .cover-overlay {
            opacity: 1;
        }

        .cover-actions {
            display: flex;
            gap: 10px;
        }

        .cover-action-btn {
            background: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cover-action-btn:hover {
            transform: scale(1.1);
        }

        .cover-action-btn.change {
            color: var(--primary-color-3);
        }

        .cover-action-btn.remove {
            color: #dc3545;
        }

        .upload-icon {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }

        .upload-text {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .upload-hint {
            color: #adb5bd;
            font-size: 0.9rem;
        }

        #cover {
            display: none;
        }

        /* Existing styles... */
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1rem;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 3px solid var(--primary-color-3);
            color: var(--primary-color-3);
            background: transparent;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            border-bottom: 3px solid #e9ecef;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge-draft {
            background-color: #e9ecef;
            color: #495057;
        }

        .status-badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge-published {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-badge-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .story-info-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .story-info-header {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .story-info-body {
            padding: 1.5rem;
        }

        .chapter-count-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chapter-count-badge.success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .chapter-count-badge.warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .review-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

    </style>
@endpush

@section('info_content')
    <div class="mb-4">
        <a href="{{ route('user.author.stories') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="story-info-card">
        <div class="story-info-header">
            <h4 class="mb-0">{{ $story->title }}</h4>
            @if ($story->status == 'draft')
                <span class="status-badge status-badge-draft">
                    <i class="fas fa-edit"></i> Bản nháp
                </span>
            @elseif($story->status == 'pending')
                <span class="status-badge status-badge-pending">
                    <i class="fas fa-clock"></i> Đang chờ duyệt
                </span>
            @elseif($story->status == 'published')
                <span class="status-badge status-badge-published">
                    <i class="fas fa-check-circle"></i> Đã xuất bản
                </span>
            @elseif($story->status == 'rejected')
                <span class="status-badge status-badge-rejected">
                    <i class="fas fa-times-circle"></i> Đã bị từ chối
                </span>
            @endif
        </div>
        <div class="story-info-body">
            <ul class="nav nav-tabs mb-4" id="storyTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit"
                        type="button" role="tab" aria-controls="edit" aria-selected="true">
                        <i class="fas fa-edit me-1"></i> Thông tin truyện
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="chapters-tab" data-bs-toggle="tab" data-bs-target="#chapters"
                        type="button" role="tab" aria-controls="chapters" aria-selected="false">
                        <i class="fas fa-list-ol me-1"></i> Quản lý chương
                        <span class="badge bg-secondary ms-1">{{ $story->chapters->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="featured-tab" data-bs-toggle="tab" data-bs-target="#featured"
                        type="button" role="tab" aria-controls="featured" aria-selected="false">
                        <i class="fas fa-star me-1"></i> Đề cử truyện
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button"
                        role="tab" aria-controls="review" aria-selected="false">
                        <i class="fas fa-check-circle me-1"></i> Yêu cầu duyệt
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="storyTabContent">
                <!-- Tab Thông tin truyện -->
                <div class="tab-pane fade show active" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                    <form action="{{ route('user.author.stories.update', $story->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ảnh bìa
                                        {{ $story->cover ? '' : '<span class="text-danger">*</span>' }}</label>

                                    <input type="file" id="cover" name="cover" accept="image/*"
                                        {{ $story->cover ? '' : 'required' }}>
                                    @error('cover')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    @if ($story->cover)
                                        <!-- Preview Area for existing cover -->
                                        <div class="cover-preview mt-3" id="previewArea">
                                            <div class="cover-preview-container">
                                                <img src="{{ Storage::url($story->cover) }}" class="preview-cover"
                                                    id="coverPreview" alt="Cover">
                                                <div class="cover-overlay">
                                                    <div class="cover-actions">
                                                        <button type="button" class="cover-action-btn change"
                                                            id="changeCover" title="Thay đổi ảnh">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="cover-action-btn remove"
                                                            id="removeCover" title="Xóa ảnh">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload Area (hidden initially) -->
                                        <div class="cover-upload-area d-none" id="uploadArea">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <div class="upload-text">Chọn ảnh bìa mới</div>
                                            <div class="upload-hint">Kéo thả ảnh vào đây hoặc click để chọn</div>
                                            <div class="upload-hint mt-2">
                                                <small>Tỷ lệ ảnh tốt nhất: 2:3 | Kích thước tối thiểu: 300x450px</small>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Upload Area for new cover -->
                                        <div class="cover-upload-area" id="uploadArea">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <div class="upload-text">Chọn ảnh bìa truyện</div>
                                            <div class="upload-hint">Kéo thả ảnh vào đây hoặc click để chọn</div>
                                            <div class="upload-hint mt-2">
                                                <small>Tỷ lệ ảnh tốt nhất: 2:3 | Kích thước tối thiểu: 300x450px</small>
                                            </div>
                                        </div>

                                        <!-- Preview Area (hidden initially) -->
                                        <div class="cover-preview d-none mt-3" id="previewArea">
                                            <div class="cover-preview-container">
                                                <img src="" class="preview-cover" id="coverPreview"
                                                    alt="Preview">
                                                <div class="cover-overlay">
                                                    <div class="cover-actions">
                                                        <button type="button" class="cover-action-btn change"
                                                            id="changeCover" title="Thay đổi ảnh">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="cover-action-btn remove"
                                                            id="removeCover" title="Xóa ảnh">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Tiêu đề truyện <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $story->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_input" class="form-label">Thể loại <span
                                            class="text-danger">*</span></label>

                                    <div class="category-input-container @error('category_input') is-invalid @enderror"
                                        id="categoryContainer">
                                        <div class="category-tags" id="categoryTags"></div>
                                        <input type="text" class="category-input" id="categoryInput"
                                            autocomplete="off">
                                        <div class="category-suggestions d-none" id="categorySuggestions"></div>
                                    </div>

                                    <!-- Hidden input to store selected categories -->
                                    <input type="hidden" name="category_input" id="categoryInputHidden"
                                        value="{{ old('category_input', $categoryNames) }}">

                                    <div class="form-text text-muted">
                                        Nhập thể loại hoặc dán nhiều thể loại cách nhau bằng dấu phẩy...
                                    </div>

                                    @error('category_input')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="6">{{ old('description', $story->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">



                                <div class="mb-3">
                                    <label for="author_name" class="form-label">Tên tác giả <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('author_name') is-invalid @enderror"
                                        id="author_name" name="author_name"
                                        value="{{ old('author_name', $story->author_name) }}" required>
                                    @error('author_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="story_type" class="form-label">Loại truyện <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('story_type') is-invalid @enderror" id="story_type"
                                        name="story_type" required>
                                        <option value="">-- Chọn loại truyện --</option>
                                        <option value="original"
                                            {{ old('story_type', $story->story_type) == 'original' ? 'selected' : '' }}>
                                            Sáng tác</option>
                                        <option value="translated"
                                            {{ old('story_type', $story->story_type) == 'translated' ? 'selected' : '' }}>
                                            Dịch/Edit</option>
                                        <option value="collected"
                                            {{ old('story_type', $story->story_type) == 'collected' ? 'selected' : '' }}>
                                            Sưu tầm</option>
                                    </select>
                                    @error('story_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="translator_name" class="form-label">Chuyển ngữ <span
                                            class="text-muted">(nếu có)</span></label>
                                    <input type="text"
                                        class="form-control @error('translator_name') is-invalid @enderror"
                                        id="translator_name" name="translator_name"
                                        value="{{ old('translator_name', $story->translator_name) }}">
                                    <div class="form-text text-muted">Điền tên người dịch/edit truyện</div>
                                    @error('translator_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="source_link" class="form-label">Link nguồn <span class="text-danger">*</span></label>
                                    <input required type="url" class="form-control @error('source_link') is-invalid @enderror" id="source_link"
                                        name="source_link" value="{{ old('source_link', $story->source_link) }}" placeholder="https://example.com">
                                    @error('source_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="is_monopoly" class="form-label mb-0">Truyện độc quyền <span
                                            class="text-muted">(nếu có)</span></label>
                                    <input type="checkbox" class="form-check-input" id="is_monopoly" name="is_monopoly"
                                        value="1" {{ old('is_monopoly', $story->is_monopoly) ? 'checked' : '' }}>
                                    <div class="form-text text-muted">
                                        Chọn nếu truyện chỉ đăng duy nhất tại {{ config('app.name') }}
                                    </div>
                                    @error('is_monopoly')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_18_plus"
                                            name="is_18_plus" value="1"
                                            {{ old('is_18_plus', $story->is_18_plus) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_18_plus">
                                            <span class="badge bg-danger me-1"><i class="fas fa-exclamation-triangle"></i>
                                                18+</span>
                                            Truyện có nội dung người lớn
                                        </label>
                                        <div class="form-text text-danger">
                                            Chọn nếu truyện có nội dung nhạy cảm, bạo lực hoặc tình dục không phù hợp với
                                            độc giả dưới 18 tuổi.
                                        </div>
                                    </div>
                                </div>



                                @if ($story->status == 'pending')
                                    <div class="alert alert-warning mt-4">
                                        <div class="mb-2"><i class="fas fa-info-circle me-2"></i> <strong>Lưu
                                                ý:</strong>
                                        </div>
                                        <p>Truyện của bạn đang chờ phê duyệt. Nếu bạn cập nhật thông tin, truyện sẽ quay lại
                                            trạng thái nháp và cần gửi yêu cầu phê duyệt lại.</p>
                                    </div>
                                @endif

                                @if ($story->status == 'published')
                                    @if ($story->completed == 1)
                                        {{-- Truyện đã hoàn thành --}}
                                        <div class="alert alert-warning mt-4">
                                            <div class="mb-2"><i class="fas fa-lock me-2"></i> <strong>Truyện đã hoàn
                                                    thành:</strong>
                                            </div>
                                            <p>Truyện của bạn đã được đánh dấu là hoàn thành. Khi bạn chỉnh sửa thông tin,
                                                hệ thống sẽ gửi yêu cầu duyệt cho admin và những thay đổi sẽ được áp dụng
                                                sau khi admin phê duyệt.</p>
                                            <p>Truyện vẫn tiếp tục hiển thị với thông tin hiện tại cho đến khi được phê
                                                duyệt.</p>
                                        </div>
                                    @else
                                        {{-- Truyện chưa hoàn thành --}}
                                        <div class="alert alert-success mt-4">
                                            <div class="mb-2"><i class="fas fa-edit me-2"></i> <strong>Truyện chưa hoàn
                                                    thành:</strong>
                                            </div>
                                            <p>Truyện của bạn đang được xuất bản nhưng chưa hoàn thành. Bạn có thể chỉnh sửa
                                                thông tin tự do mà không cần phê duyệt admin.</p>
                                            <p>Khi nào đánh dấu truyện là hoàn thành thì việc chỉnh sửa sẽ cần phê duyệt.
                                            </p>
                                        </div>
                                    @endif
                                @endif

                                @if ($story->status == 'published' && $hasPendingEditRequest)
                                    <div class="alert alert-warning mt-4">
                                        <div class="mb-2"><i class="fas fa-clock me-2"></i> <strong>Đang chờ
                                                duyệt:</strong>
                                        </div>
                                        <p>Bạn đã gửi yêu cầu chỉnh sửa thông tin truyện này và đang chờ admin phê duyệt.
                                        </p>
                                        <p>Bạn không thể thực hiện thêm thay đổi cho đến khi yêu cầu hiện tại được xử lý.
                                        </p>
                                        <p><small>Thời gian gửi yêu cầu:
                                                {{ $latestPendingEditRequest->submitted_at->format('H:i:s d/m/Y') }}</small>
                                        </p>
                                    </div>
                                @endif

                                @if ($story->status == 'rejected')
                                    <div class="alert alert-danger mt-4">
                                        <div class="mb-2"><i class="fas fa-times-circle me-2"></i> <strong>Truyện bị từ
                                                chối:</strong>
                                        </div>
                                        <p>Truyện của bạn đã bị từ chối phê duyệt. Vui lòng chỉnh sửa theo phản hồi của quản
                                            trị viên.</p>

                                        @if ($story->admin_note)
                                            <div class="mt-2">
                                                <strong>Lý do từ chối:</strong>
                                                <div class="p-2 bg-light rounded border border-danger mt-1">
                                                    {{ $story->admin_note }}</div>
                                            </div>
                                        @endif

                                        <p class="mt-2 mb-0">Sau khi chỉnh sửa, vui lòng chuyển đến tab "Yêu cầu duyệt" để
                                            gửi lại yêu cầu.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('user.author.stories') }}" class="btn btn-outline-danger me-2">Hủy</a>

                            @if ($story->status == 'published' && $hasPendingEditRequest)
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-clock me-1"></i> Đang chờ duyệt
                                </button>
                            @elseif($story->status == 'published' && $story->completed == 1)
                                {{-- Truyện đã hoàn thành, cần phê duyệt --}}
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#confirmEditModal">
                                    <i class="fas fa-paper-plane me-1"></i> Gửi yêu cầu chỉnh sửa
                                </button>

                                <!-- Modal xác nhận chỉnh sửa -->
                                <div class="modal fade" id="confirmEditModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Xác nhận
                                                    chỉnh sửa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Truyện đã hoàn thành</strong>
                                                </div>

                                                <p>Truyện của bạn đã được đánh dấu là hoàn thành.</p>
                                                <p>Khi bạn chỉnh sửa thông tin, hệ thống sẽ gửi yêu cầu duyệt cho admin và
                                                    những thay đổi sẽ được áp dụng sau khi admin phê duyệt.</p>
                                                <p>Truyện vẫn tiếp tục hiển thị với thông tin hiện tại cho đến khi được phê
                                                    duyệt.</p>

                                                <div class="mt-3">
                                                    <h6>Các thay đổi sẽ được gửi:</h6>
                                                    <div id="changesListContainer" class="border p-3 mt-2"
                                                        style="max-height: 200px; overflow-y: auto;">
                                                        <ul id="changesList" class="mb-0"></ul>
                                                    </div>
                                                </div>

                                                <p class="mt-3">Bạn có muốn tiếp tục?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-danger"
                                                    data-bs-dismiss="modal">Hủy bỏ</button>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-paper-plane me-1"></i> Gửi yêu cầu duyệt
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Truyện chưa hoàn thành hoặc chưa xuất bản, có thể sửa tự do --}}
                                <button type="submit" class="btn btn-outline-dark">
                                    Lưu thông tin
                                </button>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Tab Quản lý chương -->
                <div class="tab-pane fade" id="chapters" role="tabpanel" aria-labelledby="chapters-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            @if ($story->chapters->count() < 3)
                                <span class="chapter-count-badge warning">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $story->chapters->count() }}/3 chương
                                    tối thiểu
                                </span>
                            @else
                                <span class="chapter-count-badge success">
                                    <i class="fas fa-check-circle"></i> {{ $story->chapters->count() }} chương (Đủ điều
                                    kiện)
                                </span>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('user.author.stories.chapters.batch.create', $story->id) }}"
                                class="btn btn-outline-dark me-2">
                                <i class="fas fa-plus me-1"></i> Thêm nhiều chương
                            </a>
                            <a href="{{ route('user.author.stories.chapters.create', $story->id) }}"
                                class="btn btn-dark">
                                <i class="fas fa-plus me-1"></i> Thêm một chương
                            </a>
                        </div>
                    </div>

                    @if ($story->chapters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="60">Số</th>
                                        <th>Tên chương</th>
                                        <th width="100">Trạng thái</th>
                                        <th width="100">Hình thức</th>
                                        <th width="150">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($story->chapters->sortBy('number') as $chapter)
                                        <tr>
                                            <td>{{ $chapter->number }}</td>
                                            <td>
                                                {{ $chapter->title }}
                                                @if ($chapter->published_at && $chapter->published_at > now())
                                                    <span class="badge bg-info ms-1"
                                                        title="Hẹn giờ đăng: {{ $chapter->published_at->format('H:i d/m/Y') }}">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                @endif
                                                @if (!empty($chapter->password))
                                                    <span class="badge bg-warning ms-1" title="Chương có mật khẩu">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($chapter->status == 'published')
                                                    <span class="badge bg-success">Đã đăng</span>
                                                @else
                                                    <span class="badge bg-secondary">Nháp</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($chapter->is_free)
                                                    <span class="badge bg-success">Miễn phí</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning text-dark">{{ number_format($chapter->price) }}
                                                        xu</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('user.author.stories.chapters.edit', ['story' => $story->id, 'chapter' => $chapter->id]) }}"
                                                        class="btn btn-outline-dark">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteChapterModal{{ $chapter->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <p>Truyện chưa có chương nào. Vui lòng thêm ít nhất 3 chương trước khi gửi yêu cầu duyệt.</p>
                            <div class="mt-3">
                                <a href="{{ route('user.author.stories.chapters.batch.create', $story->id) }}"
                                    class="btn btn-outline-dark me-2">
                                    <i class="fas fa-plus me-1"></i> Thêm nhiều chương
                                </a>
                                <a href="{{ route('user.author.stories.chapters.create', $story->id) }}"
                                    class="btn btn-dark">
                                    <i class="fas fa-plus me-1"></i> Thêm một chương
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Modal xóa chương -->
                    @foreach ($story->chapters as $chapter)
                        <div class="modal fade" id="deleteChapterModal{{ $chapter->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Xác nhận xóa chương</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Bạn có chắc chắn muốn xóa chương "{{ $chapter->title }}" không?</p>
                                        <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Hành
                                            động này không thể hoàn tác.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Hủy</button>
                                        <form
                                            action="{{ route('user.author.stories.chapters.destroy', ['story' => $story->id, 'chapter' => $chapter->id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Xóa chương</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


                <!-- Tab Đề cử truyện -->
                <div class="tab-pane fade" id="featured" role="tabpanel" aria-labelledby="featured-tab">
                    <div class=" mb-4">

                        <div class="card-body">
                            {{-- Sử dụng biến từ controller để tránh duplicate queries --}}

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Thông tin đề cử:</h6>
                                <ul class="mb-0 mt-2">
                                    <li class="mb-1">Chi phí đề cử: <strong>{{ number_format($featuredPrice) }}
                                            xu</strong></li>
                                    <li class="mb-1">Thời gian đề cử: <strong>{{ $featuredDuration }} ngày</strong></li>
                                    <li class="mb-1">Truyện sẽ xuất hiện ở vị trí nổi bật trên trang chủ</li>
                                    <li>Đề cử của admin sẽ có ưu tiên cao hơn đề cử của tác giả</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Số xu hiện tại</h6>
                                            <h4 class="text-primary">{{ number_format(Auth::user()->coins) }} xu</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Chi phí đề cử</h6>
                                            <h4 class="text-warning">{{ number_format($featuredPrice) }} xu</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($story->is_featured || $story->isCurrentlyAdminFeatured())
                                <div class="alert alert-warning">
                                    <i class="fas fa-crown me-2"></i>
                                    <strong>Truyện đã được admin đề cử:</strong> Truyện của bạn đã được admin đề cử.
                                    @php
                                        $adminFeatured = $story->activeAdminFeatured;
                                        $order = $adminFeatured
                                            ? $adminFeatured->featured_order
                                            : $story->featured_order;
                                    @endphp
                                    @if ($order)
                                        <br>Thứ tự: #{{ $order }}
                                    @endif
                                    <br>Đề cử của admin có ưu tiên cao hơn đề cử của tác giả.
                                </div>
                            @elseif($story->isCurrentlyAuthorFeatured())
                                <div class="alert alert-success">
                                    <i class="fas fa-star me-2"></i>
                                    <strong>Đang được đề cử:</strong> Truyện của bạn đang được đề cử bởi tác giả.
                                    @php
                                        $authorFeatured = $story->activeAuthorFeatured;
                                    @endphp
                                    @if ($authorFeatured)
                                        <br>
                                        <small class="text-muted">
                                            Đề cử từ: {{ $authorFeatured->featured_at->format('d/m/Y H:i') }}<br>
                                            Hết hạn: {{ $authorFeatured->featured_until->format('d/m/Y H:i') }}
                                            (Còn {{ $authorFeatured->days_remaining }} ngày)
                                        </small>
                                    @endif
                                </div>
                            @else
                                @if ($story->status !== 'published')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Chưa thể đề cử:</strong> Truyện phải được xuất bản trước khi có thể đề cử.
                                    </div>
                                @elseif(Auth::user()->coins < $featuredPrice)
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <strong>Không đủ xu:</strong> Bạn cần ít nhất {{ number_format($featuredPrice) }}
                                        xu để đề cử truyện.
                                        <br>
                                        <a href="{{ route('user.deposit') }}"
                                            class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-coins me-1"></i> Nạp xu ngay
                                        </a>
                                    </div>
                                @else
                                    <form action="{{ route('user.author.stories.featured', $story->id) }}" method="POST"
                                        id="featuredForm">
                                        @csrf
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Có thể đề cử:</strong> Truyện của bạn đủ điều kiện để đề cử lên trang
                                            chủ.
                                        </div>

                                        <div class="d-grid">
                                            <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal"
                                                data-bs-target="#confirmFeaturedModal">
                                                <i class="fas fa-star me-2"></i>Đề cử truyện
                                                ({{ number_format($featuredPrice) }} xu)
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Yêu cầu duyệt -->
                <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Yêu cầu để truyện được duyệt:</h5>
                        <ul class="mb-0 mt-2">
                            <li class="mb-2">Truyện phải có đầy đủ thông tin cơ bản (tiêu đề, mô tả, thể loại, tác giả)
                            </li>
                            <li class="mb-2">Truyện phải có ít nhất 3 chương</li>
                            <li class="mb-2">Nội dung phải tuân thủ các quy định của trang web</li>
                            <li>Truyện không vi phạm bản quyền (nếu là truyện dịch/sưu tầm)</li>
                        </ul>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Trạng thái yêu cầu</h5>
                        </div>
                        <div class="card-body">
                            @if ($story->status == 'draft')
                                @if ($story->chapters->count() < 3)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Chưa đủ điều kiện:</strong> Truyện cần có ít nhất 3 chương trước khi gửi yêu
                                        cầu duyệt.
                                    </div>
                                @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Đủ điều kiện:</strong> Bạn có thể gửi yêu cầu duyệt truyện ngay bây giờ.
                                    </div>

                                    <form action="{{ route('user.author.stories.submit.for.review', $story->id) }}"
                                        method="POST" class="review-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="review_note" class="form-label">Ghi chú cho quản trị viên (không
                                                bắt buộc)</label>
                                            <textarea class="form-control" id="review_note" name="review_note" rows="4"
                                                placeholder="Nhập ghi chú hoặc thông tin bổ sung cho quản trị viên...">{{ old('review_note') }}</textarea>
                                            <div class="form-text">Bạn có thể để lại ghi chú cho quản trị viên về truyện
                                                của mình.</div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-outline-dark">
                                                <i class="fas fa-paper-plane me-1"></i> Gửi yêu cầu duyệt
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @elseif($story->status == 'pending')
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Đang chờ duyệt:</strong> Truyện của bạn đã được gửi đi và đang chờ quản trị viên
                                    phê duyệt.
                                </div>

                                @if ($story->submitted_at)
                                    <div class="d-flex align-items-center mt-3">
                                        <div class="text-muted me-3">Đã gửi yêu cầu vào:</div>
                                        <div class="fw-bold">
                                            {{ \Carbon\Carbon::parse($story->submitted_at)->format('H:i:s d/m/Y') }}</div>
                                    </div>
                                @endif

                                @if ($story->review_note)
                                    <div class="mt-3">
                                        <div class="text-muted mb-2">Ghi chú của bạn:</div>
                                        <div class="p-3 bg-light rounded">{{ $story->review_note }}</div>
                                    </div>
                                @endif
                            @elseif($story->status == 'published')
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Đã được duyệt:</strong> Truyện của bạn đã được phê duyệt và xuất bản thành công.
                                </div>
                            @elseif($story->status == 'rejected')
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Đã bị từ chối:</strong> Truyện của bạn không được duyệt.
                                </div>

                                @if ($story->admin_note)
                                    <div class="mt-3">
                                        <div class="fw-bold text-danger mb-2">Lý do từ chối:</div>
                                        <div class="p-3 bg-light rounded border border-danger">{{ $story->admin_note }}
                                        </div>
                                    </div>
                                @endif

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Hướng dẫn:</strong> Vui lòng chỉnh sửa truyện theo phản hồi của quản trị viên,
                                    sau đó bạn có thể gửi lại yêu cầu duyệt.
                                </div>

                                @if ($story->chapters->count() >= 3)
                                    <form action="{{ route('user.author.stories.submit.for.review', $story->id) }}"
                                        method="POST" class="review-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="review_note" class="form-label">Ghi chú cho quản trị viên</label>
                                            <textarea class="form-control" id="review_note" name="review_note" rows="4"
                                                placeholder="Giải thích những thay đổi bạn đã thực hiện để khắc phục vấn đề...">{{ old('review_note') }}</textarea>
                                            <div class="form-text">Vui lòng giải thích những thay đổi bạn đã thực hiện để
                                                khắc phục vấn đề.</div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-1"></i> Gửi lại yêu cầu duyệt
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Chưa đủ điều kiện:</strong> Truyện cần có ít nhất 3 chương trước khi gửi lại
                                        yêu cầu duyệt.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận đề cử -->
    <div class="modal fade" id="confirmFeaturedModal" tabindex="-1" aria-labelledby="confirmFeaturedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmFeaturedModalLabel">
                        <i class="fas fa-star me-2"></i>Xác nhận đề cử truyện
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Xác nhận đề cử:</strong> Bạn có chắc chắn muốn đề cử truyện
                        <strong>"{{ $story->title }}"</strong> lên trang chủ?
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Chi phí</h6>
                                <h4 class="text-warning">
                                    {{ number_format($featuredPrice) }} xu</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h6 class="text-muted">Thời gian</h6>
                                <h4 class="text-info">{{ $featuredDuration }}
                                    ngày</h4>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6>Lưu ý:</h6>
                        <ul class="small text-muted">
                            <li>Truyện sẽ xuất hiện ở vị trí nổi bật trên trang chủ</li>
                            <li>Đề cử của admin sẽ có ưu tiên cao hơn</li>
                            <li>Xu sẽ được trừ ngay sau khi xác nhận</li>
                            <li>Không thể hoàn lại xu sau khi đề cử</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-warning" id="confirmFeaturedBtn">
                        <i class="fas fa-star me-1"></i>Xác nhận đề cử
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/category-tags.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Cover upload functionality (existing code)
            const $coverInput = $('#cover');
            const $uploadArea = $('#uploadArea');
            const $previewArea = $('#previewArea');
            const $coverPreview = $('#coverPreview');
            const originalCoverSrc = '{{ $story->cover ? Storage::url($story->cover) : '' }}';

            // Click upload area to select file
            $uploadArea.on('click', function() {
                $coverInput.click();
            });

            // Change cover button
            $(document).on('click', '#changeCover', function(e) {
                e.stopPropagation();
                $coverInput.click();
            });

            // Remove cover button
            $(document).on('click', '#removeCover', function(e) {
                e.stopPropagation();
                $coverInput.val('');

                if (originalCoverSrc) {
                    $coverPreview.attr('src', originalCoverSrc);
                } else {
                    $previewArea.addClass('d-none');
                    $uploadArea.removeClass('d-none');
                }
            });

            // Handle file selection
            $coverInput.on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $coverPreview.attr('src', e.target.result);
                        $uploadArea.addClass('d-none');
                        $previewArea.removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Drag and drop functionality
            $uploadArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            $uploadArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            $uploadArea.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        $coverInput[0].files = files;
                        $coverInput.trigger('change');
                    }
                }
            });

            // Initialize Category Tags
            const defaultCategories = [
                'Tình cảm', 'Hài hước', 'Viễn tưởng', 'Kinh dí', 'Trinh thám',
                'Hành động', 'Phiêu lưu', 'Lãng mạn', 'Drama', 'Học đường',
                'Sinh tồn', 'Isekai', 'Xuyên không', 'Tu tiên', 'Đô thị',
                'Quân sự', 'Lịch sử', 'Thể thao', 'Game', 'Mecha'
            ];

            const allCategories = @json($allCategories ?? null) || defaultCategories;

            const categoryTags = new CategoryTags({
                containerSelector: '#categoryContainer',
                inputSelector: '#categoryInput',
                hiddenInputSelector: '#categoryInputHidden',
                tagsSelector: '#categoryTags',
                suggestionsSelector: '#categorySuggestions',
                allCategories: allCategories
            });

            // CKEditor
            CKEDITOR.replace('description', {
                on: {
                    change: function(evt) {
                        this.updateElement();
                    }
                },
                height: 200,
                removePlugins: 'uploadimage,image2,uploadfile,filebrowser',
            });

            // Tab handling and other existing functionality
            @if ($errors->has('review_note'))
                $('#storyTab button[data-bs-target="#review"]').tab('show');
            @endif

            let hash = window.location.hash;
            if (hash) {
                const tab = hash.replace('#', '');
                if (['edit', 'chapters', 'review'].includes(tab)) {
                    $('#storyTab button[data-bs-target="#' + tab + '"]').tab('show');
                }
            }

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).data('bs-target');
                history.pushState(null, null, target);
            });

            // Change detection functionality
            function detectChanges() {
                let changes = [];
                const originalData = {
                    title: @json($story->title),
                    description: CKEDITOR.instances.description.getData() !== @json($story->description),
                    author_name: @json($story->author_name),
                    translator_name: @json($story->translator_name),
                    story_type: @json($story->story_type),
                    categories: @json($categoryNames),
                    is_18_plus: @json($story->is_18_plus),
                    cover: $('#cover').val() ? true : false
                };

                // Check changes (existing logic)
                if ($('#translator_name').val() !== originalData.translator_name) {
                    const oldValue = originalData.translator_name || '(không có)';
                    const newValue = $('#translator_name').val() || '(không có)';
                    changes.push('<li>Tên người dịch: <span class="text-danger">' + oldValue +
                        '</span> → <span class="text-success">' + newValue + '</span></li>');
                }

                if ($('#title').val() !== originalData.title) {
                    changes.push('<li>Tiêu đề: <span class="text-danger">' + originalData.title +
                        '</span> → <span class="text-success">' + $('#title').val() + '</span></li>');
                }

                if (originalData.description) {
                    changes.push('<li>Mô tả đã được thay đổi</li>');
                }

                if ($('#author_name').val() !== originalData.author_name) {
                    changes.push('<li>Tên tác giả: <span class="text-danger">' + originalData.author_name +
                        '</span> → <span class="text-success">' + $('#author_name').val() + '</span></li>');
                }

                if ($('#categoryInputHidden').val() !== originalData.categories) {
                    changes.push('<li>Thể loại: <span class="text-danger">' + originalData.categories +
                        '</span> → <span class="text-success">' + $('#categoryInputHidden').val() +
                        '</span></li>');
                }

                if ($('#story_type').val() !== originalData.story_type) {
                    const storyTypes = {
                        'original': 'Sáng tác',
                        'translated': 'Dịch/Edit',
                        'collected': 'Sưu tầm'
                    };
                    changes.push('<li>Loại truyện: <span class="text-danger">' + storyTypes[originalData
                            .story_type] +
                        '</span> → <span class="text-success">' + storyTypes[$('#story_type').val()] +
                        '</span></li>');
                }

                if (originalData.cover) {
                    changes.push('<li>Ảnh bìa được thay đổi</li>');
                }

                if ($('#is_18_plus').is(':checked') !== originalData.is_18_plus) {
                    if ($('#is_18_plus').is(':checked')) {
                        changes.push('<li>Đánh dấu truyện là <span class="text-danger">nội dung 18+</span></li>');
                    } else {
                        changes.push(
                            '<li>Bỏ đánh dấu truyện là <span class="text-success">nội dung thông thường</span></li>'
                        );
                    }
                }

                return changes;
            }

            $('#confirmEditModal').on('show.bs.modal', function(e) {
                const changes = detectChanges();
                const changesList = $('#changesList');

                changesList.empty();

                if (changes.length === 0) {
                    changesList.append('<li>Không có thay đổi nào được phát hiện</li>');
                    $('#confirmEditModal button[type="submit"]').prop('disabled', true);
                } else {
                    changes.forEach(change => {
                        changesList.append(change);
                    });
                    $('#confirmEditModal button[type="submit"]').prop('disabled', false);
                }
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('confirmFeaturedBtn').addEventListener('click', function() {
                const form = document.getElementById('featuredForm');
                if (form) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
