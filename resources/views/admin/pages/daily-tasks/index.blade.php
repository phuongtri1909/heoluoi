@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Quản lý nhiệm vụ hàng ngày</h5>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.daily-tasks.statistics') }}" class="btn bg-gradient-info btn-sm mb-0">
                                <i class="fas fa-chart-bar"></i> Thống kê
                            </a>
                            <a href="{{ route('admin.daily-tasks.user-progress') }}" class="btn bg-gradient-warning btn-sm mb-0">
                                <i class="fas fa-users"></i> Tiến độ user
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Tên nhiệm vụ</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Loại</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Thưởng cám</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Max/ngày</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Trạng thái</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Thứ tự</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $task->id }}</p>
                                        </td>
                                        <td>
                                            <div>
                                                <p class="text-xs font-weight-bold mb-0">{{ $task->name }}</p>
                                                @if($task->description)
                                                    <p class="text-xs text-muted mb-0">{{ Str::limit($task->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-{{ 
                                                $task->type === 'login' ? 'primary' : 
                                                ($task->type === 'comment' ? 'success' : 
                                                ($task->type === 'bookmark' ? 'warning' : 'danger')) 
                                            }}">
                                                @switch($task->type)
                                                    @case('login') Đăng nhập @break
                                                    @case('comment') Bình luận @break
                                                    @case('bookmark') Theo dõi @break
                                                    @case('share') Chia sẻ @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-warning">
                                                <i class="fas fa-coins me-1"></i>{{ $task->coin_reward }} cám
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $task->max_per_day }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($task->active)
                                                <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Tạm dừng</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold">{{ $task->order }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a href="{{ route('admin.daily-tasks.show', $task) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a href="{{ route('admin.daily-tasks.edit', $task) }}" class="btn btn-link p-1 mb-0 action-icon edit-icon" title="Sửa">
                                                        <i class="fas fa-pencil-alt text-white"></i>
                                                    </a>
                                                </div>
                                                <form action="{{ route('admin.daily-tasks.toggle-active', $task) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link action-icon bg-{{ $task->active ? 'warning' : 'success' }} p-1 mb-0" title="{{ $task->active ? 'Tạm dừng' : 'Kích hoạt' }}">
                                                        <i class="fas fa-{{ $task->active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                               
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$tasks" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection