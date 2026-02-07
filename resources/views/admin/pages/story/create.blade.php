@extends('admin.layouts.app')

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .row .col-md-8,
        .row .col-md-4 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-control,
        .form-select {
            font-size: 0.9rem;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .form-control,
        .form-select {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Thêm truyện mới</h5>
                </div>
                <div class="card-body">

                    <form action="{{ route('admin.stories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('slug') }}">
                                    <small class="form-text text-muted">
                                        Slug là phần đường dẫn của truyện. Ví dụ: truyen-1, truyen-2, ...
                                    </small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Mô tả <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" rows="5"
                                        class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="author_name">Tên tác giả</label>
                                            <input type="text" name="author_name" id="author_name"
                                                class="form-control @error('author_name') is-invalid @enderror"
                                                value="{{ old('author_name') }}">
                                            @error('author_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="editor_id">Editor</label>
                                            <select name="editor_id" id="editor_id" class="form-control @error('editor_id') is-invalid @enderror">
                                                <option value="">-- Chọn editor --</option>
                                                @foreach ($adminUsers as $admin)
                                                    <option value="{{ $admin->id }}" {{ old('editor_id') == $admin->id ? 'selected' : '' }}>
                                                        {{ $admin->name }} ({{ $admin->role }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('editor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cover">Ảnh bìa <span class="text-danger">*</span></label>
                                    <input type="file" name="cover" id="cover"
                                        class="form-control @error('cover') is-invalid @enderror"
                                        onchange="previewImage(this)" required>
                                    <div id="cover-preview" class="mt-2"></div>
                                    @error('cover')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="categories">Thể loại <span class="text-danger">*</span></label>
                                    <div class="category-tags-container">
                                        <div class="selected-categories mb-2" id="selected-categories">
                                        </div>
                                        <select id="category-select" class="form-control">
                                            <option value="">-- Chọn thể loại --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                    @if($category->is_main)
                                                        ⭐ (Thể loại chính)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('categories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tag_id">Chủ đề (Tag)</label>
                                    <select name="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror">
                                        <option value="">-- Không chọn --</option>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}" {{ old('tag_id') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('tag_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="draft">Bản nháp</option>
                                        <option value="published" selected>Xuất bản</option>
                                    </select>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="has_combo">Bán combo (tất cả chương)</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="has_combo" class="form-check-input" id="has_combo"
                                                role="switch">
                                        </div>
                                    </div>
                                </div>

                                <div id="combo-pricing-container" class="mt-3" style="display: none;">
                                    <div class="form-group">
                                        <label for="combo_price">Giá combo (cám) <span class="text-danger">*</span></label>
                                        <input type="number" name="combo_price" id="combo_price" 
                                               class="form-control @error('combo_price') is-invalid @enderror"
                                               value="{{ old('combo_price', 0) }}" min="0">
                                        <small class="text-muted">Đặt giá combo cho tất cả các chương</small>
                                        @error('combo_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <p class="mb-0"><i class="fas fa-info-circle me-1"></i> Bạn có thể thiết lập giá combo sau khi thêm các chương cho truyện.</p>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="is_18_plus">Nội dung 18+ <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_18_plus" class="form-check-input" id="is_18_plus"
                                                role="switch" {{ old('is_18_plus') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="completed">Hoàn thành <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="completed" class="form-check-input" id="completed"
                                                role="switch" {{ old('completed') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="is_featured">Truyện đề cử</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured"
                                                role="switch" {{ old('is_featured') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div id="featured-order-container" class="form-group mt-3" style="{{ old('is_featured') ? '' : 'display: none;' }}">
                                    <label for="featured_order">Thứ tự đề cử</label>
                                    <input type="number" name="featured_order" id="featured_order" 
                                           class="form-control @error('featured_order') is-invalid @enderror"
                                           value="{{ old('featured_order') }}" min="1" 
                                           placeholder="Để trống để tự động gán thứ tự">
                                    <small class="form-text text-muted">
                                        Số nhỏ sẽ hiển thị trước. Hiện tại có {{ \App\Models\Story::where('is_featured', true)->count() }} truyện đề cử.
                                    </small>
                                    @error('featured_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn bg-gradient-primary">Lưu truyện</button>
                                <a href="{{ route('admin.stories.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts-admin')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('cover-preview');
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-thumbnail', 'mt-2');
                    img.style.maxHeight = '200px';
                    preview.appendChild(img);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category-select');
            const selectedCategoriesContainer = document.getElementById('selected-categories');

            updateCategoriesInput();

            categorySelect.addEventListener('change', function() {
                const selectedValue = this.value;
                if (selectedValue) {
                    addCategory(selectedValue, this.options[this.selectedIndex].text);
                    this.value = '';
                }
            });

            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                const selectedCategories = document.querySelectorAll('.category-tag');
                const categoryTagsContainer = document.querySelector('.category-tags-container');
                
                if (selectedCategories.length === 0) {
                    event.preventDefault();
                    categoryTagsContainer.classList.add('is-invalid');
                    categorySelect.classList.add('is-invalid');
                    return false;
                } else {
                    categoryTagsContainer.classList.remove('is-invalid');
                    categorySelect.classList.remove('is-invalid');
                }

                updateCategoriesInput();
                
                const hiddenInputs = document.querySelectorAll('input[name="categories[]"]');
                console.log('Hidden inputs before submit:', Array.from(hiddenInputs).map(input => input.value));

                return true;
            });
            
            const hasComboCheckbox = document.getElementById('has_combo');
            const comboPricingContainer = document.getElementById('combo-pricing-container');
            const comboPriceInput = document.getElementById('combo_price');
            
            hasComboCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    comboPricingContainer.style.display = 'block';
                } else {
                    comboPricingContainer.style.display = 'none';
                    comboPriceInput.value = '0';
                }
            });
            
            if ("{{ old('has_combo') }}" === '1') {
                hasComboCheckbox.checked = true;
                comboPricingContainer.style.display = 'block';
            }

            // Handle featured toggle
            const isFeaturedCheckbox = document.getElementById('is_featured');
            const featuredOrderContainer = document.getElementById('featured-order-container');
            const featuredOrderInput = document.getElementById('featured_order');
            
            isFeaturedCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    featuredOrderContainer.style.display = 'block';
                } else {
                    featuredOrderContainer.style.display = 'none';
                    featuredOrderInput.value = '';
                }
            });
        });

        function addCategory(categoryId, categoryName) {
            const selectedCategoriesContainer = document.getElementById('selected-categories');
            const categorySelect = document.getElementById('category-select');
            
            if (document.querySelector(`[data-category-id="${categoryId}"]`)) {
                return;
            }
            
            const tag = document.createElement('span');
            tag.className = 'badge bg-primary me-2 mb-2 category-tag';
            tag.setAttribute('data-category-id', categoryId);
            tag.innerHTML = `${categoryName} <button type="button" class="btn-close btn-close-white ms-1" onclick="removeCategory(${categoryId})"></button>`;
            
            selectedCategoriesContainer.appendChild(tag);
            
            const option = categorySelect.querySelector(`option[value="${categoryId}"]`);
            if (option) {
                option.disabled = true;
            }
            
            updateCategoriesInput();
        }
        
        function removeCategory(categoryId) {
            const tag = document.querySelector(`[data-category-id="${categoryId}"]`);
            const categorySelect = document.getElementById('category-select');
            
            if (tag) {
                tag.remove();
                
                const option = categorySelect.querySelector(`option[value="${categoryId}"]`);
                if (option) {
                    option.disabled = false;
                }
                
                updateCategoriesInput();
            }
        }
        
        function updateCategoriesInput() {
            const selectedCategories = document.querySelectorAll('.category-tag');
            const categoryIds = Array.from(selectedCategories).map(tag => tag.getAttribute('data-category-id'));
            
            const existingInputs = document.querySelectorAll('input[name="categories[]"]');
            existingInputs.forEach(input => input.remove());
            
            categoryIds.forEach(categoryId => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'categories[]';
                hiddenInput.value = categoryId;
                document.querySelector('.category-tags-container').appendChild(hiddenInput);
            });
        }
    </script>

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('description', {
            on: {
                change: function(evt) {
                    this.updateElement();
                }
            },
            height: 200,
            removePlugins: 'uploadimage,image2,uploadfile,filebrowser',
        });
    </script>
@endpush

@push('styles')
    <style>
        .category-tags-container {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
        }

        .selected-categories {
            min-height: 40px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .category-tag {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            background-color: #5e72e4 !important;
            color: white;
            border: none;
            cursor: default;
        }

        .category-tag .btn-close {
            font-size: 0.75rem;
            margin-left: 0.5rem;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .category-tag .btn-close:hover {
            opacity: 1;
        }

        .category-tag .btn-close:focus {
            box-shadow: none;
        }

        #category-select {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        #category-select:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
        }

        .category-tags-container.is-invalid #category-select {
            border-color: #dc3545;
        }

        .category-tags-container.is-invalid .selected-categories {
            border-color: #dc3545;
        }
    </style>
@endpush
