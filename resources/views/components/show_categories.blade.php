<!-- filepath: /d:/full_truyen/resources/views/components/categories-widget.blade.php -->
<div class="sidebar-widget categories-widget rounded-4 shadow-sm mt-4">
    <div class="widget-header mb-3">
        <h3 class="fs-5 m-0 text-dark title-dark">
            <i class="fas fa-tags text-primary me-2"></i>

            Thể Loại

        </h3>
    </div>

    <div class="widget-content">
        <div class="category-grid" id="categoryGrid">
            @foreach ($categories as $index => $category)
                <a href="{{ route('categories.story.show', $category->slug) }}"
                    class="category-item rounded-4 {{ isset($currentCategory) && $currentCategory->id == $category->id ? 'active' : '' }} {{ $index >= 8 ? 'category-hidden' : '' }}">
                    <span class="category-name">{{ ucwords($category->name) }}</span>
                </a>
            @endforeach
        </div>
        
        @if($categories->count() > 8)
            <div class="text-center mt-3">
                <button class="btn btn-outline-primary btn-sm" id="showMoreCategories">
                    <i class="fas fa-chevron-down me-1"></i>
                    Xem thêm
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2" id="showLessCategories" style="display: none;">
                    <i class="fas fa-chevron-up me-1"></i>
                    Thu lại
                </button>
            </div>
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            /* Categories Widget Styles */
            .category-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .category-item {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 8px;
                background: #f8f9fa;
                color: #333;
                text-decoration: none;
                border-radius: 4px;
                text-align: center;
                transition: all 0.3s;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .category-item:hover {
                background: #007bff;
                color: white;
                transform: translateY(-2px);
            }

            .category-tag {
                display: inline-block;
                padding: 2px 8px;
                margin-right: 4px;
                background: #f0f2f5;
                color: #666;
                border-radius: 12px;
                font-size: 0.75rem;
            }

            .category-item.active {
                background: #007bff;
                color: white;
                font-weight: 500;
            }

            .category-item.active .story-count {
                background-color: white;
                color: #007bff;
            }

            .category-hidden {
                display: none !important;
            }


            /* Responsive adjustments */
            @media (max-width: 575px) {
                .category-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (min-width: 576px) and (max-width: 767px) {
                .category-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (min-width: 768px) and (max-width: 991px) {
                .category-grid {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            @media (min-width: 992px) {
                .category-grid {
                    grid-template-columns: repeat(5, 1fr);
                }
            }

            /* Dark mode styles */
            body.dark-mode .sidebar-widget {
                background-color: #2d2d2d !important;
                border-color: #404040 !important;
            }

            body.dark-mode .widget-header {
                background-color: #404040 !important;
            }

            body.dark-mode .category-item {
                background-color: #404040 !important;
                border-color: #555 !important;
                color: #e0e0e0 !important;
            }

            body.dark-mode .category-item:hover {
                background-color: var(--primary-color-3) !important;
                color: white !important;
                transform: translateY(-2px) !important;
            }

            body.dark-mode .category-item.active {
                background-color: var(--primary-color-3) !important;
                color: white !important;
            }

            body.dark-mode .btn-outline-primary {
                border-color: var(--primary-color-3) !important;
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .btn-outline-primary:hover {
                background-color: var(--primary-color-3) !important;
                color: white !important;
            }

            body.dark-mode .btn-outline-secondary {
                border-color: #666 !important;
                color: #ccc !important;
            }

            body.dark-mode .btn-outline-secondary:hover {
                background-color: #666 !important;
                color: white !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const showMoreBtn = document.getElementById('showMoreCategories');
                const showLessBtn = document.getElementById('showLessCategories');
                const hiddenCategories = document.querySelectorAll('.category-hidden');
                
                if (showMoreBtn && showLessBtn && hiddenCategories.length > 0) {
                    showMoreBtn.addEventListener('click', function() {
                        hiddenCategories.forEach(function(category) {
                            category.classList.remove('category-hidden');
                        });
                        
                       
                        showMoreBtn.style.display = 'none';
                        showLessBtn.style.display = 'inline-block';
                    });
                   
                    showLessBtn.addEventListener('click', function() {
                        // Hide categories again
                        hiddenCategories.forEach(function(category) {
                            category.classList.add('category-hidden');
                        });
                        
                        showLessBtn.style.display = 'none';
                        showMoreBtn.style.display = 'inline-block';
                    });
                }
            });
        </script>
    @endpush
@endonce
