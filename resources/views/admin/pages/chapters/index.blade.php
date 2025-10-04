@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-0 mx-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">

                                Danh sách chương truyện: {{ $story->title }}

                            </h5>
                            <p class="text-sm mb-0">
                                Tổng số: {{ $totalChapters }} chương
                                ({{ $publishedChapters }} hiển thị / {{ $draftChapters }} nháp)
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between mt-3 gap-3">
                        <form method="GET" class="d-flex flex-wrap gap-2" id="filterForm">
                            <select name="status" class="form-select form-select-sm" style="min-width: 150px;">
                                <option value="">- Trạng thái -</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                            </select>

                            <div class="input-group input-group-sm" style="min-width: 200px;">
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                    placeholder="Tìm kiếm...">
                                <button class="btn bg-gradient-primary btn-sm px-3 mb-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn bg-gradient-danger btn-sm mb-0" id="bulkDeleteBtn"
                                disabled>
                                <i class="fas fa-trash me-1"></i><span class="d-none d-md-inline">Xóa đã chọn</span>
                            </button>

                            <a href="{{ route('admin.stories.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left me-1"></i><span class="d-none d-md-inline">Quay lại</span>
                            </a>
                            <a href="{{ route('admin.stories.chapters.bulk-create', $story) }}"
                                class="btn bg-gradient-success btn-sm mb-0">
                                <i class="fas fa-plus-circle me-1"></i><span class="d-none d-md-inline">Tạo nhiều chương</span>
                            </a>
                            <a href="{{ route('admin.stories.chapters.create', $story) }}"
                                class="btn bg-gradient-primary btn-sm mb-0">
                                <i class="fas fa-plus me-1"></i><span class="d-none d-md-inline">Thêm chương mới</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder text-center" style="width: 80px;">
                                        <div
                                            style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                            <input type="checkbox" id="selectAll"
                                                style="width: 20px; height: 20px; cursor: pointer; margin-bottom: 2px;">
                                            <span style="font-size: 8px;">Tất cả</span>
                                        </div>
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        STT
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">
                                        Tên chương
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Nội dung
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Số cám
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Views
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Slug
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Trạng thái
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Thời gian đếm ngược
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Ngày tạo
                                    </th>

                                    <th class="text-center text-uppercase  text-xxs font-weight-bolder ">
                                        Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chapters as $chapter)
                                    <tr>
                                        <td class="text-center" style="width: 80px;">
                                            <input type="checkbox" class="chapter-checkbox" value="{{ $chapter->id }}"
                                                data-chapter-id="{{ $chapter->id }}"
                                                style="width: 20px; height: 20px; cursor: pointer;">
                                            <br><small style="font-size: 8px;">Chọn</small>
                                        </td>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">Chương {{ $chapter->number }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $chapter->title }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-truncate" style="max-width: 200px;">
                                                {{ Str::limit($chapter->content, 50) }}
                                            </p>
                                        </td>
                                        <td>
                                            @if ($chapter->is_free)
                                                <span class="badge bg-gradient-success">Miễn phí</span>
                                            @else
                                                <span class="badge bg-gradient-danger">{{ $chapter->price ?? 0 }}
                                                    cám</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $chapter->views ?? 0 }}</p>
                                        </td>

                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $chapter->slug }}
                                            </p>
                                        </td>

                                        <td>
                                            <span
                                                class="badge badge-sm bg-gradient-{{ $chapter->status === 'published' ? 'success' : 'warning' }}">
                                                {{ $chapter->status === 'published' ? 'Hiển thị' : 'Nháp' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($chapter->scheduled_publish_at && $chapter->status === 'draft')
                                                <div class="countdown-timer" data-scheduled-time="{{ $chapter->scheduled_publish_at->toISOString() }}">
                                                    <span class="text-xs font-weight-bold text-warning">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <span class="countdown-text">Đang tính...</span>
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-xs text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $chapter->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a href="{{ route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $chapter]) }}"
                                                        class="btn btn-link p-1 mb-0 action-icon view-icon"
                                                        title="Chi tiết">
                                                        <i class="fas fa-eye text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a href="{{ route('admin.stories.chapters.edit', ['story' => $story, 'chapter' => $chapter]) }}"
                                                        class="btn btn-link p-1 mb-0 action-icon edit-icon" title="Sửa">
                                                        <i class="fas fa-pencil-alt text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2">
                                                    @include('admin.pages.components.delete-form', [
                                                        'id' => $chapter->id,
                                                        'route' => route('admin.stories.chapters.destroy', [
                                                            'story' => $story,
                                                            'chapter' => $chapter,
                                                        ]),
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">Chưa có chương nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 pt-4">
                        <x-pagination :paginator="$chapters" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles-main')
    <style>
        input[type="checkbox"] {
            width: 20px !important;
            height: 20px !important;
            cursor: pointer !important;
            margin: 0 !important;
        }

        #selectAll {
            transform: scale(1.2);
        }

        .chapter-checkbox {
            transform: scale(1.1);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .d-flex.flex-column.flex-md-row {
                flex-direction: column !important;
            }
            
            .d-flex.flex-wrap.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }
            
            .d-flex.flex-wrap.gap-2 > * {
                width: 100%;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
            
            .card-header p {
                font-size: 0.75rem;
            }
            
            /* Action buttons in table - 2 columns */
            .table .d-flex.flex-wrap {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 0.25rem;
                justify-content: center;
                max-width: 80px;
            }
            
            .table .d-flex.flex-wrap > div {
                width: 100%;
                margin-bottom: 0 !important;
                margin-right: 0 !important;
            }
            
            .table .action-icon {
                width: 32px;
                height: 32px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
            }
            
            .table .action-icon i {
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 576px) {
            .table th,
            .table td {
                padding: 0.25rem 0.125rem;
                font-size: 0.75rem;
            }
            
            .badge {
                font-size: 0.65rem;
                padding: 0.25rem 0.5rem;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .action-icon {
                padding: 0.25rem !important;
            }
            
            .text-xs {
                font-size: 0.7rem !important;
            }
            
            /* Action buttons in table - 2 columns */
            .table .d-flex.flex-wrap {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 0.125rem;
                justify-content: center;
                max-width: 70px;
            }
            
            .table .d-flex.flex-wrap > div {
                width: 100%;
                margin-bottom: 0 !important;
                margin-right: 0 !important;
            }
            
            .table .action-icon {
                width: 28px;
                height: 28px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.7rem;
            }
            
            .table .action-icon i {
                font-size: 0.65rem;
            }
        }
    </style>
@endpush

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit filters when changed
            const filterSelects = document.querySelectorAll('#filterForm select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });

            const selectAllCheckbox = document.getElementById('selectAll');
            const chapterCheckboxes = document.querySelectorAll('.chapter-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            selectAllCheckbox.addEventListener('change', function() {
                chapterCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
            // Individual checkbox change
            chapterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkDeleteButton();
                });
            });

            // Update select all checkbox state
            function updateSelectAllState() {
                const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
                selectAllCheckbox.checked = checkedBoxes.length === chapterCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < chapterCheckboxes
                    .length;
            }

            // Update bulk delete button state
            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
                bulkDeleteBtn.disabled = checkedBoxes.length === 0;
                bulkDeleteBtn.textContent = checkedBoxes.length > 0 ?
                    `Xóa đã chọn (${checkedBoxes.length})` : 'Xóa đã chọn';
            }

             // Bulk delete functionality
             bulkDeleteBtn.addEventListener('click', function() {
                 const checkedBoxes = document.querySelectorAll('.chapter-checkbox:checked');
                 if (checkedBoxes.length === 0) return;

                 const chapterIds = Array.from(checkedBoxes).map(cb => cb.value);

                 // First check which chapters can be deleted
                 fetch('{{ route('admin.stories.chapters.check-deletable', $story) }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                         },
                         body: JSON.stringify({
                             chapter_ids: chapterIds
                         })
                     })
                     .then(response => response.json())
                     .then(data => {
                         if (data.total_not_deletable > 0) {
                             let message = 'Không thể xóa một số chương:<br><br>';
                             data.not_deletable.forEach(chapter => {
                                 message +=
                                     `• Chương ${chapter.number}: ${chapter.title}<br>&nbsp;&nbsp;&nbsp;Lý do: ${chapter.reason}<br><br>`;
                             });

                             if (data.total_deletable > 0) {
                                 message +=
                                     `Bạn có muốn xóa ${data.total_deletable} chương có thể xóa được không?`;
                                 
                                 Swal.fire({
                                     title: 'Xác nhận xóa',
                                     html: message,
                                     icon: 'warning',
                                     showCancelButton: true,
                                     confirmButtonColor: '#d33',
                                     cancelButtonColor: '#3085d6',
                                     confirmButtonText: 'Xóa',
                                     cancelButtonText: 'Hủy'
                                 }).then((result) => {
                                     if (result.isConfirmed) {
                                         performBulkDelete(data.deletable.map(ch => ch.id));
                                     }
                                 });
                             } else {
                                 Swal.fire({
                                     title: 'Không thể xóa',
                                     html: message,
                                     icon: 'error',
                                     confirmButtonText: 'Đóng'
                                 });
                             }
                         } else {
                             Swal.fire({
                                 title: 'Xác nhận xóa',
                                 text: `Bạn có chắc chắn muốn xóa ${data.total_deletable} chương đã chọn?`,
                                 icon: 'warning',
                                 showCancelButton: true,
                                 confirmButtonColor: '#d33',
                                 cancelButtonColor: '#3085d6',
                                 confirmButtonText: 'Xóa',
                                 cancelButtonText: 'Hủy'
                             }).then((result) => {
                                 if (result.isConfirmed) {
                                     performBulkDelete(chapterIds);
                                 }
                             });
                         }
                     })
                     .catch(error => {
                         console.error('Error:', error);
                         Swal.fire({
                             title: 'Lỗi',
                             text: 'Có lỗi xảy ra khi kiểm tra chương có thể xóa',
                             icon: 'error',
                             confirmButtonText: 'Đóng'
                         });
                     });
             });

            function performBulkDelete(chapterIds) {
                // Show loading
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Use fetch instead of form submission
                fetch('{{ route('admin.stories.chapters.bulk-destroy', $story) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        chapter_ids: chapterIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        let message = data.message;
                        if (data.details && data.details.length > 0) {
                            message += '<br><br><strong>Chi tiết:</strong><br>' + data.details.join('<br>');
                        }
                        
                        Swal.fire({
                            title: 'Thành công',
                            html: message,
                            icon: 'success',
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            // Reload page to show updated data
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi xóa chương',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                });
            }
        });

        // Countdown timer functionality
        function updateCountdownTimers() {
            document.querySelectorAll('.countdown-timer').forEach(timer => {
                const scheduledTime = new Date(timer.dataset.scheduledTime);
                const now = new Date();
                const timeDiff = scheduledTime.getTime() - now.getTime();

                if (timeDiff > 0) {
                    const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                    let countdownText = '';
                    if (days > 0) {
                        countdownText = `${days} ngày ${hours}h ${minutes}m`;
                    } else if (hours > 0) {
                        countdownText = `${hours}h ${minutes}m ${seconds}s`;
                    } else if (minutes > 0) {
                        countdownText = `${minutes}m ${seconds}s`;
                    } else {
                        countdownText = `${seconds}s`;
                    }

                    timer.querySelector('.countdown-text').textContent = countdownText;
                } else {
                    timer.querySelector('.countdown-text').textContent = 'Đã đến giờ';
                    timer.querySelector('.text-warning').classList.remove('text-warning');
                    timer.querySelector('.text-warning').classList.add('text-success');
                }
            });
        }

        // Update countdown every second
        setInterval(updateCountdownTimers, 1000);
        updateCountdownTimers(); // Initial call
    </script>
@endpush
