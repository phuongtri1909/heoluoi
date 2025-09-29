@extends('layouts.information')

@section('info_title', 'Lịch sử đọc truyện')
@section('info_description', 'Lịch sử đọc truyện của bạn trên ' . request()->getHost())
@section('info_keyword', 'Lịch sử đọc truyện, ' . request()->getHost())
@section('info_section_title', 'Lịch sử đọc truyện')
@section('info_section_desc', 'Xem lại các truyện bạn đã đọc gần đây')

@section('info_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Đã đọc gần đây</h5>
        <button class="btn btn-sm delete-btn" id="openConfirmModalBtn">
            <i class="fa-solid fa-trash-can me-1"></i> Xóa lịch sử
        </button>
    </div>

    <div class="reading-history-list">
        @if (count($readingHistory ?? []) > 0)
            @foreach ($readingHistory as $key => $item)
                <div class="history-item" data-delay="{{ $key }}">
                    <div class="d-flex">
                        <div class="story-thumb-container me-3">
                            <img src="{{ Storage::url($item->story->cover) }}" alt="{{ $item->story->title }}"
                                class="story-thumb">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-start mb-2">
                                <h6 class="story-title mb-1 mb-md-0">
                                    <a href="{{ route('show.page.story', $item->story->slug) }}">
                                        {{ $item->story->title }}
                                    </a>
                                </h6>
                                <small
                                    class="text-muted ms-0 ms-md-2">{{ Carbon\Carbon::parse($item->updated_at)->diffForHumans() }}</small>
                            </div>

                            <div class="story-chapter mb-0">
                                <a href="{{ route('chapter', [$item->story->slug, $item->chapter->slug]) }}">
                                    <i class="fas fa-bookmark me-1 text-muted"></i>
                                    Chương {{ $item->chapter->number }}: {{ $item->chapter->title }}
                                </a>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-8 mb-2 mb-md-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="story-meta">
                                            <i class="fas fa-tasks"></i>
                                            {{ $item->chapter->number }}/{{ $item->story->chapters_count }} chương
                                        </small>
                                        <small class="story-meta">
                                            @if ($item->story->chapters_count > 0)
                                                {{ round(($item->chapter->number / $item->story->chapters_count) * 100) }}%
                                            @else
                                                0%
                                            @endif
                                        </small>
                                    </div>
                                    <div class="story-progress">

                                        <div class="story-progress-bar"
                                            style="width: {{ $item->story->chapters_count > 0 ? ($item->chapter->number / $item->story->chapters_count) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="d-flex flex-column align-items-end">
                                        {{-- <div class="reading-progress m-0">
                                            <div class="progress mt-1" style="height: 5px; width: 100px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $item->progress_percent }}%"
                                                    aria-valuenow="{{ $item->progress_percent }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">Đã đọc: {{ round($item->progress_percent) }}%</small>
                                        </div> --}}
                                        <a href="{{ route('chapter', [$item->story->slug, $item->chapter->slug]) }}"
                                            class="btn btn-sm action-btn-primary story-action-btn">
                                            <i class="fas fa-book-open me-1"></i> Đọc tiếp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fa-solid fa-book-open empty-icon"></i>
                <p class="empty-text">Bạn chưa đọc truyện nào.</p>
                <a href="{{ route('home') }}" class="btn discover-btn">Khám phá truyện ngay</a>
            </div>
        @endif
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa toàn bộ lịch sử đọc truyện?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmClearHistoryBtn">Xóa lịch sử</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            // Hiệu ứng hiện dần các item
            $('.history-item').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(100 * index).animate({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                }, 300);
            });

            // Mở modal xác nhận
            $('#openConfirmModalBtn').on('click', function() {
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                confirmModal.show();
            });

            // Xử lý xóa lịch sử khi nhấn nút xác nhận trong modal
            $('#confirmClearHistoryBtn').on('click', function() {
                $.ajax({
                    url: "{{ route('user.reading.history.clear') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Đóng modal
                            const modalEl = document.getElementById('confirmModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            modalInstance.hide();

                            // Đảm bảo backdrop được xóa hoàn toàn
                            $(modalEl).on('hidden.bs.modal', function() {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open').css('padding-right',
                                    '');
                            });

                            // Cập nhật giao diện
                            $('.reading-history-list').fadeOut(300, function() {
                                $(this).html(`
                                    <div class="empty-state">
                                        <i class="fa-solid fa-book-open empty-icon"></i>
                                        <p class="empty-text">Bạn chưa đọc truyện nào.</p>
                                        <a href="{{ route('home') }}" class="btn discover-btn">Khám phá truyện ngay</a>
                                    </div>
                                `).fadeIn(300);
                            });
                            showToast(response.message, 'success');
                        } else {
                            showToast('Có lỗi xảy ra khi xóa lịch sử', 'error');
                        }
                    },
                    error: function() {
                        showToast('Có lỗi xảy ra khi xóa lịch sử', 'error');
                    }
                });
            });

            // Đảm bảo backdrop và trạng thái modal được xóa khi đóng modal
            $('#confirmModal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Định dạng cho item lịch sử đọc */
        .history-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .history-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Đảm bảo container ảnh bìa có kích thước cố định */
        .story-thumb-container {
            width: 80px;
            min-width: 80px;
            height: 120px;
            overflow: hidden;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Đảm bảo ảnh bìa lấp đầy container và không bị biến dạng */
        .story-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Định dạng tiêu đề truyện */
        .story-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Giới hạn tối đa 2 dòng */
            -webkit-box-orient: vertical;
            max-height: 2.6em;
        }

        .story-title a {
            color: #333;
            text-decoration: none;
        }

        /* Định dạng tên chương */
        .story-chapter {
            margin: 8px 0;
            font-size: 14px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            /* Giới hạn tối đa 1 dòng */
            -webkit-box-orient: vertical;
        }

        .story-chapter a {
            color: #666;
            text-decoration: none;
        }

        .story-chapter a:hover {
            color: #007bff;
        }

        /* Thanh tiến độ */
        .story-progress {
            height: 5px;
            background-color: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .story-progress-bar {
            height: 100%;
            background-color: #28a745;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Các thông tin meta */
        .story-meta {
            font-size: 12px;
            color: #888;
            margin-right: 10px;
        }

        /* Nút thao tác */
        .story-action-btn {
            font-size: 13px;
            padding: 4px 10px;
            margin-top: 5px;
        }

        /* Trạng thái rỗng */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-text {
            color: #888;
            margin-bottom: 20px;
        }

        .discover-btn {
            background-color: #4F46E5;
            color: white;
        }

        /* Responsive cho màn hình nhỏ */
        @media (max-width: 767px) {
            .story-thumb-container {
                width: 60px;
                min-width: 60px;
                height: 90px;
            }

            .reading-progress {
                margin-top: 10px;
            }

            .story-action-btn {
                margin-bottom: 5px;
            }
        }
    </style>
@endpush
