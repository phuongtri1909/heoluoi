@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <h5 class="mb-0">Chỉnh sửa truyện</h5>
                </div>
                <div class="card-body">

                    <form action="{{ route('admin.stories.update', $story) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $story->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug <span class="text-danger">*</span></label>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug', $story->slug) }}" required>
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
                                        class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $story->description) }}</textarea>
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
                                                value="{{ old('author_name', $story->author_name) }}">
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
                                                    <option value="{{ $admin->id }}" {{ old('editor_id', $story->editor_id) == $admin->id ? 'selected' : '' }}>
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

                                <div class="form-group mt-3">
                                    <label for="story_type">Loại truyện <span class="text-danger">*</span></label>
                                    <select name="story_type" id="story_type" class="form-control @error('story_type') is-invalid @enderror">
                                        <option value="">-- Chọn loại truyện --</option>
                                        <option value="original" {{ old('story_type', $story->story_type) == 'original' ? 'selected' : '' }}>Sáng tác</option>
                                        <option value="translated" {{ old('story_type', $story->story_type) == 'translated' ? 'selected' : '' }}>Dịch</option>
                                        <option value="rewritten" {{ old('story_type', $story->story_type) == 'rewritten' ? 'selected' : '' }}>Chuyển ngữ</option>
                                    </select>
                                    @error('story_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cover">Ảnh bìa</label>
                                    <input type="file" name="cover" id="cover"
                                        class="form-control @error('cover') is-invalid @enderror"
                                        onchange="previewImage(this)">
                                    <div id="cover-preview" class="mt-2">
                                        @if ($story->cover)
                                            <img src="{{ Storage::url($story->cover) }}" class="img-thumbnail"
                                                style="max-height: 200px">
                                        @endif
                                    </div>
                                    @error('cover')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="categories">Thể loại <span class="text-danger">*</span></label>
                                    <div class="category-tags-container">
                                        <div class="selected-categories mb-2" id="selected-categories">
                                            @foreach ($story->categories as $category)
                                                <span class="badge bg-primary me-2 mb-2 category-tag" data-category-id="{{ $category->id }}">
                                                    {{ $category->name }}
                                                    @if($category->is_main)
                                                        ⭐
                                                    @endif
                                                    <button type="button" class="btn-close btn-close-white ms-1" onclick="removeCategory({{ $category->id }})"></button>
                                                </span>
                                            @endforeach
                                        </div>
                                        <select id="category-select" class="form-control">
                                            <option value="">-- Chọn thể loại --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ in_array($category->id, $story->categories->pluck('id')->toArray()) ? 'disabled' : '' }}>
                                                    {{ $category->name }}
                                                    @if($category->is_main)
                                                        ⭐ (Thể loại chính)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <!-- Hidden inputs will be generated dynamically by JavaScript -->
                                    </div>
                                    @error('categories')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="draft" {{ $story->status === 'draft' ? 'selected' : '' }}>Bản nháp
                                        </option>
                                        <option value="published" {{ $story->status === 'published' ? 'selected' : '' }}>
                                            Xuất bản</option>
                                    </select>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="completed">Truyện đã hoàn thành <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="completed" class="form-check-input" id="completed"
                                                role="switch" {{ $story->completed ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="has_combo">Bán combo (tất cả chương)</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="has_combo" class="form-check-input" id="has_combo"
                                                role="switch" {{ $story->has_combo ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div id="combo-pricing-container" class="mt-3" style="{{ $story->has_combo ? '' : 'display: none;' }}">
                                    <div class="form-group">
                                        <label for="combo_price">Giá combo (xu) <span class="text-danger">*</span></label>
                                        <input type="number" name="combo_price" id="combo_price" 
                                               class="form-control @error('combo_price') is-invalid @enderror"
                                               value="{{ old('combo_price', $story->combo_price) }}" min="0">
                                        @error('combo_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @php
                                        $totalChapters = $story->chapters()->where('status', 'published')->count();
                                        $paidChapters = $story->chapters()->where('status', 'published')->where('is_free', 0)->count();
                                        $totalRegularPrice = $story->chapters()->where('status', 'published')->where('is_free', 0)->sum('price');
                                        $savings = $totalRegularPrice > 0 ? $totalRegularPrice - ($story->combo_price ?? 0) : 0;
                                        $savingsPercent = $totalRegularPrice > 0 ? round(($savings / $totalRegularPrice) * 100) : 0;
                                    @endphp

                                    <div class="alert alert-info mt-3" id="combo-summary">
                                        <h6>Thông tin combo:</h6>
                                        <p class="mb-1">- Tổng số chương: <span id="total-chapters">{{ $totalChapters }}</span></p>
                                        <p class="mb-1">- Số chương trả phí: <span id="paid-chapters">{{ $paidChapters }}</span></p>
                                        <p class="mb-1">- Tổng giá nếu mua lẻ: <span id="total-regular-price">{{ $totalRegularPrice }}</span> xu</p>
                                        <p class="mb-1">- Tiết kiệm: <span id="savings">{{ $savings }}</span> xu (<span id="savings-percent">{{ $savingsPercent }}</span>%)</p>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="is_18_plus">Nội dung 18+ <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_18_plus" class="form-check-input" id="is_18_plus"
                                                role="switch" {{ old('is_18_plus', $story->is_18_plus) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="completed">Hoàn thành <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="completed" class="form-check-input" id="completed"
                                                role="switch" {{ old('completed', $story->completed) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 me-3" for="is_featured">Truyện đề cử</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured"
                                                role="switch" {{ old('is_featured', $story->is_featured) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div id="featured-order-container" class="form-group mt-3" style="{{ old('is_featured', $story->is_featured) ? '' : 'display: none;' }}">
                                    <label for="featured_order">Thứ tự đề cử</label>
                                    <input type="number" name="featured_order" id="featured_order" 
                                           class="form-control @error('featured_order') is-invalid @enderror"
                                           value="{{ old('featured_order', $story->featured_order) }}" min="1" 
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
                                <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
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
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');

            // Initialize hidden inputs for existing categories
            updateCategoriesInput();

            // Auto-generate slug from title
            titleInput.addEventListener('input', function() {
                const title = this.value;
                let slug = title
                    .toLowerCase()
                    .trim();
                
                // Convert Vietnamese characters to ASCII
                slug = slug
                    .replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a')
                    .replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e')
                    .replace(/ì|í|ị|ỉ|ĩ/g, 'i')
                    .replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o')
                    .replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u')
                    .replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single
                    .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
                
                slugInput.value = slug;
            });

            // Add category when select changes
            categorySelect.addEventListener('change', function() {
                const selectedValue = this.value;
                if (selectedValue) {
                    addCategory(selectedValue, this.options[this.selectedIndex].text);
                    this.value = ''; // Reset select
                }
            });

            // Form validation before submit
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

                // Ensure hidden inputs are updated before submit
                updateCategoriesInput();
                
                // Debug: Log the hidden inputs
                const hiddenInputs = document.querySelectorAll('input[name="categories[]"]');
                console.log('Hidden inputs before submit:', Array.from(hiddenInputs).map(input => input.value));

                return true;
            });

            // Handle combo pricing toggle
            const hasComboCheckbox = document.getElementById('has_combo');
            const comboPricingContainer = document.getElementById('combo-pricing-container');
            const comboPriceInput = document.getElementById('combo_price');
            
            hasComboCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    comboPricingContainer.style.display = 'block';
                    updateComboSummary();
                } else {
                    comboPricingContainer.style.display = 'none';
                    comboPriceInput.value = '0';
                }
            });
            
            // Update combo summary calculations when price changes
            comboPriceInput.addEventListener('input', updateComboSummary);
            
            function updateComboSummary() {
                const totalRegularPrice = parseInt(document.getElementById('total-regular-price').textContent) || 0;
                const comboPrice = parseInt(comboPriceInput.value) || 0;
                
                const savings = totalRegularPrice - comboPrice;
                const savingsPercent = totalRegularPrice > 0 ? Math.round((savings / totalRegularPrice) * 100) : 0;
                
                document.getElementById('savings').textContent = savings > 0 ? savings : 0;
                document.getElementById('savings-percent').textContent = savingsPercent > 0 ? savingsPercent : 0;
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

        // Add category function
        function addCategory(categoryId, categoryName) {
            const selectedCategoriesContainer = document.getElementById('selected-categories');
            const categorySelect = document.getElementById('category-select');
            const categoriesInput = document.getElementById('categories-input');
            
            // Check if category already exists
            if (document.querySelector(`[data-category-id="${categoryId}"]`)) {
                return;
            }
            
            // Create tag element
            const tag = document.createElement('span');
            tag.className = 'badge bg-primary me-2 mb-2 category-tag';
            tag.setAttribute('data-category-id', categoryId);
            tag.innerHTML = `${categoryName} <button type="button" class="btn-close btn-close-white ms-1" onclick="removeCategory(${categoryId})"></button>`;
            
            // Add to container
            selectedCategoriesContainer.appendChild(tag);
            
            // Disable option in select
            const option = categorySelect.querySelector(`option[value="${categoryId}"]`);
            if (option) {
                option.disabled = true;
            }
            
            // Update hidden input
            updateCategoriesInput();
        }
        
        // Remove category function
        function removeCategory(categoryId) {
            const tag = document.querySelector(`[data-category-id="${categoryId}"]`);
            const categorySelect = document.getElementById('category-select');
            
            if (tag) {
                tag.remove();
                
                // Enable option in select
                const option = categorySelect.querySelector(`option[value="${categoryId}"]`);
                if (option) {
                    option.disabled = false;
                }
                
                // Update hidden input
                updateCategoriesInput();
            }
        }
        
        // Update hidden inputs with selected categories
        function updateCategoriesInput() {
            const selectedCategories = document.querySelectorAll('.category-tag');
            const categoryIds = Array.from(selectedCategories).map(tag => tag.getAttribute('data-category-id'));
            
            // Remove existing hidden inputs
            const existingInputs = document.querySelectorAll('input[name="categories[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Create new hidden inputs for each category ID
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
        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
        }

        .form-switch .form-check-input:checked {
            background-color: #2dce89;
            border-color: #2dce89;
        }

        .form-switch .form-check-input:focus {
            border-color: rgba(45, 206, 137, 0.25);
            box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25);
        }

        /* Category Tags Styles */
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
