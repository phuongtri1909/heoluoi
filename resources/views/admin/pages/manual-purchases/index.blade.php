@extends('admin.layouts.app')

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Cộng/Trừ quyền truy cập cho người dùng</h6>
                        <a href="{{ route('admin.manual-purchases.create') }}" class="btn bg-gradient-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Thêm quyền truy cập
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="mb-4">
                        <form method="GET" class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="search" class="form-control-label">Tìm kiếm</label>
                                    <input type="text" class="form-control" 
                                           name="search" placeholder="Nhập email, name..." 
                                           value="{{ $search }}">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </button>
                                <a href="{{ route('admin.manual-purchases.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                </a>
                            </div>
                        </form>
                    </div>

                    @if($storyPurchases->count() > 0 || $chapterPurchases->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Người dùng</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loại</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nội dung</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reference ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Admin</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày tạo</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Story Purchases -->
                                    @foreach($storyPurchases as $purchase)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $purchase->user->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $purchase->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">Mua truyện</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $purchase->story->title }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Slug: {{ $purchase->story->slug }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $purchase->reference_id ?? '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                @if($purchase->admin)
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $purchase->admin->name}}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $purchase->admin->email }}</p>
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning">Không tồn tại</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $purchase->created_at->format('d/m/Y') }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ $purchase->created_at->format('H:i:s') }}</p>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                @if($purchase->notes)
                                                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" 
                                                            data-bs-toggle="modal" data-bs-target="#notesModal{{ $purchase->id }}">
                                                        <i class="fas fa-file-text"></i>
                                                    </button>
                                                @endif
                                                
                                                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" 
                                                            onclick="deleteStoryPurchase({{ $purchase->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                            </td>
                                        </tr>

                                        <!-- Notes Modal -->
                                        @if($purchase->notes)
                                            <div class="modal fade" id="notesModal{{ $purchase->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Ghi chú</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="mb-0">{{ $purchase->notes }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    
                                    <!-- Chapter Purchases -->
                                    @foreach($chapterPurchases as $purchase)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $purchase->user->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $purchase->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Mua chương</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Chương {{ $purchase->chapter->number ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $purchase->chapter->title ?? 'N/A' }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ $purchase->chapter->story->title ?? 'N/A' }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $purchase->reference_id ?? '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                @if($purchase->admin)
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $purchase->admin->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $purchase->admin->email }}</p>
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning">Không tồn tại</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $purchase->created_at->format('d/m/Y') }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ $purchase->created_at->format('H:i:s') }}</p>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                @if($purchase->notes)
                                                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" 
                                                            data-bs-toggle="modal" data-bs-target="#notesModalChapter{{ $purchase->id }}">
                                                        <i class="fas fa-file-text"></i>
                                                    </button>
                                                @endif
                                                
                                                <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" 
                                                        onclick="deleteChapterPurchase({{ $purchase->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Notes Modal -->
                                        @if($purchase->notes)
                                            <div class="modal fade" id="notesModalChapter{{ $purchase->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Ghi chú</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="mb-0">{{ $purchase->notes }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            @if($storyPurchases->hasPages())
                                {{ $storyPurchases->links('pagination::bootstrap-4') }}
                            @elseif($chapterPurchases->hasPages())
                                {{ $chapterPurchases->links('pagination::bootstrap-4') }}
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-shopping-cart fa-3x"></i>
                            </div>
                            <h6>Chưa có quyền truy cập thủ công nào</h6>
                            <p class="text-muted">Hãy thêm quyền truy cập cho người dùng từ web cũ</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Bạn có chắc chắn muốn xóa quyền truy cập này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteStoryPurchase(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/admin/manual-purchases/story/' + id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteChapterPurchase(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/admin/manual-purchases/chapter/' + id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
