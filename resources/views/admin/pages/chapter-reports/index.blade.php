@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Quản lý báo cáo chương</h5>
                            <p class="text-sm text-muted mb-0">Danh sách báo cáo lỗi từ người dùng</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm" id="refreshStats">
                                <i class="fas fa-sync"></i> Làm mới
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mx-3 mt-3 mb-3">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm shadow text-center border-radius-md">
                                        <i class="fas fa-list text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <div class="numbers">
                                            <p class="text-sm text-white mb-0">Tổng cộng</p>
                                            <h5 class="text-white mb-0" id="stat-total">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm shadow text-center border-radius-md">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <div class="numbers">
                                            <p class="text-sm text-white mb-0">Chờ xử lý</p>
                                            <h5 class="text-white mb-0" id="stat-pending">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm shadow text-center border-radius-md">
                                        <i class="fas fa-cog text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <div class="numbers">
                                            <p class="text-sm text-white mb-0">Đang xử lý</p>
                                            <h5 class="text-white mb-0" id="stat-processing">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm shadow text-center border-radius-md">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <div class="numbers">
                                            <p class="text-sm text-white mb-0">Đã xử lý</p>
                                            <h5 class="text-white mb-0" id="stat-resolved">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body px-3">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử
                                    lý</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử lý
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Từ ngày</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Đến ngày</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Tìm kiếm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.chapter-reports.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    @if ($reports->count() > 0)
                        <form method="POST" action="{{ route('admin.chapter-reports.bulk-update') }}" id="bulkForm"
                            class="mb-3">
                            @csrf
                            <div class="d-flex align-items-center gap-2">
                                <select name="action" id="bulkAction" class="form-select me-2"
                                    style="max-width: 200px;">
                                    <option value="">Chọn hành động</option>
                                    <option value="mark_processing">Đánh dấu đang xử lý</option>
                                    <option value="mark_resolved">Đánh dấu đã xử lý</option>
                                    <option value="mark_rejected">Đánh dấu từ chối</option>
                                    <option value="delete">Xóa báo cáo</option>
                                </select>
                                <input type="hidden" name="selected_reports" id="selectedReports">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="applyBulkAction"
                                    disabled>
                                    Áp dụng
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-2">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Người báo cáo</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Truyện & Chương</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Mô tả</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder opacity-7">Ngày tạo</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="ps-4">
                                            <input type="checkbox" class="form-check-input report-checkbox"
                                                value="{{ $report->id }}">
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $report->id }}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $report->user->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $report->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ Str::limit($report->story->title, 30) }}</h6>
                                                <p class="text-xs text-muted mb-0">Chương {{ $report->chapter->number }}:
                                                    {{ Str::limit($report->chapter->title, 25) }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0" style="max-width: 250px;">
                                                {{ Str::limit($report->description, 80) }}</p>
                                        </td>
                                        <td>
                                            {!! $report->status_badge !!}
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $report->created_at->format('d/m/Y H:i') }}</p>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.chapter-reports.show', $report->id) }}"
                                                    class="btn btn-info btn-sm" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('chapter', ['storySlug' => $report->story->slug, 'chapterSlug' => $report->chapter->slug]) }}"
                                                    class="btn btn-success btn-sm" title="Xem chương" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Không có báo cáo nào</h5>
                                                <p class="text-muted">Chưa có báo cáo lỗi nào từ người dùng.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($reports->hasPages())
                            <div class="px-4 pt-4">
                                <x-pagination :paginator="$reports" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const reportCheckboxes = document.querySelectorAll('.report-checkbox');
            const selectedReportsInput = document.getElementById('selectedReports');
            const applyBulkActionBtn = document.getElementById('applyBulkAction');
            const bulkForm = document.getElementById('bulkForm');
            const bulkActionSelect = document.getElementById('bulkAction');

            selectAllCheckbox.addEventListener('change', function() {
                reportCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionButton();
            });

            reportCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkActionButton();
                });
            });

            function updateSelectAllState() {
                const checkedCount = document.querySelectorAll('.report-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === reportCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < reportCheckboxes.length;
            }

            function updateBulkActionButton() {
                const checkedCount = document.querySelectorAll('.report-checkbox:checked').length;
                applyBulkActionBtn.disabled = !bulkActionSelect.value || checkedCount === 0;
            }

            applyBulkActionBtn.addEventListener('click', function() {
                const checkedCheckboxes = document.querySelectorAll('.report-checkbox:checked');
                const selectedIds = Array.from(checkedCheckboxes).map(cb => cb.value);

                if (selectedIds.length === 0) {
                    Swal.fire('Chưa chọn báo cáo nào', 'Vui lòng chọn ít nhất một báo cáo.', 'warning');
                    return;
                }

                if (!bulkActionSelect.value) {
                    Swal.fire('Chưa chọn hành động', 'Vui lòng chọn hành động để thực hiện.', 'warning');
                    return;
                }

                const actionText = bulkActionSelect.options[bulkActionSelect.selectedIndex].text;

                Swal.fire({
                    title: 'Xác nhận',
                    text: `Bạn có chắc muốn ${actionText.toLowerCase()} ${selectedIds.length} báo cáo?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Có',
                    cancelButtonText: 'Không'
                }).then((result) => {
                    if (result.isConfirmed) {
                        selectedReportsInput.value = JSON.stringify(selectedIds);
                        bulkForm.submit();
                    }
                });
            });

            bulkActionSelect.addEventListener('change', updateBulkActionButton);

            // Refresh stats
            document.getElementById('refreshStats').addEventListener('click', function() {
                fetch('{{ route('admin.chapter-reports.stats.api') }}')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('stat-total').textContent = data.total;
                        document.getElementById('stat-pending').textContent = data.pending;
                        document.getElementById('stat-processing').textContent = data.processing;
                        document.getElementById('stat-resolved').textContent = data.resolved;
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Load initial stats
            document.getElementById('refreshStats').click();
        });
    </script>
@endpush
