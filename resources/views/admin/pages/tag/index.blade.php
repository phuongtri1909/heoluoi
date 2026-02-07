@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Danh sách chủ đề (Tag)</h5>
                        </div>
                        <a href="{{ route('admin.tags.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus"></i> Thêm chủ đề
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Tên chủ đề</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Slug</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tags as $tag)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $tag->id }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $tag->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $tag->slug }}</p>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center gap-1">
                                                <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @include('admin.pages.components.delete-form', [
                                                    'id' => $tag->id,
                                                    'route' => route('admin.tags.destroy', $tag)
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$tags" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
