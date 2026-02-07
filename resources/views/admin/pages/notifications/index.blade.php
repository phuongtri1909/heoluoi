@extends('admin.layouts.app')

@section('title', 'Gửi thông báo')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex flex-row justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Thông báo đã gửi</h5>
                        <p class="text-sm mb-0">Danh sách thông báo hàng loạt / gửi riêng</p>
                    </div>
                    <a href="{{ route('admin.notifications.create') }}" class="btn bg-gradient-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tạo thông báo
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">Tất cả</a>
                    <a href="{{ route('admin.notifications.index', ['type' => 'broadcast']) }}" class="btn btn-sm {{ request('type') === 'broadcast' ? 'btn-primary' : 'btn-outline-primary' }}">Gửi tất cả user</a>
                    <a href="{{ route('admin.notifications.index', ['type' => 'targeted']) }}" class="btn btn-sm {{ request('type') === 'targeted' ? 'btn-primary' : 'btn-outline-primary' }}">Gửi riêng</a>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Tiêu đề</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Loại</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Người tạo</th>
                                <th class="text-uppercase text-xxs font-weight-bolder">Ngày tạo</th>
                                <th class="text-center text-uppercase text-xxs font-weight-bolder">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($notifications as $n)
                            <tr>
                                <td>{{ $n->id }}</td>
                                <td>
                                    <span class="text-dark font-weight-bold">{{ Str::limit($n->title, 50) }}</span>
                                </td>
                                <td>
                                    @if($n->is_broadcast)
                                        <span class="badge bg-info">Gửi tất cả user</span>
                                    @else
                                        <span class="badge bg-secondary">Gửi riêng</span>
                                    @endif
                                </td>
                                <td>{{ $n->createdBy?->name ?? '-' }}</td>
                                <td>{{ $n->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center d-flex justify-content-center">
                                    <a href="{{ route('admin.notifications.edit', $n) }}" class="action-icon edit-icon me-2" title="Sửa"><i class="fas fa-pencil-alt text-white"></i></a>
                                    
                                    @include('admin.pages.components.delete-form', [
                                        'id' => $n->id,
                                        'route' => route('admin.notifications.destroy', $n)
                                    ])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Chưa có thông báo nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 pt-4">
                    <x-pagination :paginator="$notifications" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
