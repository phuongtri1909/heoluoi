@extends('layouts.information')

@section('info_title', 'Truyện đã lưu')
@section('info_description', 'Danh sách truyện bạn đã lưu trên ' . request()->getHost())
@section('info_keyword', 'Truyện đã lưu, bookmark, theo dõi truyện, ' . request()->getHost())
@section('info_section_title', 'Truyện đã lưu')
@section('info_section_desc', 'Quản lý các truyện bạn đang theo dõi')

@section('info_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Danh sách theo dõi</h5>
        <div class="dropdown">
            <button class="btn btn-sm action-btn-secondary dropdown-toggle" type="button" id="sortDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-sort me-1"></i> Sắp xếp
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item sort-option" data-sort="newest" href="#"><i
                            class="fas fa-calendar-alt me-2"></i> Mới nhất</a></li>
                <li><a class="dropdown-item sort-option" data-sort="oldest" href="#"><i
                            class="fas fa-calendar me-2"></i> Cũ nhất</a></li>
                <li><a class="dropdown-item sort-option" data-sort="az" href="#"><i
                            class="fas fa-sort-alpha-down me-2"></i> A-Z</a></li>
            </ul>
        </div>
    </div>

    <div class="bookmarks-list">
        @if (count($bookmarks ?? []) > 0)
            @foreach ($bookmarks as $key => $bookmark)
                <div class="bookmark-item" data-date="{{ $bookmark->created_at->timestamp }}"
                    data-delay="{{ $key }}">
                    <div class="d-flex">
                        <div class="me-3">
                            <img src="{{ Storage::url($bookmark->story->cover) }}" alt="{{ $bookmark->story->title }}"
                                class="story-thumb-bookmark">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="story-title">
                                        <a href="{{ route('show.page.story', $bookmark->story->slug) }}">
                                            {{ $bookmark->story->title }}
                                        </a>
                                    </h6>
                                    <span
                                        class="status-badge {{ $bookmark->story->completed ? 'status-completed' : 'status-ongoing' }}">
                                        <i
                                            class="fas {{ $bookmark->story->completed ? 'fa-check-circle' : 'fa-spinner fa-spin' }} me-1"></i>
                                        {{ $bookmark->story->completed ? 'Hoàn thành' : 'Đang ra' }}
                                    </span>
                                </div>
                                <button class="btn btn-sm delete-btn remove-bookmark"
                                    data-story-id="{{ $bookmark->story->id }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                            <div class="row align-items-center mb-2">
                                <div class="col">
                                    <div class="story-meta">
                                        <i class="fas fa-list me-1"></i> {{ $bookmark->story->chapters_count ?? 0 }} chương
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-eye me-1"></i> {{ number_format($bookmark->story->views ?? 0) }}
                                        lượt xem
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex flex-wrap">
                                @if ($bookmark->lastChapter)
                                    <a href="{{ route('chapter', [$bookmark->story->slug, $bookmark->lastChapter->slug]) }}"
                                        class="btn btn-sm action-btn-primary story-action-btn me-2 mb-2 mb-sm-0">
                                        <i class="fas fa-book-open me-1"></i> Đọc tiếp
                                    </a>
                                    <a href="{{ route('chapter', [$bookmark->story->slug, $bookmark->lastChapter->slug]) }}"
                                        class="btn btn-sm action-btn-secondary story-action-btn me-2 mb-2 mb-sm-0">
                                        <i class="fas fa-bookmark me-1"></i> Đọc tiếp chương
                                        {{ $bookmark->lastChapter->number }}
                                    </a>
                                @else
                                    <a href="{{ route('show.page.story', $bookmark->story->slug) }}"
                                        class="btn btn-sm action-btn-primary story-action-btn me-2 mb-2 mb-sm-0">
                                        <i class="fas fa-book-open me-1"></i> Đọc
                                    </a>
                                @endif
                                @if ($bookmark->story->latestChapter)
                                    <a href="{{ route('chapter', [$bookmark->story->slug, $bookmark->story->latestChapter->slug]) }}"
                                        class="btn btn-sm action-btn-secondary story-action-btn">
                                        <i class="fas fa-arrow-right me-1"></i> Chương mới
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fa-solid fa-bookmark empty-icon"></i>
                <p class="empty-text">Bạn chưa lưu truyện nào.</p>
                <a href="{{ route('home') }}" class="btn discover-btn">Khám phá truyện ngay</a>
            </div>
        @endif
    </div>
@endsection

@push('info_scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Hàm hiển thị thông báo dạng toast
        function showToast(message, type = 'success') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }

        $(document).ready(function() {
            // Hiệu ứng hiện dần các item
            $('.bookmark-item').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(100 * index).animate({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                }, 300);
            });

            // Handle remove bookmark
            $('.remove-bookmark').on('click', function() {
                const storyId = $(this).data('story-id');
                const bookmarkItem = $(this).closest('.bookmark-item');

                Swal.fire({
                    title: 'Xóa truyện khỏi danh sách?',
                    text: 'Bạn có chắc muốn xóa truyện này khỏi danh sách theo dõi?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('user.bookmark.remove') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                story_id: storyId
                            },
                            success: function(response) {
                                if (response.success) {
                                    bookmarkItem.fadeOut(300, function() {
                                        $(this).remove();

                                        if ($('.bookmark-item').length === 0) {
                                            $('.bookmarks-list').html(`
                                                <div class="empty-state">
                                                    <i class="fa-solid fa-bookmark empty-icon"></i>
                                                    <p class="empty-text">Bạn chưa lưu truyện nào.</p>
                                                    <a href="{{ route('home') }}" class="btn discover-btn">Khám phá truyện ngay</a>
                                                </div>
                                            `);
                                        }
                                    });
                                    showToast(response.message, 'success');
                                } else {
                                    showToast(response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                console.error('Error:', xhr);
                                showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                            }
                        });
                    }
                });
            });

            // Handle sorting
            $('.sort-option').on('click', function(e) {
                e.preventDefault();
                const sortType = $(this).data('sort');
                const bookmarkItems = $('.bookmark-item').get();

                bookmarkItems.sort(function(a, b) {
                    if (sortType === 'newest') {
                        return $(b).data('date') - $(a).data('date');
                    } else if (sortType === 'oldest') {
                        return $(a).data('date') - $(b).data('date');
                    } else if (sortType === 'az') {
                        const titleA = $(a).find('h6 a').text().trim().toLowerCase();
                        const titleB = $(b).find('h6 a').text().trim().toLowerCase();
                        return titleA.localeCompare(titleB);
                    }
                });

                const bookmarksList = $('.bookmarks-list');
                $.each(bookmarkItems, function(i, item) {
                    bookmarksList.append(item);
                });

                // Update UI to show current sort
                $('#sortDropdown').html('<i class="fas fa-sort me-1"></i> ' + $(this).text().trim());
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .story-thumb-bookmark {
            width: 70px;
            height: 100%;
            object-fit: scale-down;
            transition: transform 0.3s ease;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            margin: 20px 0;
        }

        .empty-icon {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-text {
            font-size: 18px;
            color: #888;
            margin-bottom: 20px;
        }

        .discover-btn {
            background-color: var(--primary-color-3);
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .discover-btn:hover {
            background-color: var(--primary-color-4);
            color: white;
            transform: translateY(-2px);
        }

        .bookmark-item {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .bookmark-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #ddd;
        }

        .story-thumb {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            display: inline-block;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 12px;
            margin-top: 5px;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .status-ongoing {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .delete-btn {
            color: #f44336;
            background: none;
            border: none;
            padding: 5px;
            transition: all 0.2s ease;
        }

        .delete-btn:hover {
            color: #d32f2f;
            transform: scale(1.2);
        }
    </style>
@endpush
