@extends('admin.layouts.app')

@push('styles-admin')
    <style>
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-card h4 {
            margin: 0;
            font-weight: 600;
        }

        .info-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        .content-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            min-height: 200px;
            line-height: 1.6;
            white-space: pre-wrap;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9em;
        }

        .status-published {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-draft {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .price-badge {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            display: inline-block;
        }

        .free-badge {
            background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            display: inline-block;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
            margin-right: 15px;
        }

        .info-value {
            flex: 1;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-edit {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-back {
            background: linear-gradient(45deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(45deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .link-aff {
            color: #007bff;
            text-decoration: none;
            word-break: break-all;
        }

        .link-aff:hover {
            text-decoration: underline;
        }

        .no-link {
            color: #6c757d;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                min-width: auto;
                margin-right: 0;
                margin-bottom: 5px;
            }

            .action-buttons {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>Chi tiết chương {{ $chapter->number }}
                        </h5>
                        <div class="action-buttons">
                            <a href="{{ route('admin.stories.chapters.edit', ['story' => $story, 'chapter' => $chapter]) }}"
                               class="btn-action btn-edit">
                                <i class="fas fa-edit"></i>
                                Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.stories.chapters.index', $story) }}"
                               class="btn-action btn-back">
                                <i class="fas fa-arrow-left"></i>
                                Trở về
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">

                    

                    <!-- Story Info Card -->
                    <div class="info-card">
                        <h4>{{ $story->title }}</h4>
                        <p><i class="fas fa-book me-2"></i>{{ $story->author_name ?? 'Tác giả không xác định' }}</p>
                    </div>

                    <!-- Chapter Stats -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">{{ $chapter->views ?? 0 }}</div>
                            <div class="stat-label">Lượt xem</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $chapter->purchases()->count() }}</div>
                            <div class="stat-label">Lượt mua</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $story->ratings()->count() }}</div>
                            <div class="stat-label">Đánh giá</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ str_word_count(strip_tags($chapter->content)) }}</div>
                            <div class="stat-label">Số từ</div>
                        </div>
                    </div>

                    <!-- Chapter Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-hashtag me-2"></i>Số chương:
                                </div>
                                <div class="info-value">
                                    <strong>{{ $chapter->number }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-toggle-on me-2"></i>Trạng thái:
                                </div>
                                <div class="info-value">
                                    @if($chapter->status == 'published')
                                        <span class="status-badge status-published">
                                            <i class="fas fa-check-circle me-1"></i>Đã xuất bản
                                        </span>
                                    @else
                                        <span class="status-badge status-draft">
                                            <i class="fas fa-edit me-1"></i>Bản nháp
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-coins me-2"></i>Hình thức:
                                </div>
                                <div class="info-value">
                                    @if($chapter->is_free)
                                        <span class="free-badge">
                                            <i class="fas fa-unlock me-1"></i>Miễn phí
                                        </span>
                                    @else
                                        <span class="price-badge">
                                            <i class="fas fa-lock me-1"></i>{{ number_format($chapter->price) }} xu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-calendar me-2"></i>Ngày tạo:
                                </div>
                                <div class="info-value">
                                    {{ $chapter->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-heading me-2"></i>Tiêu đề:
                                </div>
                                <div class="info-value">
                                    <strong>{{ $chapter->title }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-link me-2"></i>Slug:
                                </div>
                                <div class="info-value">
                                    <code>{{ $chapter->slug }}</code>
                                </div>
                            </div>
                        </div>

                        @if($chapter->updated_at && $chapter->updated_at != $chapter->created_at)
                        <div class="col-12">
                            <div class="info-row">
                                <div class="info-label">
                                    <i class="fas fa-clock me-2"></i>Cập nhật cuối:
                                </div>
                                <div class="info-value">
                                    {{ $chapter->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Chapter Content -->
                        <div class="col-12">
                            <div class="form-group">
                                <label class="info-label mb-3">
                                    <i class="fas fa-file-alt me-2"></i>Nội dung chương:
                                </label>
                                <div class="content-display">{{ $chapter->content }}</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 text-center mt-4">
                            <div class="action-buttons justify-content-center">
                                <a href="{{ route('admin.stories.chapters.edit', ['story' => $story, 'chapter' => $chapter]) }}"
                                   class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i>
                                    Chỉnh sửa chương
                                </a>

                                <a href="{{ route('admin.stories.chapters.index', $story) }}"
                                   class="btn-action btn-back">
                                    <i class="fas fa-list"></i>
                                    Danh sách chương
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.stories.chapters.destroy', ['story' => $story, 'chapter' => $chapter]) }}"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa chương này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i>
                                        Xóa chương
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-navigation me-2"></i>Điều hướng nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($prevChapter = $story->chapters()->where('number', '<', $chapter->number)->orderBy('number', 'desc')->first())
                        <div class="col-md-6">
                            <a href="{{ route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $prevChapter]) }}"
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-chevron-left me-2"></i>
                                Chương {{ $prevChapter->number }}: {{ Str::limit($prevChapter->title, 30) }}
                            </a>
                        </div>
                        @endif

                        @if($nextChapter = $story->chapters()->where('number', '>', $chapter->number)->orderBy('number', 'asc')->first())
                        <div class="col-md-6">
                            <a href="{{ route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $nextChapter]) }}"
                               class="btn btn-outline-primary w-100">
                                Chương {{ $nextChapter->number }}: {{ Str::limit($nextChapter->title, 30) }}
                                <i class="fas fa-chevron-right ms-2"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scrolling to navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add copy to clipboard functionality for slug
        const slugElement = document.querySelector('code');
        if (slugElement) {
            slugElement.style.cursor = 'pointer';
            slugElement.title = 'Click để copy slug';
            slugElement.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent).then(() => {
                    showToast('Đã sao chép slug!', 'success');
                });
            });
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        }

        // Add confirmation for delete action
        const deleteForm = document.querySelector('form[action*="destroy"]');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa chương này? Hành động này không thể hoàn tác!')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
