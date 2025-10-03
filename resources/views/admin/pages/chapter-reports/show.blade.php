@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Chi tiết báo cáo</h5>
                            <p class="text-sm text-muted mb-0">ID: {{ $report->id }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('chapter', ['storySlug' => $report->story->slug, 'chapterSlug' => $report->chapter->slug]) }}"
                                class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Xem chương
                            </a>
                            <a href="{{ route('admin.chapter-reports.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Report Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-dark">Thông tin người báo cáo</h6>
                                    <hr>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user text-info me-2"></i>
                                        <strong>{{ $report->user->name }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-envelope text-info me-2"></i>
                                        {{ $report->user->email }}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar text-info me-2"></i>
                                        Tham gia: {{ $report->user->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-dark">Thông tin chương</h6>
                                    <hr>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-book text-success me-2"></i>
                                        <strong>Truyện:</strong> {{ $report->story->title }}
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-file-alt text-success me-2"></i>
                                        <strong>Chương:</strong> {{ $report->chapter->number }} -
                                        {{ $report->chapter->title }}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-eye text-success me-2"></i>
                                        <strong>Truy cập:</strong>
                                        <a href="{{ route('chapter', ['storySlug' => $report->story->slug, 'chapterSlug' => $report->chapter->slug]) }}"
                                            target="_blank" class="btn btn-link btn-sm p-0">Xem chương</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-gradient-dark">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-white mb-1">Trạng thái hiện tại</h6>
                                            <div class="d-flex align-items-center">
                                                {!! $report->status_badge !!}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-white-50">Cập nhật lần cuối</small><br>
                                            <span
                                                class="text-white">{{ $report->updated_at->format('d/m/Y H:i:s') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Description -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Nội dung báo cáo
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class>{{ $report->description }}</p>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">
                                            Gửi lúc: {{ $report->created_at->format('d/m/Y H:i:s') }}
                                        </small>
                                        <small class="text-muted">
                                            ID báo cáo: #{{ $report->id }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Response Form -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-tools me-2"></i>Phản hồi từ admin
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.chapter-reports.update.status', $report->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Thay đổi trạng thái</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>
                                                Chờ xử lý</option>
                                            <option value="processing"
                                                {{ $report->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>
                                                Đã xử lý</option>
                                            <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>
                                                Từ chối</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Thời gian</label>
                                        <div class="form-control-plaintext">
                                            {{ $report->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                                @if ($report->admin_response)
                                    <div class="mb-3">
                                        <label class="form-label">Phản hồi hiện tại</label>
                                        <div class="alert alert-info">
                                            <i class="fas fa-comment me-2"></i>{{ $report->admin_response }}
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="admin_response" class="form-label">Phản hồi của bạn</label>
                                    <textarea name="admin_response" id="admin_response" class="form-control @error('admin_response') is-invalid @enderror"
                                        rows="4" placeholder="Nhập phản hồi cho người báo cáo... (không bắt buộc)" maxlength="1000">{{ old('admin_response', $report->admin_response) }}</textarea>
                                    <div class="form-text">
                                        Số ký tự: <span id="charCount">0</span>/1000
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn bg-gradient-primary">
                                        <i class="fas fa-cog me-1"></i> Cập nhật trạng thái
                                    </button>
                                    <a href="{{ route('admin.chapter-reports.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('admin_response');
            const charCount = document.getElementById('charCount');

            // Character counter
            textarea.addEventListener('input', function() {
                const count = this.value.length;
                charCount.textContent = count;

                if (count > 1000) {
                    charCount.classList.add('text-danger');
                } else {
                    charCount.classList.remove('text-danger');
                }
            });

            // Initial count
            charCount.textContent = textarea.value.length;
        });
    </script>
@endpush

@push('styles-admin')
    <style>
        .charCount.text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
    </style>
@endpush
