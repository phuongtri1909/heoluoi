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
        <div class="color-text d-flex align-items-baseline bg-1 px-3 py-1">
            <h5 class="mb-0">Danh Sách Chương ({{ $chapters->count() }} chương)</h5>
        </div>

        <div class="list-chapter mt-5">
            <div id="chapters-container">
                @include('components.chapter-items', [
                    'chapters' => $chapters,
                    'story' => $story,
                    'chapterPurchaseStatus' => $chapterPurchaseStatus,
                    'sortOrder' => 'asc',
                ])
            </div>
        </div>

        <div class="d-block d-md-flex align-items-center mb-3 pagination-container">
            <x-pagination :paginator="$chapters" />
        </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chaptersContainer = document.getElementById('chapters-container');
            let currentSortOrder = 'asc'; 

            initAjaxPagination();
            function initAjaxPagination() {
                document.querySelectorAll('.pagination-container').forEach(container => {
                    container.addEventListener('click', function(e) {
                        if (e.target.closest('a.page-link')) {
                            e.preventDefault();
                            const pageUrl = e.target.closest('a.page-link').getAttribute('href');
                            if (!pageUrl) return;
                            const urlParams = new URLSearchParams(pageUrl.split('?')[1]);
                            const page = urlParams.get('page') || 1;
                            fetchChapters(currentSortOrder, page);
                            window.history.pushState({}, '', pageUrl);
                        }
                    });
                });
            }

            function fetchChapters(sortOrder, page = 1) {
                chaptersContainer.innerHTML =
                    '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Đang tải...</p></div>';
                $.ajax({
                    url: '{{ route('chapters.list', ['storyId' => $story->id]) }}',
                    type: 'GET',
                    data: {
                        sort_order: sortOrder,
                        page: page
                    },
                    success: function(response) {
                        chaptersContainer.innerHTML = response.html;
                        if (response.pagination) {
                            document.querySelectorAll('.pagination-container').forEach(container => {
                                container.outerHTML = response.pagination;
                            });
                            initAjaxPagination();
                        }
                        if (page !== 1) {
                            const chapterList = document.querySelector('.list-chapter');
                            if (chapterList) {
                                const offsetPosition = chapterList.offsetTop - 20;
                                window.scrollTo({
                                    top: offsetPosition,
                                    behavior: 'smooth'
                                });
                            }
                        }
                    },
                    error: function() {
                        chaptersContainer.innerHTML =
                            '<div class="alert alert-danger">Có lỗi xảy ra khi tải danh sách chương.</div>';
                    }
                });
            }
        });
    </script>
@endpush
