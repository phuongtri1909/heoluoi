@extends('layouts.information')

@section('info_title', 'Báo cáo của tôi - ' . config('app.name'))
@section('info_description', 'Xem danh sách các báo cáo lỗi chương đã gửi')
@section('info_keyword', 'báo cáo, lỗi chương, báo cáo của tôi')

@section('info_section_title', 'Báo cáo của tôi')
@section('info_section_desc', 'Danh sách các báo cáo lỗi chương bạn đã gửi đến admin')

@push('styles')
    <style>
        .report-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-color-1);
        }

        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color-1);
        }

        .report-card.pending::before {
            background: #ffc107;
        }

        .report-card.processing::before {
            background: #17a2b8;
        }

        .report-card.resolved::before {
            background: #28a745;
        }

        .report-card.rejected::before {
            background: #dc3545;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-resolved {
            background: #d4edda;
            color: #155724;
        }

        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .stats-number {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: var(--primary-color-1);
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .story-link {
            background: #f8f9fa;
            color: #495057;
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .story-link:hover {
            background: #e9ecef;
            color: #495057;
            border-color: #adb5bd;
        }

        .chapter-link {
            background: #f8f9fa;
            color: #495057;
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .chapter-link:hover {
            background: #e9ecef;
            color: #495057;
            border-color: #adb5bd;
        }

        .report-btn {
            background: var(--primary-color-1);
            border: none;
            color: white;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .report-btn:hover {
            background: var(--primary-color-2);
            color: white;
        }

        .description-box {
            background: #f8f9fa;
            border-left: 3px solid var(--primary-color-1);
            border-radius: 6px;
            padding: 1rem;
            margin: 0.8rem 0;
        }

        .admin-response-box {
            background: #f8f9fa;
            border-left: 3px solid #6c757d;
            border-radius: 6px;
            padding: 1rem;
            margin: 0.8rem 0;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slideInUp 0.4s ease-out;
        }
    </style>
@endpush

@section('info_content')
    <div class="animate-slide-up">
        <!-- Stats Section -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $reports->total() }}</div>
                    <div class="stats-label">Tổng báo cáo</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $reports->where('status', 'pending')->count() }}</div>
                    <div class="stats-label">Chờ xử lý</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $reports->where('status', 'processing')->count() }}</div>
                    <div class="stats-label">Đang xử lý</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $reports->where('status', 'resolved')->count() }}</div>
                    <div class="stats-label">Đã xử lý</div>
                </div>
            </div>
        </div>

        @if ($reports->count() > 0)
            <!-- Reports List -->
            <div class="row g-4">
                @foreach ($reports as $report)
                    <div class="col-12">
                        <div class="report-card {{ $report->status }} p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold mb-2">
                                            Báo cáo #{{ $report->id }}
                                            <span class="status-badge badge-{{ $report->status }}">
                                                @switch($report->status)
                                                    @case('pending') Chờ xử lý @break
                                                    @case('processing') Đang xử lý @break
                                                    @case('resolved') Đã xử lý @break
                                                    @case('rejected') Từ chối @break
                                                @endswitch
                                            </span>
                                        </h5>

                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <a href="{{ route('show.page.story', $report->story->slug) }}" 
                                                   class="story-link me-3">
                                                    {{ Str::limit($report->story->title, 35) }}
                                                </a>
                                                <a href="{{ route('chapter', ['storySlug' => $report->story->slug, 'chapterSlug' => $report->chapter->slug]) }}" 
                                                   class="chapter-link" target="_blank">
                                                    Chương {{ $report->chapter->number }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="description-box">
                                            <strong>Nội dung báo cáo:</strong>
                                            <p class="mb-2 mt-1">{{ $report->description }}</p>
                                        </div>

                                        @if ($report->admin_response)
                                            <div class="admin-response-box">
                                                <strong>Phản hồi từ admin:</strong>
                                                <p class="mb-2 mt-1">{{ $report->admin_response }}</p>
                                                <small class="text-muted">
                                                    Cập nhật: {{ $report->updated_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-end">
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            {{ $report->created_at->format('d/m/Y H:i') }}
                                        </small><br>
                                        <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                    </div>
                                    <a href="{{ route('chapter', ['storySlug' => $report->story->slug, 'chapterSlug' => $report->chapter->slug]) }}" 
                                       class="report-btn" target="_blank">
                                        Xem chương
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($reports->hasPages())
                <div class="d-flex justify-content-center p-3">
                    <x-pagination :paginator="$reports" />
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-flag fa-4x"></i>
                </div>
                <h6>Chưa có báo cáo nào</h6>
                <p class="text-muted">Bạn chưa gửi báo cáo lỗi chương nào đến admin.<br>
                    Khi bạn phát hiện lỗi trong chương truyện, hãy sử dụng nút "Báo lỗi chương" để thông báo cho chúng tôi.
                </p>
                <a href="{{ route('home') }}" class="report-btn">
                    <i class="fas fa-home me-2"></i>Về trang chủ
                </a>
            </div>
        @endif
    </div>
@endsection

@push('info_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to cards on load
            const cards = document.querySelectorAll('.report-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endpush
