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
    }
    
    @media (max-width: 576px) {
        .table th,
        .table td {
            padding: 0.375rem 0.125rem;
            font-size: 0.75rem;
        }
        
        .action-icon {
            padding: 0.25rem !important;
        }
        
        /* Hide description column on very small screens */
        .table th:nth-child(3),
        .table td:nth-child(3) {
            display: none;
        }
    }
</style>
@endpush

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Danh sách cấu hình hệ thống</h6>
                    <a href="{{ route('admin.configs.create') }}" class="btn bg-gradient-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm cấu hình
                    </a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase  text-xxs font-weight-bolder ">Khóa</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Giá trị</th>
                                    <th class="text-uppercase  text-xxs font-weight-bolder  ps-2">Mô tả</th>
                                    <th class=" "></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($configs as $config)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $config->key }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ Str::limit($config->value, 50) }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs  mb-0">
                                            {{ $config->description ?? 'Không có mô tả' }}
                                        </p>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('admin.configs.edit', $config->id) }}" class=" font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit config">
                                            <i class="fas fa-edit text-success"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
