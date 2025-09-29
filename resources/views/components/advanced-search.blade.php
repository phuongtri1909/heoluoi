@push('styles')
    @vite('resources/assets/frontend/css/advanced-search.css')
@endpush

<div class="advanced-search-container">
    <div class="search-content">
        <form method="GET" action="{{ $searchUrl }}" id="advanced-search-form">
            @if (isset($searchType))
                <input type="hidden" name="search_type" value="{{ $searchType }}">
            @endif

            <div class="filter-group">
                <div class="filter-label">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Từ khóa</span>
                </div>
                <div class="select-wrapper">
                    <input type="text" name="query" value="{{ $currentQuery ?? request('query') }}"
                        placeholder="Nhập tên truyện, tác giả, dịch giả..." class="filter-select" />
                    <i class="fa-solid fa-chevron-down select-arrow" style="pointer-events:none;opacity:.25"></i>
                </div>
            </div>

            <!-- Danh mục truyện -->
            <div class="filter-group">
                <div class="filter-label">
                    <i class="fa-solid fa-tags"></i>
                    <span>Thể loại</span>
                </div>
                <div class="select-wrapper">
                    <select name="category" id="category" class="filter-select">
                        <option value="">Tất cả thể loại</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <!-- Bộ lọc sắp xếp -->
            <div class="filter-group">
                <div class="filter-label">
                    <i class="fa-solid fa-sort"></i>
                    <span>Sắp xếp</span>
                </div>
                <div class="select-wrapper">
                    <select name="sort" id="sort" class="filter-select">
                        <option value="">Mặc định</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="most_chapters" {{ request('sort') == 'most_chapters' ? 'selected' : '' }}>Nhiều
                            chương nhất</option>
                        <option value="least_chapters" {{ request('sort') == 'least_chapters' ? 'selected' : '' }}>Ít
                            chương nhất</option>
                        <option value="most_views" {{ request('sort') == 'most_views' ? 'selected' : '' }}>Xem nhiều
                            nhất</option>
                        <option value="highest_rating" {{ request('sort') == 'highest_rating' ? 'selected' : '' }}>Đánh
                            giá cao nhất</option>
                    </select>
                    <i class="fa-solid fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <!-- Số chương -->
            <div class="filter-group">
                <div class="filter-label">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Số chương</span>
                </div>
                <div class="select-wrapper">
                    <select name="chapters" id="chapters" class="filter-select">
                        <option value="">Tất cả</option>
                        <option value="1-10" {{ request('chapters') == '1-10' ? 'selected' : '' }}>1-10 chương
                        </option>
                        <option value="11-50" {{ request('chapters') == '11-50' ? 'selected' : '' }}>11-50 chương
                        </option>
                        <option value="51-100" {{ request('chapters') == '51-100' ? 'selected' : '' }}>51-100 chương
                        </option>
                        <option value="100+" {{ request('chapters') == '100+' ? 'selected' : '' }}>Trên 100 chương
                        </option>
                    </select>
                    <i class="fa-solid fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <!-- Tình trạng -->
            <div class="filter-group">
                <div class="filter-label">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Tình trạng</span>
                </div>
                <div class="select-wrapper">
                    <select name="status" id="status" class="filter-select">
                        <option value="">Tất cả</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang cập nhật
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn
                            thành</option>
                    </select>
                    <i class="fa-solid fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <!-- Nút tìm kiếm -->
            <div class="search-actions ms-1">
                <button type="submit" class="search-btn primary">
                    <i class="fa-solid fa-search"></i>
                    <span>Tìm kiếm</span>
                    <div class="btn-ripple"></div>
                </button>

                <a href="{{ $searchUrl }}" class="search-btn secondary">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Đặt lại</span>
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite('resources/assets/frontend/js/advanced-search.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const advancedSearchForm = document.getElementById('advanced-search-form');

            if (advancedSearchForm) {
                advancedSearchForm.addEventListener('submit', function(e) {
                    // Remove empty parameters before submitting
                    const formData = new FormData(this);
                    const cleanedParams = new URLSearchParams();

                    for (let [key, value] of formData.entries()) {
                        if (value && value.trim() !== '') {
                            cleanedParams.append(key, value);
                        }
                    }

                    // Prevent default form submission
                    e.preventDefault();

                    // Construct clean URL
                    const baseUrl = this.action;
                    const cleanUrl = cleanedParams.toString() ?
                        `${baseUrl}?${cleanedParams.toString()}` : baseUrl;

                    // Navigate to clean URL
                    window.location.href = cleanUrl;
                });
            }
        });
    </script>
@endpush
