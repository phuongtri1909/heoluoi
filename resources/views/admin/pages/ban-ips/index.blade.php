@extends('admin.layouts.app')

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .w-md-auto {
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Quản lý IP bị cấm</h5>
                            <p class="text-sm mb-0">
                                Tổng số: {{ $stats['total'] }} IP bị cấm
                                ({{ $stats['with_user'] }} có user / {{ $stats['without_user'] }} không có user)
                            </p>
                        </div>
                        <a href="{{ route('admin.ban-ips.create') }}" class="btn bg-gradient-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Thêm IP cấm
                        </a>
                    </div>
                    
                    <form action="{{ route('admin.ban-ips.index') }}" method="GET" class="mt-3 d-flex flex-column flex-md-row gap-2">
                        <div class="d-flex flex-column flex-md-row gap-2 mb-2 mb-md-0">
                            <input type="text" name="ip" class="form-control form-control-sm w-100 w-md-auto"
                                   placeholder="Địa chỉ IP" value="{{ request('ip') }}">

                            <input type="text" name="user" class="form-control form-control-sm w-100 w-md-auto"
                                   placeholder="Tên hoặc email user" value="{{ request('user') }}">
                        </div>

                        <div class="input-group input-group-sm">
                            <button class="btn bg-gradient-primary btn-sm px-2 mb-0" type="submit">
                                <i class="fa-solid fa-search"></i>
                            </button>
                            <a href="{{ route('admin.ban-ips.index') }}" class="btn btn-secondary btn-sm px-2 mb-0">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">IP Address</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">User</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Lý do</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Người cấm</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Ngày cấm</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($banIps as $banIp)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $banIp->ip_address }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($banIp->user)
                                            <a href="{{ route('admin.users.show', $banIp->user) }}" class="text-primary">
                                                <h6 class="mb-0 text-sm">{{ $banIp->user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $banIp->user->email }}</p>
                                            </a>
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $banIp->reason ? Str::limit($banIp->reason, 30) : 'Không có' }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $banIp->bannedBy->name ?? 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $banIp->banned_at ? $banIp->banned_at->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                <a href="{{ route('admin.ban-ips.edit', $banIp) }}" 
                                                   class="btn btn-link p-1 mb-0 action-icon edit-icon" title="Sửa">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-column align-items-center mb-2">
                                                <button type="button" class="btn btn-link p-1 mb-0 action-icon delete-icon" 
                                                        title="Xóa" onclick="deleteBanIp({{ $banIp->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-ban fa-3x mb-3"></i>
                                            <p>Chưa có IP nào bị cấm</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <x-pagination :paginator="$banIps" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn gỡ cấm IP này không?
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
@endsection

@push('scripts-admin')
<script>
    function deleteBanIp(banIpId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/admin/ban-ips/${banIpId}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush
