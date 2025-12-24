@extends('admin.layouts.app')

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Quản lý cám người dùng</h6>
                        <div class="input-group w-50">
                            <form action="{{ route('admin.coins.index') }}" method="GET" class="d-flex w-100">
                                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên hoặc email..." value="{{ request('search') }}">
                                <button class="btn bg-gradient-primary mb-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Người dùng</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Email</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Số cám hiện tại</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Vai trò</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/defaults/avatar_default.jpg') }}" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ number_format($user->coins) }}</p>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin_main' ? 'danger' : ($user->role === 'admin_sub' ? 'warning' : ($user->role === 'user' ? 'success' : 'info')) }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center">
                                            <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                <a href="{{ route('admin.coins.create', $user->id) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Cộng/Trừ cám">
                                                    <i class="fas fa-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>

                                            <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-link p-1 mb-0 action-icon view-icon-primary" title="Chi tiết">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
                                            </div>
                                        
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không tìm thấy người dùng nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <x-pagination :paginator="$users" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
