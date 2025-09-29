@extends('layouts.information')

@section('info_title', 'Nhiệm vụ hàng ngày')
@section('info_description', 'Hoàn thành nhiệm vụ hàng ngày để nhận xu thưởng trên ' . request()->getHost())
@section('info_keyword', 'nhiệm vụ hàng ngày, xu thưởng, điểm thưởng, ' . request()->getHost())
@section('info_section_title', 'Nhiệm vụ hàng ngày')
@section('info_section_desc', 'Hoàn thành các nhiệm vụ để nhận xu thưởng')

@push('styles')
    <style>
        .task-card {
            background: white;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color-1);
        }

        .task-card.completed {
            background: linear-gradient(135deg, #f8fff8 0%, #e8f8e8 100%);
            border-color: #28a745;
        }

        .task-card.completed::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .task-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .task-icon.login { background: linear-gradient(135deg, #6c5ce7, #a29bfe); color: white; }
        .task-icon.comment { background: linear-gradient(135deg, #00b894, #00cec9); color: white; }
        .task-icon.bookmark { background: linear-gradient(135deg, #fdcb6e, #f39c12); color: white; }
        .task-icon.share { background: linear-gradient(135deg, #e17055, #d63031); color: white; }

        .progress-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-color-1) var(--progress, 0%), #f1f3f4 var(--progress, 0%));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .coin-reward {
            background: linear-gradient(135deg, #f39c12, #fdcb6e);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .task-btn {
            background: linear-gradient(135deg, var(--primary-color-1), var(--primary-color-3));
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .task-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(123, 197, 174, 0.3);
            color: white;
        }

        .task-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color-1), var(--primary-color-3));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .history-table th {
            background: linear-gradient(90deg, rgba(123, 197, 174, 0.1), rgba(158, 210, 190, 0.05));
            border-color: rgba(123, 197, 174, 0.2);
            font-weight: 600;
        }

        .history-row {
            transition: all 0.3s ease;
        }

        .history-row:hover {
            background-color: rgba(123, 197, 174, 0.05);
            transform: translateX(3px);
        }

        .task-type-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-login { background: #e3f2fd; color: #1976d2; }
        .badge-comment { background: #e8f5e8; color: #2e7d32; }
        .badge-bookmark { background: #fff3e0; color: #f57c00; }
        .badge-share { background: #fce4ec; color: #c2185b; }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideInUp 0.6s ease-out;
        }
    </style>
@endpush

@section('info_content')
    <div class="animate-slide-up">
        <!-- Stats Section -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['today'] }}</div>
                    <div class="stats-label">Hôm nay</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['this_week'] }}</div>
                    <div class="stats-label">Tuần này</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['this_month'] }}</div>
                    <div class="stats-label">Tháng này</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total'] }}</div>
                    <div class="stats-label">Tổng cộng</div>
                </div>
            </div>
        </div>

        <!-- Daily Tasks Section -->
        <div class="row g-4 mb-5">
            @foreach($tasks as $task)
                <div class="col-12 col-md-6">
                    <div class="task-card {{ $task['is_completed'] ? 'completed' : '' }} p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3 flex-grow-1">
                            <div class="flex-grow-1">
                                <div class="task-icon {{ $task['type'] }}">
                                    @switch($task['type'])
                                        @case('login')
                                            <i class="fas fa-sign-in-alt"></i>
                                            @break
                                        @case('comment')
                                            <i class="fas fa-comment"></i>
                                            @break
                                        @case('bookmark')
                                            <i class="fas fa-bookmark"></i>
                                            @break
                                        @case('share')
                                            <i class="fas fa-share-alt"></i>
                                            @break
                                    @endswitch
                                </div>
                                <h5 class="fw-bold mb-2">{{ $task['name'] }}</h5>
                                <p class="text-muted mb-2">{{ $task['description'] }}</p>
                                <div class="coin-reward">
                                    <i class="fas fa-coins"></i>
                                    {{ $task['coin_reward'] }} xu
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="progress-circle" style="--progress: {{ $task['progress_percentage'] }}%">
                                    <div class="progress-text">{{ $task['completed_count'] }}/{{ $task['max_per_day'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            @if($task['is_completed'])
                                <button class="task-btn" disabled>
                                    <i class="fas fa-check me-2"></i>Đã hoàn thành
                                </button>
                            @else
                                @switch($task['type'])
                                    @case('login')
                                        <button class="task-btn" onclick="completeLoginTask()">
                                            <i class="fas fa-gift me-2"></i>Nhận thưởng
                                        </button>
                                        @break
                                    @case('comment')
                                        <small class="text-muted">Bình luận truyện để nhận thưởng</small>
                                        @break
                                    @case('bookmark')
                                        <small class="text-muted">Theo dõi truyện để nhận thưởng</small>
                                        @break
                                    @case('share')
                                        <small class="text-muted">Chia sẻ truyện để nhận thưởng</small>
                                        @break
                                @endswitch
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- History Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i>
                    Lịch sử nhiệm vụ
                </h5>
            </div>
            <div class="card-body p-0">
                @if($history->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover history-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nhiệm vụ</th>
                                    <th>Loại</th>
                                    <th>Thưởng</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $item)
                                    <tr class="history-row">
                                        <td>
                                            <div class="fw-medium">{{ $item->dailyTask->name }}</div>
                                            <small class="text-muted">{{ $item->dailyTask->description }}</small>
                                        </td>
                                        <td>
                                            <span class="task-type-badge badge-{{ $item->dailyTask->type }}">
                                                @switch($item->dailyTask->type)
                                                    @case('login') Đăng nhập @break
                                                    @case('comment') Bình luận @break
                                                    @case('bookmark') Theo dõi @break
                                                    @case('share') Chia sẻ @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-warning">
                                                <i class="fas fa-coins me-1"></i>
                                                {{ $item->dailyTask->coin_reward }} xu
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $item->last_completed_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center p-3">
                        <x-pagination :paginator="$history" />
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-tasks fa-3x"></i>
                        </div>
                        <h6>Chưa có lịch sử nhiệm vụ</h6>
                        <p class="text-muted">Hãy bắt đầu hoàn thành các nhiệm vụ để nhận xu thưởng!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        // Complete login task
        function completeLoginTask() {
            fetch('{{ route("user.daily-tasks.complete.login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Reload page to update UI
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
            });
        }

        // Auto complete login task on page load if not completed
        document.addEventListener('DOMContentLoaded', function() {
            // Check if login task is not completed and auto trigger it
            const loginTaskBtn = document.querySelector('.task-btn[onclick="completeLoginTask()"]');
            if (loginTaskBtn && !loginTaskBtn.disabled) {
                // Add pulse animation to draw attention
                loginTaskBtn.classList.add('pulse');
            }
        });
    </script>
@endpush