@push('styles')
    <style>
        @media (min-width: 768px) {
            .w-md-auto {
                width: auto !important;
            }
        }

        .sort-btn {
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            margin-left: 10px;
        }

        .sort-btn:hover {
            color: var(--primary-color);
            background-color: rgba(0, 0, 0, 0.05);
        }

        .sort-btn i {
            font-size: 16px;
        }

        .search-chapter-input {
            border-radius: 5px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 6px 12px;
            width: 100%;
            max-width: 300px;
        }

        .section-title-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        .title-container {
            display: flex;
            align-items: center;
        }

        @media (max-width: 767px) {
            .section-title-actions {
                flex-direction: column;
                align-items: flex-start;
                margin-top: 10px;
            }
        }

        /* Dark mode styles for all_chapter component */
        body.dark-mode .section-title {
            color: #e0e0e0 !important;
        }

        body.dark-mode .sort-btn {
            color: #e0e0e0 !important;
        }

        body.dark-mode .sort-btn:hover {
            color: var(--primary-color-3) !important;
            background-color: rgba(57, 205, 224, 0.1) !important;
        }

        body.dark-mode .form-control {
            background-color: #2d2d2d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .form-control:focus {
            background-color: #2d2d2d !important;
            border-color: var(--primary-color-3) !important;
            color: #e0e0e0 !important;
            box-shadow: 0 0 0 0.2rem rgba(57, 205, 224, 0.25) !important;
        }

        body.dark-mode .form-control::placeholder {
            color: rgba(224, 224, 224, 0.6) !important;
        }

        body.dark-mode .search-chapter-input {
            background-color: #2d2d2d !important;
            border-color: #555 !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .search-chapter-input:focus {
            border-color: var(--primary-color-3) !important;
            box-shadow: 0 0 0 0.2rem rgba(57, 205, 224, 0.25) !important;
        }

        body.dark-mode .alert-danger {
            background-color: rgba(220, 53, 69, 0.2) !important;
            border-color: #dc3545 !important;
            color: #f1aeb5 !important;
        }
    </style>
@endpush

<section id="all-chapter" class="mt-5">
    <div class="container">
        <div class="section-title d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div class="title-container">
                <i class="fa-solid fa-book-open fa-xl color-2"></i>
                <h5 class="fw-bold ms-2 d-inline mb-0">DANH SÁCH CHƯƠNG</h5>
                <a href="#" class="sort-btn text-decoration-none text-dark" id="sortBtn">
                    <i class="fas fa-sort-amount-down-alt fa-xl text-dark" id="sortIcon"></i>
                </a>
            </div>

            <div class="section-title-actions mt-2 mt-md-0">
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Tìm kiếm chương..." id="searchChapterInput">
                </div>
            </div>
        </div>

        <div class="d-block d-md-flex align-items-center mb-3 pagination-container">
            <x-pagination :paginator="$chapters" />
        </div>

        <div class="list-chapter">
            <div id="chapters-container">
                @include('components.chapter-items', [
                    'chapters' => $chapters, 
                    'story' => $story,
                    'chapterPurchaseStatus' => $chapterPurchaseStatus,
                    'sortOrder' => 'asc'
                ])
            </div>
        </div>

        <div class="d-block d-md-flex align-items-center mb-3 pagination-container">
            <x-pagination :paginator="$chapters" />
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortBtn = document.getElementById('sortBtn');
            const sortIcon = document.getElementById('sortIcon');
            const chaptersContainer = document.getElementById('chapters-container');
            const searchInput = document.getElementById('searchChapterInput');
            let currentSortOrder = 'asc'; // Mặc định sắp xếp từ cũ đến mới (Chương 1 lên đầu)

            // Xử lý sự kiện sắp xếp
            sortBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Đổi trạng thái sắp xếp
                currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';

                // Thay đổi icon
                if (currentSortOrder === 'asc') {
                    sortIcon.className = 'fas fa-sort-amount-down-alt'; // Từ cũ đến mới (1->n)
                } else {
                    sortIcon.className = 'fas fa-sort-amount-up'; // Từ mới đến cũ (n->1)
                }

                // Lấy danh sách chương với thứ tự mới
                fetchChapters(currentSortOrder);
            });

            // Tìm kiếm chương
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    fetchChapters(currentSortOrder, searchInput.value);
                }, 500);
            });

            // Xử lý phân trang AJAX
            initAjaxPagination();

            function initAjaxPagination() {
                // Áp dụng cho cả hai phần phân trang (trên và dưới)
                document.querySelectorAll('.pagination-container').forEach(container => {
                    container.addEventListener('click', function(e) {
                        // Chỉ xử lý click vào liên kết phân trang, không phải phần tử khác
                        if (e.target.closest('a.page-link')) {
                            e.preventDefault();

                            // Lấy URL từ liên kết
                            const pageUrl = e.target.closest('a.page-link').getAttribute('href');
                            if (!pageUrl) return;

                            // Lấy số trang từ URL
                            const urlParams = new URLSearchParams(pageUrl.split('?')[1]);
                            const page = urlParams.get('page') || 1;

                            // Gọi AJAX để lấy trang mới
                            fetchChapters(currentSortOrder, searchInput.value, page);

                            // Cập nhật URL mà không reload trang
                            window.history.pushState({}, '', pageUrl);
                        }
                    });
                });
            }

            // Hàm gọi AJAX để lấy danh sách chương
            function fetchChapters(sortOrder, searchTerm = '', page = 1) {
                // Hiển thị loading
                chaptersContainer.innerHTML =
                    '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Đang tải...</p></div>';

                // Gọi AJAX để lấy danh sách chương
                $.ajax({
                    url: '{{ route('chapters.list', ['storyId' => $story->id]) }}',
                    type: 'GET',
                    data: {
                        sort_order: sortOrder,
                        search: searchTerm,
                        page: page
                    },
                    success: function(response) {
                        // Cập nhật nội dung chương
                        chaptersContainer.innerHTML = response.html;

                        // Cập nhật phần phân trang (nếu có trong response)
                        if (response.pagination) {
                            document.querySelectorAll('.pagination-container').forEach(container => {
                                container.outerHTML = response.pagination;
                            });
                            // Khởi tạo lại xử lý phân trang AJAX
                            initAjaxPagination();
                        }

                        // Không cuộn lại vị trí cho sắp xếp và tìm kiếm
                        // Chỉ cuộn cho phân trang
                        if (page !== 1) {
                            // Cuộn mượt đến đầu danh sách chương nếu là phân trang
                            const chapterList = document.querySelector('.list-chapter');
                            if (chapterList) {
                                const offsetPosition = chapterList.offsetTop - 20;
                                window.scrollTo({
                                    top: offsetPosition,
                                    behavior: 'smooth'
                                });
                            }
                        }

                        // Khởi tạo tooltips cho chương VIP
                        initVipTooltips();
                    },
                    error: function() {
                        chaptersContainer.innerHTML =
                            '<div class="alert alert-danger">Có lỗi xảy ra khi tải danh sách chương.</div>';
                    }
                });
            }

            // Khởi tạo tooltips cho chương VIP
            function initVipTooltips() {
                // Khởi tạo tooltips bằng Bootstrap Native API
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].map(tooltipTriggerEl => {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Khởi tạo tooltips khi trang mới load
            initVipTooltips();
        });
    </script>
@endpush
