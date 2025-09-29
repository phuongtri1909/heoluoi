@extends('admin.layouts.app')

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Danh sách cấu hình hệ thống</h6>
                    <a href="{{ route('admin.configs.create') }}" class="btn btn-primary btn-sm">
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
                                        <form action="{{ route('admin.configs.destroy', $config->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class=" font-weight-bold text-xs border-0 bg-transparent p-0" onclick="return confirm('Bạn có chắc chắn muốn xóa cấu hình này?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
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
