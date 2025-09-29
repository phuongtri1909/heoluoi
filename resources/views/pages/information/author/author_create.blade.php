@extends('layouts.information')

@section('info_title', 'Đăng truyện mới')
@section('info_description', 'Đăng truyện mới trên ' . request()->getHost())
@section('info_keyword', 'đăng truyện, tác giả, sáng tác, ' . request()->getHost())
@section('info_section_title', 'Đăng truyện mới')
@section('info_section_desc', 'Tạo truyện mới của bạn')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/category-tags.css') }}" rel="stylesheet" />
    <style>
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
    </style>
@endpush

@section('info_content')
    <div class="mb-4">
        <a href="{{ route('user.author.stories') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('user.author.stories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">

                <div class="mb-3">
                    <label class="form-label fw-bold">Ảnh bìa <span class="text-danger">*</span></label>

                    <input type="file" id="cover" name="cover" accept="image/*" required>
                    @error('cover')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <!-- Upload Area -->
                    <div class="cover-upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Chọn ảnh bìa truyện</div>
                        <div class="upload-hint">Kéo thả ảnh vào đây hoặc click để chọn</div>
                        <div class="upload-hint mt-2">
                            <small>Tỷ lệ ảnh tốt nhất: 2:3 | Kích thước tối thiểu: 300x450px</small>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div class="cover-preview d-none mt-3" id="previewArea">
                        <div class="cover-preview-container">
                            <img src="" class="preview-cover" id="coverPreview" alt="Preview">
                            <div class="cover-overlay">
                                <div class="cover-actions">
                                    <button type="button" class="cover-action-btn change" id="changeCover" title="Thay đổi ảnh">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="cover-action-btn remove" id="removeCover" title="Xóa ảnh">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề truyện <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_input" class="form-label">Thể loại <span class="text-danger">*</span></label>
                    
                    <div class="category-input-container @error('category_input') is-invalid @enderror" id="categoryContainer">
                        <div class="category-tags" id="categoryTags"></div>
                        <input type="text" 
                               class="category-input" 
                               id="categoryInput" 
                               autocomplete="off">
                        <div class="category-suggestions d-none" id="categorySuggestions"></div>
                    </div>
                    
                    <!-- Hidden input to store selected categories -->
                    <input type="hidden" name="category_input" id="categoryInputHidden" value="{{ old('category_input') }}">
                    
                    <div class="form-text text-muted">
                       Nhập thể loại hoặc dán nhiều thể loại cách nhau bằng dấu phẩy...
                    </div>
                    
                    @error('category_input')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description2" name="description"
                        rows="6">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">



                <div class="mb-3">
                    <label for="author_name" class="form-label">Tên tác giả <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('author_name') is-invalid @enderror" id="author_name"
                        name="author_name" value="{{ old('author_name') }}" required>
                    @error('author_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="story_type" class="form-label">Loại truyện <span class="text-danger">*</span></label>
                    <select class="form-select @error('story_type') is-invalid @enderror" id="story_type" name="story_type"
                        required>
                        <option value="">-- Chọn loại truyện --</option>
                        <option value="original" {{ old('story_type') == 'original' ? 'selected' : '' }}>Sáng tác</option>
                        <option value="translated" {{ old('story_type') == 'translated' ? 'selected' : '' }}>Dịch/Edit
                        </option>
                        <option value="collected" {{ old('story_type') == 'collected' ? 'selected' : '' }}>Sưu tầm</option>
                    </select>
                    @error('story_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="translator_name" class="form-label">Chuyển ngữ <span class="text-muted">(nếu
                            có)</span></label>
                    <input type="text" class="form-control @error('translator_name') is-invalid @enderror"
                        id="translator_name" name="translator_name" value="{{ old('translator_name') }}">
                    <div class="form-text text-muted">Điền tên người dịch/edit truyện</div>
                    @error('translator_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="source_link" class="form-label">Link nguồn <span class="text-danger">*</span></label>
                    <input required type="url" class="form-control @error('source_link') is-invalid @enderror" id="source_link"
                        name="source_link" value="{{ old('source_link') }}" placeholder="https://example.com">
                    @error('source_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_monopoly" class="form-label mb-0">Truyện độc quyền <span class="text-muted">(nếu
                            có)</span></label>
                    <input type="checkbox" class="form-check-input" id="is_monopoly" name="is_monopoly" value="1"
                        {{ old('is_monopoly') ? 'checked' : '' }}>
                    <div class="form-text text-muted">
                        Chọn nếu truyện chỉ đăng duy nhất tại {{ config('app.name') }}
                    </div>
                    @error('is_monopoly')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 mt-4">
                    <div class="form-check">
                        <label class="form-check-label fw-bold" for="is_18_plus">
                            <span class="badge bg-danger me-1"><i class="fas fa-exclamation-triangle"></i> 18+</span>
                            Truyện có nội dung người lớn
                        </label>
                        <input class="form-check-input" type="checkbox" id="is_18_plus" name="is_18_plus"
                            value="1" {{ old('is_18_plus') ? 'checked' : '' }}>

                        <div class="form-text text-danger">
                            Chọn nếu truyện có nội dung nhạy cảm, bạo lực hoặc tình dục không phù hợp với độc giả dưới 18
                            tuổi.
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('user.author.stories') }}" class="btn btn-outline-danger me-2">Hủy</a>
            <button type="submit" class="btn btn-outline-dark">
                Đăng truyện
            </button>
        </div>
    </form>
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
                $previewArea.addClass('d-none');
                $uploadArea.removeClass('d-none');
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
            CKEDITOR.replace('description2', {
                on: {
                    change: function(evt) {
                        this.updateElement();
                    }
                },
                height: 200,
                removePlugins: 'uploadimage,image2,uploadfile,filebrowser',
            });
        });
    </script>
@endpush
