@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-0 mx-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">

                                Danh sách chương truyện: {{ $story->title }}

                            </h5>
                            <p class="text-sm mb-0">
                                Tổng số: {{ $totalChapters }} chương
                                ({{ $publishedChapters }} hiển thị / {{ $draftChapters }} nháp)
                            </p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="">- Trạng thái -</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                            </select>

                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Tìm kiếm...">
                                <button class="btn bg-gradient-primary btn-sm px-2 mb-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <div>

                            <a href="{{ route('admin.stories.index') }}" class="btn bg-gradient-secondary btn-sm mb-0 me-2">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            <a href="{{ route('admin.stories.chapters.create', $story) }}" class="btn bg-gradient-primary btn-sm mb-0">
                                <i class="fas fa-plus me-2"></i>Thêm chương mới
                            </a>

                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        STT
                                    </th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">
                                        Tên chương
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Nội dung
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Số cám
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Views
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Slug
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Trạng thái
                                    </th>

                                    <th class="text-uppercase  text-xxs font-weight-bolder ">
                                        Ngày tạo
                                    </th>

                                    <th
                                        class="text-center text-uppercase  text-xxs font-weight-bolder ">
                                        Hành động
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chapters as $chapter)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">Chương {{ $chapter->number }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $chapter->title }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-truncate" style="max-width: 200px;">
                                                {{ Str::limit($chapter->content, 50) }}
                                            </p>
                                        </td>
                                        <td>
                                            @if($chapter->is_free)
                                                <span class="badge bg-gradient-success">Miễn phí</span>
                                            @else
                                                <span class="badge bg-gradient-danger">{{ $chapter->price ?? 0 }} cám</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $chapter->views ?? 0 }}</p>
                                        </td>

                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $chapter->slug }}
                                            </p>
                                        </td>

                                        <td>
                                            <span
                                                class="badge badge-sm bg-gradient-{{ $chapter->status === 'published' ? 'success' : 'warning' }}">
                                                {{ $chapter->status === 'published' ? 'Hiển thị' : 'Nháp' }}
                                            </span>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $chapter->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a href="{{ route('admin.stories.chapters.show', ['story' => $story, 'chapter' => $chapter]) }}" class="btn btn-link p-1 mb-0 action-icon view-icon" title="Chi tiết">
                                                        <i class="fas fa-eye text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a href="{{ route('admin.stories.chapters.edit', ['story' => $story, 'chapter' => $chapter]) }}" class="btn btn-link p-1 mb-0 action-icon edit-icon" title="Sửa">
                                                        <i class="fas fa-pencil-alt text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2">
                                                    @include('admin.pages.components.delete-form', [
                                                        'id' => $chapter->id,
                                                        'route' => route('admin.stories.chapters.destroy', ['story' => $story, 'chapter' => $chapter])
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">Chưa có chương nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 pt-4">
                        <x-pagination :paginator="$chapters" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')

@endpush
