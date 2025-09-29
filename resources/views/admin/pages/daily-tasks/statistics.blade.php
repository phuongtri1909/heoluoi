@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Thống kê nhiệm vụ hàng ngày</h5>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.daily-tasks.user-progress') }}" class="btn bg-gradient-warning btn-sm mb-0">
                                <i class="fas fa-users"></i> Tiến độ user
                            </a>
                            <a href="{{ route('admin.daily-tasks.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    

                    <!-- Thống kê tổng quan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">Thống kê tổng quan</h6>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-white">Tổng nhiệm vụ</h6>
                                    <h4 class="text-white">{{ $stats['total_tasks'] }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-white">Nhiệm vụ hoạt động</h6>
                                    <h4 class="text-white">{{ $stats['active_tasks'] }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-white">Tổng hoàn thành</h6>
                                    <h4 class="text-white">{{ number_format($stats['total_completions']) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-white">User tham gia</h6>
                                    <h4 class="text-white">{{ number_format($stats['unique_users']) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-danger text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-white">Hôm nay</h6>
                                    <h4 class="text-white">{{ number_format($stats['today_completions']) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-dark text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-white">Tuần này</h6>
                                    <h4 class="text-white">{{ number_format($stats['this_week_completions']) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê theo loại nhiệm vụ -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">Thống kê theo loại nhiệm vụ</h6>
                        </div>
                        @foreach($taskTypeStats as $taskStat)
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6 class="text-{{ 
                                            $taskStat->type === 'login' ? 'primary' : 
                                            ($taskStat->type === 'comment' ? 'success' : 
                                            ($taskStat->type === 'bookmark' ? 'warning' : 'danger')) 
                                        }}">
                                            @switch($taskStat->type)
                                                @case('login') Đăng nhập @break
                                                @case('comment') Bình luận @break
                                                @case('bookmark') Theo dõi @break
                                                @case('share') Chia sẻ @break
                                            @endswitch
                                        </h6>
                                        <h4 class="text-{{ 
                                            $taskStat->type === 'login' ? 'primary' : 
                                            ($taskStat->type === 'comment' ? 'success' : 
                                            ($taskStat->type === 'bookmark' ? 'warning' : 'danger')) 
                                        }}">{{ number_format($taskStat->total_completions ?? 0) }}</h4>
                                        <p class="text-xs text-muted mb-0">{{ $taskStat->name }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Thống kê theo ngày (7 ngày gần nhất) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6 class="mb-0">Thống kê 7 ngày gần nhất</h6>
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    @if($dailyStats->count() > 0)
                                        <div class="table-responsive p-0">
                                            <table class="table align-items-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-uppercase text-xxs font-weight-bolder">Ngày</th>
                                                        <th class="text-uppercase text-xxs font-weight-bolder">Tổng hoàn thành</th>
                                                        <th class="text-uppercase text-xxs font-weight-bolder">User tham gia</th>
                                                        <th class="text-uppercase text-xxs font-weight-bolder">Trung bình/User</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($dailyStats as $dailyStat)
                                                        <tr>
                                                            <td>
                                                                <span class="text-xs font-weight-bold">{{ \Carbon\Carbon::parse($dailyStat->task_date)->format('d/m/Y') }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-sm bg-gradient-success">{{ number_format($dailyStat->total_completions) }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-sm bg-gradient-info">{{ number_format($dailyStat->unique_users) }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="text-xs font-weight-bold">
                                                                    {{ $dailyStat->unique_users > 0 ? number_format($dailyStat->total_completions / $dailyStat->unique_users, 1) : 0 }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-muted">Chưa có dữ liệu thống kê</p>
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