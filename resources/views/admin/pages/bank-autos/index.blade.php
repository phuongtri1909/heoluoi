@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Danh sách ngân hàng tự động</h5>
                        </div>
                        <a href="{{ route('admin.bank-autos.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus"></i> Thêm ngân hàng tự động
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-xxs font-weight-bolder">ID</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder ps-2">Logo</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Tên ngân hàng</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Mã</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Số tài khoản</th>
                                    <th class="text-uppercase text-xxs font-weight-bolder">Chủ tài khoản</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">QR Code</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Trạng thái</th>
                                    <th class="text-center text-uppercase text-xxs font-weight-bolder">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bankAutos as $bankAuto)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $bankAuto->id }}</p>
                                        </td>
                                        <td>
                                            @if($bankAuto->logo)
                                                <img src="{{ Storage::url($bankAuto->logo) }}" alt="{{ $bankAuto->name }}" class="img-fluid" style="max-height: 40px;">
                                            @else
                                                <div class="text-center bg-light p-2 rounded">
                                                    <i class="fas fa-robot text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bankAuto->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bankAuto->code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bankAuto->account_number }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bankAuto->account_name }}</p>
                                        </td>
                                        <td class="text-center">
                                            @if($bankAuto->qr_code)
                                                <a href="{{ Storage::url($bankAuto->qr_code) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-qrcode"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Không có</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($bankAuto->status)
                                                <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Vô hiệu</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a title="Xem chi tiết" href="{{ route('admin.bank-autos.show', $bankAuto->id) }}" class="text-info action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a title="Sửa" href="{{ route('admin.bank-autos.edit', $bankAuto->id) }}" class="action-icon edit-icon" title="Sửa">
                                                        <i class="fas fa-pencil-alt text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2">
                                                    @include('admin.pages.components.delete-form', [
                                                        'id' => $bankAuto->id,
                                                        'route' => route('admin.bank-autos.destroy', $bankAuto->id)
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$bankAutos" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
