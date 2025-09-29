{{-- filepath: resources/views/pages/information/author/author_chapters.blade.php --}}
@extends('layouts.information')

@section('info_title', 'Quản lý chương truyện')
@section('info_description', 'Quản lý chương truyện của bạn trên ' . request()->getHost())
@section('info_keyword', 'quản lý chương, đăng truyện, tác giả, ' . request()->getHost())
@section('info_section_title', 'Quản lý chương truyện')
@section('info_section_desc', 'Truyện: ' . $story->title)

@section('info_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('user.author.stories') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="text-end">
            @if ($story->chapters->count() < 3 && $story->status == 'draft')
                <span class="badge bg-warning text-dark me-2">
                    <i class="fas fa-exclamation-triangle me-1"></i> Cần thêm {{ 3 - $story->chapters->count() }} chương nữa
                    để đủ điều kiện duyệt
                </span>
            @endif

            @if ($story->completed)
                @if ($story->hasCombo())
                    <a href="{{ route('user.author.stories.combo.edit', $story->id) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-tags me-1"></i> Chỉnh sửa combo
                    </a>
                @else
                    <a href="{{ route('user.author.stories.combo.create', $story->id) }}"
                        class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-tags me-1"></i> Tạo combo
                    </a>
                @endif
            @endif

            <div class="mb-2">
                <a href="{{ route('user.author.stories.chapters.create', $story->id) }}"
                    class="btn btn-outline-success btn-sm">
                    <i class="fas fa-plus me-1"></i> Chương
                </a>
                <a href="{{ route('user.author.stories.chapters.batch.create', $story->id) }}"
                    class="btn btn-outline-success btn-sm">
                    <i class="fas fa-plus me-1"></i> Nhiều Chương
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-1">
                    <img src="{{ Storage::url($story->cover_thumbnail) }}" alt="{{ $story->title }}" class="img-thumbnail"
                        style="width: 60px; height: 80px; object-fit: cover;">
                </div>
                <div class="col-md-7">
                    <a href="{{ route('show.page.story', $story->slug) }}"
                        class="card-title mb-1 fs-6 text-dark text-decoration-none fw-bold">{{ $story->title }}
                        @if ($story->is_18_plus)
                            <span class="badge bg-danger ms-2"><i class="fas fa-exclamation-triangle me-1"></i> 18+</span>
                        @endif
                    </a>
                    <div class="text-muted small mb-2">
                        <span class="me-3">
                            <i class="fas fa-book me-1"></i> {{ $story->chapters->count() }} chương
                        </span>
                        <span>
                            <i class="fas fa-calendar-alt me-1"></i> Cập nhật:
                            {{ $story->updated_at->format('d/m/Y H:i') }}
                        </span>
                        <span class="badge bg-warning ms-2">
                            {{ $story->getTotalChapterPriceAttribute() }} <i class="fa-solid fa-sack-dollar"></i>
                        </span>
                    </div>
                    <div>
                        @foreach ($story->categories as $category)
                            <span class="badge bg-secondary me-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="mb-2">
                        @if ($story->status == 'published')
                            <span class="badge bg-success">Đã xuất bản</span>
                        @elseif($story->status == 'draft')
                            <span class="badge bg-secondary">Bản nháp</span>
                        @elseif($story->status == 'pending')
                            <span class="badge bg-warning">Chờ duyệt</span>
                        @endif

                        @if ($story->completed)
                            <span class="badge bg-info">Đã hoàn thành</span>
                        @else
                            <span class="badge bg-secondary">Đang ra</span>
                        @endif
                    </div>

                    <div>
                        @if (!$story->completed && $story->status == 'published')
                            <button type="button" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                data-bs-target="#markCompleteModal">
                                <i class="fas fa-check-double me-1"></i> Đánh dấu hoàn thành
                            </button>
                        @endif

                        @if ($story->status == 'draft')
                            <a href="{{ route('user.author.stories.edit', $story->id) }}#review"
                                class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-check-circle me-1"></i> Yêu cầu duyệt
                            </a>
                        @elseif ($story->status == 'pending')
                            <a href="{{ route('user.author.stories.edit', $story->id) }}#review"
                                class="btn btn-sm btn-outline-secondary me-1">
                                <i class="fas fa-check-circle me-1"></i> Đang chờ duyệt
                            </a>
                        @endif

                        <div class="mb-2"></div>

                        <a href="{{ route('user.author.stories.edit', $story->id) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> Sửa thông tin
                        </a>

                        @if ($story->chapters()->where('status', 'published')->count() > 0)
                            <a href="{{ route('user.author.stories.chapters.bulk-price', $story->id) }}"
                                class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-coins me-1"></i> Cập nhật giá
                            </a>
                        @endif
                    </div>

                    <div class="mb-2"></div>

                    <div>
                        <a href="{{ route('user.author.stories.chapters.create', $story->id) }}"
                            class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i> Chương
                        </a>
                        <a href="{{ route('user.author.stories.chapters.batch.create', $story->id) }}"
                            class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i> Nhiều Chương
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($story->hasCombo())
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Combo trọn bộ</h5>
                <a href="{{ route('user.author.stories.combo.edit', $story->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <div class="combo-badge me-3">
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    <i class="fas fa-book-open me-1"></i> Trọn bộ
                                    {{ $story->chapters->where('status', 'published')->count() }} chương
                                </span>
                            </div>
                            <div class="combo-status">
                                @if (!$story->has_combo)
                                    <span class="badge bg-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-eye-slash me-1"></i> Đã ẩn
                                    </span>
                                @else
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-eye me-1"></i> Đang hiển thị
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="mb-2">
                            <span class="fs-4 fw-bold text-primary">{{ number_format($story->combo_price) }} <i
                                    class="fas fa-coins"></i></span>
                            <span
                                class="text-muted text-decoration-line-through ms-2">{{ number_format($story->total_chapter_price) }}</span>
                        </div>
                        <div class="discount-badge">
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="fas fa-percentage me-1"></i> Tiết kiệm {{ $story->discount_percentage }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($story->completed)
        <div class="alert bg-primary-bg-2 mb-4 d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-info-circle me-2"></i>
                Truyện của bạn đã hoàn thành. Bạn có thể tạo combo trọn bộ để độc giả mua với mức giá ưu đãi.
            </div>
            <div>
                <a href="{{ route('user.author.stories.combo.create', $story->id) }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-tags me-1"></i> Tạo combo ngay
                </a>
            </div>
        </div>
    @endif

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($chapters->count() > 0)
        {{-- Bulk Actions Controls --}}
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllChapters">
                        <label class="form-check-label" for="selectAllChapters">
                            <small class="text-muted">Chọn tất cả</small>
                        </label>
                    </div>

                    <div id="bulkActionsContainer" style="display: none;">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                            <i class="fas fa-trash-alt me-1"></i>Xóa đã chọn (<span id="bulkSelectedCount">0</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chapters Table --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="30" class="text-center">
                                    <input type="checkbox" id="selectAllTableChapters" class="form-check-input">
                                </th>
                                <th scope="col" width="60">Chương</th>
                                <th scope="col">Tên chương</th>
                                <th scope="col" width="100">Xu</th>
                                <th scope="col" width="120">Trạng thái</th>
                                <th scope="col" width="150">Thời gian</th>
                                <th scope="col" width="110">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chapters as $chapter)
                                <tr class="chapter-row" data-chapter-id="{{ $chapter->id }}">
                                    <td class="text-center">
                                        <input type="checkbox" name="selected_chapters[]" value="{{ $chapter->id }}"
                                               class="form-check-input chapter-checkbox">
                                    </td>
                                    <td class="fw-bold">{{ $chapter->number }}</td>
                                    <td>
                                        <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}"
                                            class="fw-bold text-decoration-none text-dark chapter-title">{{ $chapter->title }}</a>
                                        <div class="text-muted small">
                                            <i class="fas fa-eye me-1"></i> {{ number_format($chapter->views) }} lượt xem
                                        </div>
                                    </td>
                                    <td>
                                        @if ($chapter->is_free == true)
                                            <span class="badge bg-secondary">Miễn phí</span>
                                            @if ($chapter->password)
                                                <span class="badge bg-warning text-dark" title="Có mật khẩu">
                                                    <i class="fa-solid fa-lock"></i>
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $chapter->price }} <i
                                                    class="fa-solid fa-sack-dollar"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($chapter->status == 'published')
                                            <span class="badge bg-success">Đã đăng</span>
                                        @elseif($chapter->status == 'draft' && $chapter->published_at && $chapter->published_at->isFuture())
                                            <span class="badge bg-info">Đã lên lịch</span>
                                            <div class="countdown-timer small mt-1"
                                                data-target="{{ $chapter->published_at->timestamp }}"
                                                data-id="{{ $chapter->id }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <span class="countdown-text">Đang tính...</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Nháp</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="mb-1">
                                                <i class="fas fa-calendar-plus me-1 text-muted"></i>
                                                {{ $chapter->created_at->format('d/m/Y H:i') }}
                                            </div>

                                            @if ($chapter->published_at && $chapter->published_at->isFuture())
                                                <div class="text-info">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ $chapter->published_at->format('d/m/Y H:i') }}
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    {{ $chapter->updated_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('user.author.stories.chapters.edit', ['story' => $story->id, 'chapter' => $chapter->id]) }}"
                                                class="btn btn-sm btn-outline-info" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $chapter->id }}" title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Individual Delete Modal --}}
                                <div class="modal fade" id="deleteModal{{ $chapter->id }}" tabindex="-1"
                                    aria-labelledby="deleteModalLabel{{ $chapter->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $chapter->id }}">
                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                    Xác nhận xóa chương
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Cảnh báo:</strong> Hành động này không thể hoàn tác!
                                                </div>
                                                <p>Bạn có chắc chắn muốn xóa chương này?</p>
                                                <div class="border rounded p-3 bg-light">
                                                    <strong>Chương {{ $chapter->number }}:</strong> {{ $chapter->title }}
                                                    <div class="small text-muted mt-1">
                                                        <i class="fas fa-eye me-1"></i> {{ number_format($chapter->views) }} lượt xem
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i>Hủy
                                                </button>
                                                <form action="{{ route('user.author.stories.chapters.destroy', ['story' => $story->id, 'chapter' => $chapter->id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash-alt me-1"></i>Xác nhận xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $chapters->links('components.pagination') }}
        </div>

        {{-- Bulk Delete Modal --}}
        <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkDeleteModalLabel">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Xác nhận xóa nhiều chương
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Cảnh báo:</strong> Bạn đang chuẩn bị xóa <span id="deleteCount" class="fw-bold">0</span> chương.
                        </div>

                        <p>Các chương sẽ bị xóa:</p>
                        <div id="chaptersToDelete" class="border rounded p-3 bg-light max-height-200 overflow-auto">
                            <!-- Danh sách chương sẽ được populate bằng JavaScript -->
                        </div>

                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Hành động này không thể hoàn tác.</strong> Tất cả dữ liệu sẽ bị xóa vĩnh viễn.
                        </div>

                        <p class="mb-0">Bạn có chắc chắn muốn tiếp tục?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Hủy
                        </button>
                        <form id="bulkDeleteForm" action="{{ route('user.author.stories.chapters.bulk-delete', $story->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="current_page" value="{{ request('page', 1) }}">
                            <div id="selectedChaptersInputs">
                                <!-- Hidden inputs sẽ được populate bằng JavaScript -->
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i>Xác nhận xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-4">
                        <i class="fas fa-book-open text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted">Chưa có chương nào</h5>
                    <p class="text-muted">Hãy thêm chương đầu tiên cho truyện của bạn!</p>

                    @if ($story->status == 'published' || $story->status == 'draft')
                        <div class="mt-4">
                            <a href="{{ route('user.author.stories.chapters.create', $story->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-2"></i> Thêm chương đầu tiên
                            </a>
                            <a href="{{ route('user.author.stories.chapters.batch.create', $story->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i> Thêm nhiều chương
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Truyện này chưa được duyệt. Vui lòng thêm ít nhất 3 chương và gửi duyệt.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Mark Complete Modal --}}
    <div class="modal fade" id="markCompleteModal" tabindex="-1" aria-labelledby="markCompleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markCompleteModalLabel">
                        <i class="fas fa-check-double text-success me-2"></i>Đánh dấu truyện đã hoàn thành
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Sau khi đánh dấu hoàn thành, truyện sẽ được hiển thị với trạng thái "Hoàn thành".
                    </div>

                    <p>Bạn có chắc chắn muốn đánh dấu truyện này là đã hoàn thành?</p>
                    <p>Sau khi đánh dấu hoàn thành, bạn có thể:</p>
                    <ul>
                        <li>Vẫn tiếp tục thêm chương mới nếu muốn</li>
                        <li>Tạo combo trọn bộ để độc giả mua với giá ưu đãi</li>
                        <li>Truyện sẽ xuất hiện trong danh sách "Truyện đã hoàn thành"</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <form action="{{ route('user.author.stories.mark-complete', $story->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-double me-1"></i>Đánh dấu hoàn thành
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Countdown Timer Styles */
    .countdown-timer {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 4px;
        padding: 2px 6px;
        margin-top: 4px;
    }

    .countdown-timer .fas {
        font-size: 0.8rem;
    }

    .countdown-timer .text-danger {
        font-weight: bold;
        animation: pulse 1s infinite alternate;
    }

    @keyframes pulse {
        from { opacity: 1; }
        to { opacity: 0.7; }
    }

    /* Bulk Selection Styles */
    .chapter-row {
        transition: all 0.3s ease;
    }

    .chapter-row:has(.chapter-checkbox:checked) {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        border-left: 4px solid var(--bs-primary);
    }

    .chapter-checkbox {
        transform: scale(1.2);
        transition: transform 0.2s ease;
    }

    .chapter-checkbox:checked {
        transform: scale(1.3);
    }

    /* Modal Styles */
    .max-height-200 {
        max-height: 200px;
        overflow-y: auto;
    }

    #chaptersToDelete {
        font-size: 0.9rem;
    }

    #chaptersToDelete .fas {
        color: var(--bs-primary);
    }

    /* Bulk Actions Animation */
    #bulkActionsContainer {
        transition: all 0.3s ease;
    }

    #bulkActionsContainer .btn {
        animation: pulse-danger 2s infinite;
    }

    @keyframes pulse-danger {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    /* Empty State Styles */
    .empty-state {
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .empty-icon {
        opacity: 0.6;
    }

    /* Table Improvements */
    .table th {
        border-top: none;
        font-weight: 600;
        background-color: var(--bs-gray-50);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .chapter-title:hover {
        color: var(--bs-primary) !important;
    }

    /* Responsive Improvements */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Card Improvements */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        border-bottom: 1px solid rgba(var(--bs-primary-rgb), 0.2);
    }

    /* Badge Improvements */
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }

    /* Alert Improvements */
    .alert {
        border: none;
        border-radius: 0.5rem;
    }

    .alert-dismissible .btn-close {
        padding: 0.75rem 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown Timer Functionality
    const countdownTimers = document.querySelectorAll('.countdown-timer');
    if (countdownTimers.length > 0) {
        const timers = [];

        countdownTimers.forEach(timer => {
            const targetTimestamp = parseInt(timer.getAttribute('data-target'));
            const chapterId = timer.getAttribute('data-id');
            const countdownText = timer.querySelector('.countdown-text');

            timers.push({
                element: countdownText,
                targetTime: targetTimestamp,
                chapterId: chapterId
            });

            updateCountdown(countdownText, targetTimestamp, chapterId);
        });

        setInterval(() => {
            timers.forEach(timer => {
                updateCountdown(timer.element, timer.targetTime, timer.chapterId);
            });
        }, 1000);
    }

    function updateCountdown(element, targetTime, chapterId) {
        const now = Math.floor(Date.now() / 1000);
        const timeLeft = targetTime - now;

        if (timeLeft <= 0) {
            element.innerHTML = '<span class="text-success">Đang xuất bản...</span>';
            return;
        }

        const days = Math.floor(timeLeft / 86400);
        const hours = Math.floor((timeLeft % 86400) / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;

        let displayText = '';
        if (days > 0) {
            displayText += `${days}d `;
        }
        displayText += `${padZero(hours)}:${padZero(minutes)}:${padZero(seconds)}`;

        element.textContent = displayText;

        // Color coding based on time left
        if (timeLeft < 300) { // Under 5 minutes
            element.classList.remove('text-info', 'text-warning');
            element.classList.add('text-danger');
        } else if (timeLeft < 3600) { // Under 1 hour
            element.classList.remove('text-info', 'text-danger');
            element.classList.add('text-warning');
        } else {
            element.classList.remove('text-warning', 'text-danger');
            element.classList.add('text-info');
        }
    }

    function padZero(num) {
        return num < 10 ? '0' + num : num;
    }

    // Bulk Selection Functionality
    const selectAllCheckbox = document.getElementById('selectAllTableChapters');
    const selectAllTopCheckbox = document.getElementById('selectAllChapters');
    const chapterCheckboxes = document.querySelectorAll('.chapter-checkbox');
    const bulkActionsContainer = document.getElementById('bulkActionsContainer');
    const bulkSelectedCount = document.getElementById('bulkSelectedCount');
    const bulkDeleteModal = document.getElementById('bulkDeleteModal');

    if (selectAllCheckbox && chapterCheckboxes.length > 0) {
        // Update bulk actions visibility and count
        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.chapter-checkbox:checked');
            const count = selectedCheckboxes.length;

            if (count > 0) {
                bulkActionsContainer.style.display = 'block';
                bulkSelectedCount.textContent = count;
            } else {
                bulkActionsContainer.style.display = 'none';
            }

            // Update select all checkbox states
            updateSelectAllState();
        }

        function updateSelectAllState() {
            const selectedCheckboxes = document.querySelectorAll('.chapter-checkbox:checked');
            const count = selectedCheckboxes.length;
            const totalCount = chapterCheckboxes.length;

            [selectAllCheckbox, selectAllTopCheckbox].forEach(checkbox => {
                if (checkbox) {
                    if (count === 0) {
                        checkbox.indeterminate = false;
                        checkbox.checked = false;
                    } else if (count === totalCount) {
                        checkbox.indeterminate = false;
                        checkbox.checked = true;
                    } else {
                        checkbox.indeterminate = true;
                        checkbox.checked = false;
                    }
                }
            });
        }

        // Select all functionality
        [selectAllCheckbox, selectAllTopCheckbox].forEach(checkbox => {
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    chapterCheckboxes.forEach(chapterCheckbox => {
                        chapterCheckbox.checked = this.checked;
                    });
                    updateBulkActions();
                });
            }
        });

        // Individual checkbox change
        chapterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        // Bulk delete modal preparation
        if (bulkDeleteModal) {
            bulkDeleteModal.addEventListener('show.bs.modal', function() {
                const selectedCheckboxes = document.querySelectorAll('.chapter-checkbox:checked');
                const chaptersToDelete = document.getElementById('chaptersToDelete');
                const deleteCount = document.getElementById('deleteCount');
                const selectedChaptersInputs = document.getElementById('selectedChaptersInputs');

                // Clear previous content
                chaptersToDelete.innerHTML = '';
                selectedChaptersInputs.innerHTML = '';

                // Update count
                deleteCount.textContent = selectedCheckboxes.length;

                // Create list of chapters to delete
                selectedCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const chapterNumber = row.children[1].textContent.trim();
                    const chapterTitle = row.querySelector('.chapter-title').textContent.trim();

                    // Add to display list
                    const listItem = document.createElement('div');
                    listItem.className = 'mb-2 p-2 border rounded bg-white';
                    listItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book text-primary me-2"></i>
                            <div>
                                <strong>Chương ${chapterNumber}:</strong> ${chapterTitle}
                            </div>
                        </div>
                    `;
                    chaptersToDelete.appendChild(listItem);

                    // Add hidden input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'selected_chapters[]';
                    hiddenInput.value = checkbox.value;
                    selectedChaptersInputs.appendChild(hiddenInput);
                });
            });
        }

        // Initialize
        updateBulkActions();
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
@endpush
