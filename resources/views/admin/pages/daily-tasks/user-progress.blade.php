@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Tiến độ hoàn thành nhiệm vụ của User</h5>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.daily-tasks.statistics') }}" class="btn bg-gradient-info btn-sm mb-0">
                                <i class="fas fa-chart-bar"></i> Thống kê
                            </a>
                            <a href="{{ route('admin.daily-tasks.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder">User</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Nhiệm vụ</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Loại</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Số lần hoàn thành</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Ngày</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Lần cuối</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userProgress as $progress)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ $progress->user->avatar ? Storage::url($progress->user->avatar) : asset('images/defaults/avatar_default.jpg') }}" 
                                                         class="avatar avatar-sm me-3" alt="user">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $progress->user->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $progress->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <p class="text-xs font-weight-bold mb-0">{{ $progress->dailyTask->name }}</p>
                                                @if($progress->dailyTask->description)
                                                    <p class="text-xs text-muted mb-0">{{ Str::limit($progress->dailyTask->description, 30) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-{{ 
                                                $progress->dailyTask->type === 'login' ? 'primary' : 
                                                ($progress->dailyTask->type === 'comment' ? 'success' : 
                                                ($progress->dailyTask->type === 'bookmark' ? 'warning' : 'danger')) 
                                            }}">
                                                @switch($progress->dailyTask->type)
                                                    @case('login') Đăng nhập @break
                                                    @case('comment') Bình luận @break
                                                    @case('bookmark') Theo dõi @break
                                                    @case('share') Chia sẻ @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-sm bg-gradient-success">{{ $progress->completed_count }}/{{ $progress->dailyTask->max_per_day }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $progress->task_date }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $progress->last_completed_at ? $progress->last_completed_at->format('d/m/Y H:i') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">{{ $progress->ip_address ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$userProgress" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection