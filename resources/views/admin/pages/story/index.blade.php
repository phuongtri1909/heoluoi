@extends('admin.layouts.app')

@push('styles-admin')
<style>
    /* Responsive design for story index */
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
        
        .action-icon {
            padding: 0.25rem !important;
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
        
        .text-xs {
            font-size: 0.7rem !important;
        }
        
        .img-fluid {
            width: 50px !important;
            height: 70px !important;
        }
    }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex flex-row justify-content-between">
                    <div>
                        <h5 class="mb-0">
                            Danh sách truyện
                        </h5>
                        <p class="text-sm mb-0">
                            Tổng số: {{ $totalStories }} Truyện
                            ({{ $publishedStories }} hiển thị / {{ $draftStories }} nháp / {{ $featuredStories }} đề cử)
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between mt-3 gap-3">
                    <form method="GET" class="d-flex flex-wrap gap-2" id="filterForm">
                        <!-- Status filter -->
                        <div style="min-width: 150px;">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">- Trạng thái -</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                            </select>
                        </div>

                        <!-- Featured filter -->
                        <div style="min-width: 150px;">
                            <select name="featured" class="form-select form-select-sm">
                                <option value="">- Đề cử -</option>
                                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Đề cử</option>
                                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Thường</option>
                            </select>
                        </div>

                        <!-- Category filter -->
                        <div style="min-width: 150px;">
                            <select name="category" class="form-select form-select-sm">
                                <option value="">- Thể loại -</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search input -->
                        <div class="input-group input-group-sm" style="min-width: 200px;">
                            <input type="text" class="form-control" name="search"
                                   value="{{ request('search') }}" placeholder="Tìm kiếm...">
                            <button class="btn bg-gradient-primary btn-sm px-3 mb-0" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.stories.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus me-1"></i><span class="d-none d-md-inline">Thêm truyện mới</span>
                        </a>

                        <!-- Bulk actions button -->
                        <button type="button" class="btn bg-gradient-warning btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#bulkActionsModal">
                            <i class="fas fa-star me-1"></i><span class="d-none d-md-inline">Đề cử hàng loạt</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase  text-xxs font-weight-bolder ">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="text-center text-uppercase  text-xxs font-weight-bolder ">ID</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder ">Ảnh bìa</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder  text-start">Tiêu đề</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder  text-start">Tác giả</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder ">Số chương</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder ">Giá truyện</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder ">Đề cử</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder ">Editor</th>
                                <th class="text-uppercase  text-xxs font-weight-bolder ">Trạng thái</th>
                                <th class="text-center text-uppercase  text-xxs font-weight-bolder ">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stories as $story)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="story_ids[]" value="{{ $story->id }}" class="story-checkbox">
                                </td>
                                <td class="text-center">{{ $story->id }}</td>
                                <td>
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($story->cover_thumbnail) }}" class="img-fluid" style="width: 70px; height: 100px;">
                                        @if($story->is_featured)
                                            <span class="position-absolute top-0 start-0 badge bg-gradient-warning" style="font-size: 0.65em;">
                                                #{{ $story->featured_order }}
                                            </span>
                                        @endif

                                        @if($story->completed == true)
                                            <span class="position-absolute start-0 badge bg-gradient-success" style="font-size: 0.65em; top: 25%;">
                                                Full
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-start">
                                    {{ $story->title }}
                                    @if($story->is_featured)
                                        <i class="fas fa-star text-warning ms-1" title="Truyện đề cử"></i>
                                    @endif
                                </td>
                                <td>
                                    {{ $story->author_name }}
                                </td>
                                <td>{{ $story->chapters_count }}</td>
                                <td>
                                    @if($story->has_combo)
                                        <span class="badge bg-gradient-danger">{{ $story->combo_price }} cám</span>
                                    @else
                                       -
                                    @endif
                                </td>
                                <td>
                                    @if($story->is_featured)
                                        <span class="badge bg-gradient-warning">
                                            @if($story->featured_order)
                                                #{{ $story->featured_order }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-gradient-secondary">Không</span>
                                    @endif
                                </td>
                                <td>
                                    @if($story->editor)
                                     <a href="{{ route('admin.users.show', $story->editor->id) }}" class="text-primary" title="Chi tiết">{{ $story->editor->name }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-sm bg-gradient-{{ $story->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ $story->status === 'published' ? 'Xuất bản' : 'Nháp' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <div class="d-flex flex-column align-items-center mb-2 me-2">
                                            <a href="{{ route('admin.stories.chapters.index', $story) }}" class="btn btn-link p-1 mb-0 action-icon view-icon-primary" title="Xem chương">
                                                <i class="fas fa-book-open"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex flex-column align-items-center mb-2 me-2">
                                            <a href="{{ route('admin.stories.show', $story) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex flex-column align-items-center mb-2 me-2">
                                            <a href="{{ route('admin.stories.edit', $story) }}" class="btn btn-link p-1 mb-0 action-icon edit-icon" title="Sửa">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        </div>
                                        <div class="d-flex flex-column align-items-center mb-2 me-2">
                                            <form action="{{ route('admin.stories.toggle-featured', $story) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-link p-1 mb-0 action-icon {{ $story->is_featured ? 'featured-icon' : 'non-featured-icon' }}"
                                                        title="{{ $story->is_featured ? 'Bỏ đề cử' : 'Đặt làm đề cử' }}">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="d-flex flex-column align-items-center mb-2">
                                            @include('admin.pages.components.delete-form', [
                                                'id' => $story->id,
                                                'route' => route('admin.stories.destroy', $story)
                                            ])
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">Không có truyện nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 pt-4">
                    <x-pagination :paginator="$stories" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal - NEW -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star me-2"></i>Thao tác hàng loạt với truyện đề cử
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkFeaturedForm" action="{{ route('admin.stories.bulk-featured') }}" method="POST">
                    @csrf
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Chọn các truyện trong danh sách và chọn hành động bên dưới.
                    </div>

                    <div class="form-group">
                        <label for="bulkAction">Hành động:</label>
                        <select name="action" id="bulkAction" class="form-select" required>
                            <option value="">-- Chọn hành động --</option>
                            <option value="feature">Đặt làm truyện đề cử</option>
                            <option value="unfeature">Bỏ đề cử</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Truyện đã chọn:</label>
                        <div id="selectedStoriesCount" class="badge bg-gradient-info">
                            Chưa chọn truyện nào
                        </div>
                        <div id="selectedStoriesList" class="mt-2 small text-muted">
                        </div>
                    </div>

                    <input type="hidden" name="story_ids" id="hiddenStoryIds">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="bulkFeaturedForm" class="btn btn-warning">
                    <i class="fas fa-star me-1"></i>Thực hiện
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

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

    // Bulk selection functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const storyCheckboxes = document.querySelectorAll('.story-checkbox');
    const selectedStoriesCount = document.getElementById('selectedStoriesCount');
    const selectedStoriesList = document.getElementById('selectedStoriesList');
    const hiddenStoryIds = document.getElementById('hiddenStoryIds');
    const bulkFeaturedForm = document.getElementById('bulkFeaturedForm');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        storyCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedStories();
    });

    // Individual checkbox change
    storyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateSelectedStories();
        });
    });

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
        const totalBoxes = storyCheckboxes.length;

        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    function updateSelectedStories() {
        const checkedBoxes = document.querySelectorAll('.story-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        // Update count
        if (selectedIds.length === 0) {
            selectedStoriesCount.textContent = 'Chưa chọn truyện nào';
            selectedStoriesCount.className = 'badge bg-gradient-secondary';
        } else {
            selectedStoriesCount.textContent = `${selectedIds.length} truyện đã chọn`;
            selectedStoriesCount.className = 'badge bg-gradient-info';
        }

        // Update list
        if (selectedIds.length > 0) {
            const storyTitles = Array.from(checkedBoxes).map(cb => {
                const row = cb.closest('tr');
                const titleCell = row.querySelector('td:nth-child(4)'); // Title column
                return titleCell.textContent.trim();
            });
            selectedStoriesList.innerHTML = storyTitles.map(title => `• ${title}`).join('<br>');
        } else {
            selectedStoriesList.innerHTML = '';
        }

        // Update hidden input
        hiddenStoryIds.value = selectedIds.join(',');
    }

    // Form validation
    bulkFeaturedForm.addEventListener('submit', function(e) {
        const selectedIds = hiddenStoryIds.value;
        if (!selectedIds) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một truyện.');
            return false;
        }

        const action = document.getElementById('bulkAction').value;
        if (!action) {
            e.preventDefault();
            alert('Vui lòng chọn hành động.');
            return false;
        }

        // Convert comma-separated string to array for form submission
        const storyIdsArray = selectedIds.split(',');
        hiddenStoryIds.removeAttribute('name'); // Remove to avoid duplicate

        // Add individual inputs for each story ID
        storyIdsArray.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'story_ids[]';
            input.value = id;
            bulkFeaturedForm.appendChild(input);
        });

        return true;
    });

    // Initial state
    updateSelectAllState();
    updateSelectedStories();
});
</script>
@endpush
