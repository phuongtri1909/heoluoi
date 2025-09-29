@extends('layouts.information')

@section('info_title', 'Danh sách truyện của tôi')
@section('info_description', 'Quản lý danh sách truyện của bạn trên ' . request()->getHost())
@section('info_keyword', 'danh sách truyện, đăng truyện, tác giả, ' . request()->getHost())
@section('info_section_title', 'Truyện của tôi')
@section('info_section_desc', 'Quản lý danh sách truyện đã đăng')

@push('styles')
    <style>
        .table-stories th {
            background: linear-gradient(90deg, rgba(123, 197, 174, 0.2), rgba(158, 210, 190, 0.1));
            border-color: rgba(123, 197, 174, 0.3);
        }

        .story-row {
            transition: all 0.3s ease;
        }

        .story-row:hover {
            background-color: rgba(123, 197, 174, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.03);
        }

        .story-badge {
            font-size: 0.72rem;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .story-actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .story-actions .btn {
            transition: all 0.3s ease;
        }

        .story-actions .btn:hover {
            transform: translateX(3px);
        }

        .story-thumb {
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .story-thumb:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .filter-section {
            background: rgba(123, 197, 174, 0.03);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(123, 197, 174, 0.1);
        }

        .story-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            flex: 1;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #777;
        }

        .modal-confirm {
            max-width: 400px;
        }

        .modal-confirm .icon-box {
            color: #f44336;
            height: 90px;
            width: 90px;
            margin: 0 auto 20px;
            border-radius: 50%;
            border: 3px solid #f8d7da;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-confirm .icon-box i {
            font-size: 46px;
        }

        .modal-confirm .btn {
            border-radius: 30px;
            padding: 8px 25px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animated-fadeInUp {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
@endpush

@section('info_content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('user.author.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Quay lại Dashboard
            </a>
            <h4 class="fw-bold mb-0">Truyện của tôi</h4>
            <p class="text-muted small mb-0">Quản lý tất cả truyện của bạn</p>
        </div>
        <a href="{{ route('user.author.stories.create') }}" class="btn btn-outline-primary">
            <i class="fas fa-plus me-1"></i> Truyện
        </a>
    </div>

    <div class="story-stats animated-fadeInUp">
        <div class="stat-item">
            <div class="stat-number text-primary">{{ $stories->total() ?? 0 }}</div>
            <div class="stat-label">Tổng truyện</div>
        </div>
        <div class="stat-item">
            <div class="stat-number text-success">{{ $publishedCount ?? 0 }}</div>
            <div class="stat-label">Đã xuất bản</div>
        </div>
        <div class="stat-item">
            <div class="stat-number text-warning">{{ $pendingCount ?? 0 }}</div>
            <div class="stat-label">Chờ duyệt</div>
        </div>
        <div class="stat-item">
            <div class="stat-number text-info">{{ $draftCount ?? 0 }}</div>
            <div class="stat-label">Bản nháp</div>
        </div>
    </div>

    <div class="filter-section animated-fadeInUp">
        <form action="{{ route('user.author.stories') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm truyện..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="approved" class="form-select">
                    <option value="">-- Tất cả phê duyệt --</option>
                    <option value="1" {{ request('approved') == '1' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="0" {{ request('approved') == '0' ? 'selected' : '' }}>Chờ duyệt</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Lọc</button>
            </div>
        </form>
    </div>

    @if ($stories->count() > 0)
        <div class="table-responsive animated-fadeInUp">
            <table class="table table-hover table-stories">
                <thead>
                    <tr>
                        <th scope="col" width="70">Ảnh</th>
                        <th scope="col">Tên truyện</th>
                        <th scope="col" width="120">Loại</th>
                        <th scope="col" width="150">Trạng thái</th>
                        <th scope="col" width="5">Full</th>
                        <th scope="col" width="130">Lượt xem</th>
                        <th scope="col" width="150">Đề cử</th>
                        <th scope="col" width="120">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stories as $story)
                        <tr class="story-row">
                            <td>
                                <div class="position-relative">
                                    <img src="{{ Storage::url($story->cover_thumbnail) }}" alt="{{ $story->title }}"
                                        class="story-thumb" style="width: 45px; height: 65px; object-fit: cover;">
                                    @if ($story->is_18_plus)
                                        <div class="position-absolute top-0 end-0">
                                            <div class="age-badge">18+</div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a href="{{  route('show.page.story', $story->slug) }}" class="fw-bold text-decoration-none text-dark">{{ $story->title }}</a>
                                <div class="text-muted small">
                                    <span class="me-2"><i class="fas fa-book me-1"></i>{{ $story->chapters->count() }}
                                        chương</span>
                                    <span><i
                                            class="fas fa-calendar-alt me-1"></i>{{ $story->created_at->format('d/m/Y') }}</span>
                                </div>
                                {{-- <div class="small mt-1">
                                    @foreach ($story->categories->take(3) as $category)
                                        <span class="badge bg-light text-dark me-1">{{ $category->name }}</span>
                                    @endforeach
                                    @if ($story->categories->count() > 3)
                                        <span
                                            class="badge bg-light text-dark">+{{ $story->categories->count() - 3 }}</span>
                                    @endif
                                </div> --}}
                            </td>
                            <td>
                                @if ($story->story_type == 'original')
                                    <span class="story-badge bg-info text-white">Sáng tác</span>
                                @elseif($story->story_type == 'translated')
                                    <span class="story-badge bg-warning text-dark">Dịch/Edit</span>
                                @else
                                    <span class="story-badge bg-secondary text-white">Sưu tầm</span>
                                @endif
                            </td>
                            <td>
                                @if ($story->status == 'published')
                                    <span class="story-badge bg-success text-white">Đã xuất bản</span>
                                @elseif($story->status == 'draft')
                                    <span class="story-badge bg-secondary text-white">Bản nháp</span>
                                @elseif($story->status == 'pending')
                                    <span class="story-badge bg-warning text-dark">Chờ duyệt</span>
                                @elseif($story->status == 'rejected')
                                    <span class="story-badge bg-danger text-white">Từ chối</span>
                                @endif

                            </td>
                            <td>
                                @if ($story->completed)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-center">
                                    <div class="fw-bold">{{ number_format($story->total_views ?? 0) }}</div>
                                    <div class="small text-muted">lượt xem</div>
                                    <div class="fw-bold text-success mt-1">{{ number_format($story->total_revenue ?? 0) }}</div>
                                    <div class="small text-muted">xu</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-center">
                                    @if($story->is_featured || $story->isCurrentlyAdminFeatured())
                                        <span class="badge bg-warning text-dark mb-1">Admin đề cử</span>
                                        @php
                                            $adminFeatured = $story->activeAdminFeatured;
                                            $order = $adminFeatured ? $adminFeatured->featured_order : $story->featured_order;
                                        @endphp
                                        @if($order)
                                            <small class="text-muted">#{{ $order }}</small>
                                        @endif
                                    @elseif($story->isCurrentlyAuthorFeatured())
                                        <span class="badge bg-info text-white mb-1">Tác giả đề cử</span>
                                        @php
                                            $authorFeatured = $story->activeAuthorFeatured;
                                        @endphp
                                        @if($authorFeatured)
                                            <small class="text-muted">
                                                {{ $authorFeatured->featured_until->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted small">Chưa đề cử</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="story-actions">
                                    <a href="{{ route('user.author.stories.chapters', $story->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list-ol me-1"></i> Chương
                                    </a>
                                    <a href="{{ route('user.author.stories.edit', $story->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-edit me-1"></i> Sửa
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $story->id }}">
                                        <i class="fas fa-trash-alt me-1"></i> Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal xác nhận xóa -->
                        <div class="modal fade" id="deleteModal{{ $story->id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $story->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-confirm">
                                <div class="modal-content">
                                    <div class="modal-header flex-column border-0 pb-0">
                                        <div class="icon-box">
                                            <i class="fas fa-trash-alt"></i>
                                        </div>
                                        <h4 class="modal-title w-100 text-center"
                                            id="deleteModalLabel{{ $story->id }}">
                                            Xác nhận xóa
                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <p>Bạn có chắc chắn muốn xóa truyện <strong>{{ $story->title }}</strong>?</p>
                                        <p class="text-danger small">Tất cả các chương sẽ bị xóa và không thể khôi phục.
                                        </p>
                                    </div>
                                    <div class="modal-footer justify-content-center border-0 pt-0">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Hủy</button>
                                        <form action="{{ route('user.author.stories.destroy', $story->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <x-pagination :paginator="$stories" />
        </div>
    @else
        <div class="empty-state text-center py-5 animated-fadeInUp">
            <div class="empty-icon mb-4">
                <i class="fas fa-book-open fa-xl text-muted"></i>
            </div>
            <h5>Bạn chưa có truyện nào</h5>
            <p class="text-muted">Hãy bắt đầu bằng cách đăng truyện đầu tiên của bạn!</p>
            <a href="{{ route('user.author.stories.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-plus me-2"></i> Đăng truyện mới
            </a>
        </div>
    @endif
@endsection

@push('info_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hiệu ứng hover cho hàng trong bảng
            const storyRows = document.querySelectorAll('.story-row');
            storyRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.08)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                });
            });

            // Hiệu ứng cho các ảnh thumbnail
            const storyThumbs = document.querySelectorAll('.story-thumb');
            storyThumbs.forEach(thumb => {
                thumb.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });

                thumb.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });

            // Hiệu ứng cho các nút thao tác
            const actionButtons = document.querySelectorAll('.story-actions .btn');
            actionButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(3px)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });
        });
    </script>
@endpush
