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
                                        @php
                                            $storyType = array_key_exists('story_type', $story->getAttributes()) ? ($story->story_type ?? '') : '';
                                        @endphp
                                        <option value="original" {{ old('story_type', $storyType) == 'original' ? 'selected' : '' }}>Sáng tác</option>
                                        <option value="translated" {{ old('story_type', $storyType) == 'translated' ? 'selected' : '' }}>Dịch</option>
                                        <option value="rewritten" {{ old('story_type', $storyType) == 'rewritten' ? 'selected' : '' }}>Chuyển ngữ</option>
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
                                        <label class="mb-0 me-3" for="has_combo">Bán combo (tất cả chương)</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="has_combo" class="form-check-input" id="has_combo"
                                                role="switch" {{ $story->has_combo ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div id="combo-pricing-container" class="mt-3" style="{{ $story->has_combo ? '' : 'display: none;' }}">
                                    <div class="form-group">
                                        <label for="combo_price">Giá combo (cám) <span class="text-danger">*</span></label>
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
                                        <p class="mb-1">- Tổng giá nếu mua lẻ: <span id="total-regular-price">{{ $totalRegularPrice }}</span> cám</p>
                                        <p class="mb-1">- Tiết kiệm: <span id="savings">{{ $savings }}</span> cám (<span id="savings-percent">{{ $savingsPercent }}</span>%)</p>
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

                    {{-- Section để chỉnh sửa views của chapters --}}
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quản lý lượt xem các chương</h5>
                                    <p class="text-sm mb-0 text-muted">Tổng lượt xem hiện tại: <strong id="total-story-views">{{ number_format($story->total_views ?? 0) }}</strong></p>
                                </div>
                                <div class="card-body">
                                    {{-- Bulk update views --}}
                                    <div class="alert alert-info mb-3">
                                        <h6 class="mb-2">Điều chỉnh views hàng loạt:</h6>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <select class="form-select form-select-sm" id="bulk-views-action">
                                                    <option value="set">Set tất cả về</option>
                                                    <option value="set_total">Set tổng views về</option>
                                                    <option value="add">Tăng thêm</option>
                                                    <option value="subtract">Giảm đi</option>
                                                    <option value="multiply">Nhân với</option>
                                                    <option value="divide">Chia cho</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control form-control-sm" id="bulk-views-value" placeholder="Nhập số" min="0" step="0.01">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" class="btn btn-sm btn-warning" id="bulk-update-views-btn" data-story-id="{{ $story->id }}">
                                                    <i class="fa-solid fa-bolt"></i> Áp dụng cho tất cả
                                                </button>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-white d-block mt-1">
                                                    <em id="bulk-views-hint">Ví dụ: "Set tất cả về 1000" sẽ set mỗi chương = 1000</em>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%">STT</th>
                                                    <th width="40%">Tên chương</th>
                                                    <th width="20%">Lượt xem hiện tại</th>
                                                    <th width="25%">Điều chỉnh lượt xem</th>
                                                    <th width="10%">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($chapters as $chapter)
                                                <tr data-chapter-id="{{ $chapter->id }}">
                                                    <td>{{ $chapter->number }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $chapter]) }}" target="_blank">
                                                            {{ $chapter->title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="current-views-{{ $chapter->id }}">{{ number_format($chapter->views) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" 
                                                                   class="form-control chapter-views-input" 
                                                                   data-chapter-id="{{ $chapter->id }}"
                                                                   value="{{ $chapter->views }}" 
                                                                   min="0"
                                                                   placeholder="Nhập số lượt xem">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm bg-gradient-primary update-chapter-views-btn" 
                                                                data-chapter-id="{{ $chapter->id }}"
                                                                data-story-id="{{ $story->id }}">
                                                            Lưu
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

        // Update hint text based on action
        const bulkViewsAction = document.getElementById('bulk-views-action');
        const bulkViewsHint = document.getElementById('bulk-views-hint');
        if (bulkViewsAction && bulkViewsHint) {
            bulkViewsAction.addEventListener('change', function() {
                const hints = {
                    'set': 'Ví dụ: "Set tất cả về 1000" sẽ set mỗi chương = 1000',
                    'set_total': 'Ví dụ: "Set tổng views về 50000" sẽ phân bổ tổng 50000 views cho tất cả chương',
                    'add': 'Ví dụ: "Tăng thêm 1000" sẽ cộng 1000 views vào mỗi chương',
                    'subtract': 'Ví dụ: "Giảm đi 1000" sẽ trừ 1000 views từ mỗi chương',
                    'multiply': 'Ví dụ: "Nhân với 2" sẽ nhân đôi views của mỗi chương',
                    'divide': 'Ví dụ: "Chia cho 2" sẽ chia đôi views của mỗi chương'
                };
                bulkViewsHint.textContent = hints[this.value] || hints['set'];
            });
        }

        // Bulk update chapter views
        const bulkUpdateViewsBtn = document.getElementById('bulk-update-views-btn');
        if (bulkUpdateViewsBtn) {
            bulkUpdateViewsBtn.addEventListener('click', function() {
                const storyId = this.getAttribute('data-story-id');
                const action = document.getElementById('bulk-views-action').value;
                const value = parseFloat(document.getElementById('bulk-views-value').value);

                if (isNaN(value) || value < 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cảnh báo',
                        text: 'Vui lòng nhập giá trị hợp lệ (số >= 0)'
                    });
                    return;
                }

                const actionNames = {
                    'set': 'Set tất cả về',
                    'set_total': 'Set tổng views về',
                    'add': 'Tăng thêm',
                    'subtract': 'Giảm đi',
                    'multiply': 'Nhân với',
                    'divide': 'Chia cho'
                };

                Swal.fire({
                    icon: 'question',
                    title: 'Xác nhận',
                    text: `Bạn có chắc chắn muốn ${actionNames[action]} ${value} cho tất cả các chương?`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Không tìm thấy CSRF token'
                        });
                        return;
                    }

                    // Disable button
                    bulkUpdateViewsBtn.disabled = true;
                    bulkUpdateViewsBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

                    fetch(`/admin/stories/${storyId}/chapters/bulk-update-views`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            action: action,
                            value: value
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            // Update all chapter views in the table
                            data.chapters.forEach(chapter => {
                                const currentViewsSpan = document.querySelector(`.current-views-${chapter.id}`);
                                const input = document.querySelector(`.chapter-views-input[data-chapter-id="${chapter.id}"]`);
                                if (currentViewsSpan) {
                                    currentViewsSpan.textContent = number_format(chapter.views);
                                }
                                if (input) {
                                    input.value = chapter.views;
                                }
                            });

                            // Update total story views
                            document.getElementById('total-story-views').textContent = number_format(data.total_views);

                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: data.message || 'Đã cập nhật lượt xem thành công',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message || 'Có lỗi xảy ra'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'Có lỗi xảy ra khi cập nhật';
                        if (error.message) {
                            errorMessage = error.message;
                        } else if (typeof error === 'object' && error.message) {
                            errorMessage = error.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: errorMessage
                        });
                    })
                    .finally(() => {
                        bulkUpdateViewsBtn.disabled = false;
                        bulkUpdateViewsBtn.innerHTML = '<i class="fa-solid fa-bolt"></i> Áp dụng cho tất cả';
                    });
                });
            });
        }

        // Update individual chapter views
        document.querySelectorAll('.update-chapter-views-btn').forEach(button => {
            button.addEventListener('click', function() {
                const chapterId = this.getAttribute('data-chapter-id');
                const storyId = this.getAttribute('data-story-id');
                const input = document.querySelector(`.chapter-views-input[data-chapter-id="${chapterId}"]`);
                const views = parseInt(input.value);
                
                if (isNaN(views) || views < 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cảnh báo',
                        text: 'Vui lòng nhập số lượt xem hợp lệ (số nguyên >= 0)'
                    });
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không tìm thấy CSRF token'
                    });
                    return;
                }

                // Disable button
                button.disabled = true;
                button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

                fetch(`/admin/stories/${storyId}/chapters/${chapterId}/update-views`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        views: views
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Update current views display
                        document.querySelector(`.current-views-${chapterId}`).textContent = number_format(data.views);
                        
                        // Update total story views
                        document.getElementById('total-story-views').textContent = number_format(data.total_views);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message || 'Đã cập nhật lượt xem thành công',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Có lỗi xảy ra'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = 'Có lỗi xảy ra khi lưu';
                    if (error.message) {
                        errorMessage = error.message;
                    } else if (typeof error === 'object' && error.message) {
                        errorMessage = error.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: errorMessage
                    });
                })
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-solid fa-save"></i> Lưu';
                });
            });
        });

        // Helper function to format numbers
        function number_format(number) {
            return new Intl.NumberFormat('vi-VN').format(number);
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
