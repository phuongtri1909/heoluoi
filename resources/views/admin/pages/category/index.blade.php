@extends('admin.layouts.app')

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column !important;
            gap: 1rem;
        }
        
        .btn-sm {
            width: 100%;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }
        
        .text-truncate {
            max-width: 150px;
        }
    }
    
    @media (max-width: 576px) {
        .table th,
        .table td {
            padding: 0.375rem 0.125rem;
            font-size: 0.75rem;
        }
        
        .text-truncate {
            max-width: 100px;
        }
        
        .action-icon {
            padding: 0.25rem !important;
        }
    }
</style>
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Danh sách thể loại</h5>
                        </div>
                        <a href="{{ route('admin.categories.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus"></i> Thêm thể loại
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">ID</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Tên thể loại</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Slug</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Mô tả</th>
                                    <th class="text-center text-uppercase  text-xxs font-weight-bolder ">Thể loại chính</th>
                                    <th class="text-center text-uppercase  text-xxs font-weight-bolder ">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $category->id }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $category->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $category->slug }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $category->description }}</p>
                                        </td>
                                        <td class="text-center">
                                            @if($category->is_main)
                                                <span class="badge badge-sm bg-gradient-success">Có</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Không</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a title="Xem truyện" href="{{ route('admin.categories.show', $category->id) }}" class="text-info action-icon view-icon" title="Xem truyện">
                                                        <i class="fas fa-eye text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a title="Sửa" href="{{ route('admin.categories.edit', $category->id) }}" class="action-icon edit-icon" title="Sửa">
                                                        <i class="fas fa-pencil-alt text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2">
                                                    @include('admin.pages.components.delete-form', [
                                                        'id' => $category->id,
                                                        'route' => route('admin.categories.destroy', $category->id)
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$categories" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
