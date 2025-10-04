@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Chi tiết ngân hàng tự động: {{ $bankAuto->name }}</h5>
                        </div>
                        <a href="{{ route('admin.bank-autos.index') }}" class="btn bg-gradient-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Thông tin cơ bản</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="150">ID</th>
                                            <td>{{ $bankAuto->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tên ngân hàng</th>
                                            <td>{{ $bankAuto->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mã ngân hàng</th>
                                            <td>{{ $bankAuto->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Số tài khoản Casso</th>
                                            <td>{{ $bankAuto->account_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Chủ tài khoản Casso</th>
                                            <td>{{ $bankAuto->account_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Trạng thái</th>
                                            <td>
                                                @if($bankAuto->status)
                                                    <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">Vô hiệu</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Ngày tạo</th>
                                            <td>{{ $bankAuto->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cập nhật lần cuối</th>
                                            <td>{{ $bankAuto->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card shadow-sm mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Logo</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            @if($bankAuto->logo)
                                                <img src="{{ Storage::url($bankAuto->logo) }}" alt="{{ $bankAuto->name }}" class="img-fluid mb-3" style="max-height: 150px;">
                                                <div>
                                                    <a href="{{ Storage::url($bankAuto->logo) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-external-link-alt mr-1"></i> Xem ảnh gốc
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-5 bg-light rounded">
                                                    <i class="fas fa-robot fa-4x mb-3 text-muted"></i>
                                                    <p>Chưa có logo</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Mã QR</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            @if($bankAuto->qr_code)
                                                <img src="{{ Storage::url($bankAuto->qr_code) }}" alt="QR Code" class="img-fluid mb-3" style="max-height: 200px;">
                                                <div>
                                                    <a href="{{ Storage::url($bankAuto->qr_code) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-external-link-alt mr-1"></i> Xem ảnh gốc
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-5 bg-light rounded">
                                                    <i class="fas fa-qrcode fa-4x mb-3 text-muted"></i>
                                                    <p>Chưa có mã QR</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
