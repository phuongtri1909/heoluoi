@extends('layouts.app')

@section('title', 'Tài khoản bị khóa')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-ban fa-4x text-danger"></i>
                    </div>
                    <h2 class="mb-4 text-danger fw-bold">Tài khoản của bạn đã bị khóa</h2>
                    <div class="alert alert-warning mb-4">
                        <p class="mb-2 fw-semibold">{{ $message }}</p>
                        <p class="mb-0">
                            Nếu đây là sự cố ngoài ý muốn, vui lòng liên hệ 
                            <a href="https://www.facebook.com/HeoLuoiChamDocTruyen/" target="_blank" rel="noopener noreferrer" class="fw-bold text-decoration-underline">
                                fan page
                            </a> 
                            để được hỗ trợ sớm nhất.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

