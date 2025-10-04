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
            width: 40px !important;
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
            width: 30px !important;
            height: 30px !important;
        }
        
        .action-icon {
            padding: 0.25rem !important;
        }
        
        /* Hide some columns on very small screens */
        .table th:nth-child(4),
        .table td:nth-child(4),
        .table th:nth-child(5),
        .table td:nth-child(5) {
            display: none;
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
                            <h5 class="mb-0">Danh sách ngân hàng</h5>
                        </div>
                        <a href="{{ route('admin.banks.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus"></i> Thêm ngân hàng
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
                                @foreach ($banks as $bank)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $bank->id }}</p>
                                        </td>
                                        <td>
                                            @if($bank->logo)
                                                <img src="{{ Storage::url($bank->logo) }}" alt="{{ $bank->name }}" class="img-fluid" style="max-height: 40px;">
                                            @else
                                                <div class="text-center bg-light p-2 rounded">
                                                    <i class="fas fa-university text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bank->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bank->code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bank->account_number }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $bank->account_name }}</p>
                                        </td>
                                        <td class="text-center">
                                            @if($bank->qr_code)
                                                <a href="{{ Storage::url($bank->qr_code) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-qrcode"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Không có</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($bank->status)
                                                <span class="badge badge-sm bg-gradient-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Vô hiệu</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-wrap justify-content-center">
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a title="Xem chi tiết" href="{{ route('admin.banks.show', $bank->id) }}" class="text-info action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2 me-2">
                                                    <a title="Sửa" href="{{ route('admin.banks.edit', $bank->id) }}" class="action-icon edit-icon" title="Sửa">
                                                        <i class="fas fa-pencil-alt text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column align-items-center mb-2">
                                                    @include('admin.pages.components.delete-form', [
                                                        'id' => $bank->id,
                                                        'route' => route('admin.banks.destroy', $bank->id)
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 pt-4">
                            <x-pagination :paginator="$banks" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 