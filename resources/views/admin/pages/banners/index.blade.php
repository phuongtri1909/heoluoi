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
        
        .img-fluid {
            width: 60px !important;
            height: 40px !important;
        }
    }
    
    @media (max-width: 576px) {
        .table th,
        .table td {
            padding: 0.375rem 0.125rem;
            font-size: 0.75rem;
        }
        
        .img-fluid {
            width: 40px !important;
            height: 30px !important;
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
                            <h5 class="mb-0">Danh sách Banner</h5>
                        </div>
                        <a href="{{ route('admin.banners.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus"></i> Thêm Banner
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder ">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder  ps-2">Hình ảnh</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ">Liên kết tới</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ">Trạng thái</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder ">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($banners as $banner)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $banner->id }}</p>
                                        </td>
                                        <td>
                                            <img src="{{ Storage::url($banner->image) }}" alt="Banner" class="img-fluid" style="max-height: 60px;">
                                        </td>
                                        <td>
                                            @if($banner->story)
                                                <p class="text-xs font-weight-bold mb-0">Truyện: {{ $banner->story->title }}</p>
                                            @else
                                                <p class="text-xs font-weight-bold mb-0">
                                                    <a href="{{ $banner->link }}" target="_blank">{{ $banner->link }}</a>
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $banner->status ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $banner->status ? 'Đang hiển thị' : 'Ẩn' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="mx-3 action-icon edit-icon" title="Sửa">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @include('admin.pages.components.delete-form', [
                                                    'id' => $banner->id,
                                                    'route' => route('admin.banners.destroy', $banner->id)
                                                ])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$banners" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
