@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Chi tiết nhiệm vụ: {{ $dailyTask->name }}</h5>
                        <p class="text-sm mb-0">{{ $dailyTask->description }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.daily-tasks.edit', $dailyTask) }}" class="btn bg-gradient-primary btn-sm">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.daily-tasks.index') }}" class="btn bg-gradient-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Thông tin cơ bản -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="text-white">Loại nhiệm vụ</h6>
                                <span class="badge bg-white text-primary">
                                    @switch($dailyTask->type)
                                        @case('login') Đăng nhập @break
                                        @case('comment') Bình luận @break
                                        @case('bookmark') Theo dõi @break
                                        @case('share') Chia sẻ @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-success text-white">
                            <div class="card-body text-center">
                                <h6 class="text-white">Thưởng xu</h6>
                                <h4 class="text-white">{{ $dailyTask->coin_reward }} xu</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="text-white">Max/ngày</h6>
                                <h4 class="text-white">{{ $dailyTask->max_per_day }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-{{ $dailyTask->active ? 'success' : 'secondary' }} text-white">
                            <div class="card-body text-center">
                                <h6 class="text-white">Trạng thái</h6>
                                <span class="badge bg-white text-{{ $dailyTask->active ? 'success' : 'secondary' }}">
                                    {{ $dailyTask->active ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3">Thống kê nhiệm vụ</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-primary">Tổng hoàn thành</h6>
                                <h4 class="text-primary">{{ number_format($stats['total_completions']) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-success">User tham gia</h6>
                                <h4 class="text-success">{{ number_format($stats['unique_users']) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-info">Hôm nay</h6>
                                <h4 class="text-info">{{ number_format($stats['today_completions']) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-warning">Tuần này</h6>
                                <h4 class="text-warning">{{ number_format($stats['this_week_completions']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lịch sử hoàn thành gần đây -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header pb-0">
                                <h6 class="mb-0">Lịch sử hoàn thành gần đây</h6>
                            </div>
                            <div class="card-body px-0 pt-0 pb-2">
                                @if($recentCompletions->count() > 0)
                                    <div class="table-responsive p-0">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-xxs font-weight-bolder">User</th>
                                                    <th class="text-uppercase text-xxs font-weight-bolder">Số lần hoàn thành</th>
                                                    <th class="text-uppercase text-xxs font-weight-bolder">Ngày</th>
                                                    <th class="text-uppercase text-xxs font-weight-bolder">Lần cuối</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentCompletions as $completion)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div>
                                                                    <img src="{{ $completion->user->avatar ? Storage::url($completion->user->avatar) : asset('images/defaults/avatar_default.jpg') }}" 
                                                                         class="avatar avatar-sm me-3" alt="user">
                                                                </div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm">{{ $completion->user->name }}</h6>
                                                                    <p class="text-xs text-secondary mb-0">{{ $completion->user->email }}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-sm bg-gradient-success">{{ $completion->completed_count }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-xs font-weight-bold">{{ $completion->task_date }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-xs font-weight-bold">{{ $completion->last_completed_at ? $completion->last_completed_at->format('d/m/Y H:i') : '-' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted">Chưa có lịch sử hoàn thành</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection