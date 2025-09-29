<!-- filepath: /d:/full_truyen/resources/views/components/categories-widget.blade.php -->
<div class="sidebar-widget categories-widget rounded-4 shadow-sm">
    <div class="widget-header mb-3">
        <h3 class="fs-5 m-0 text-dark title-dark">
            <i class="fas fa-tags text-primary me-2"></i>
            @if (!$currentCategory)
                Thể Loại
            @else
                {{ $currentCategory->name }}
            @endif
        </h3>
    </div>

    @if (!$isSearch && isset($currentCategory) && $currentCategory->description)
        <div class="category-description mb-3 px-3">
            <p class="mb-2">{{ $currentCategory->description }}</p>
            <h4 class="h6 mt-3 mb-2 fw-bold">Các thể loại khác:</h4>
        </div>
    @endif

    <div class="widget-content">
        <div class="category-grid">
            @foreach ($categories as $category)
                <a href="{{ route('categories.story.show', $category->slug) }}" 
                   class="category-item rounded-4 {{ isset($currentCategory) && $currentCategory->id == $category->id ? 'active' : '' }}">
                    <span class="category-name">{{ $category->name }}</span>
                    {{-- <span class="story-count badge bg-secondary">{{ $category->stories_count }}</span> --}}
                </a>
            @endforeach
        </div>
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
                display: block;
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
            

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .category-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
        </style>
    @endpush
@endonce
