@extends('layouts.information')

@section('info_title', 'Nạp cám thành công')
@section('info_description', 'Giao dịch nạp cám tự động đã thành công')
@section('info_keyword', 'nạp cám thành công, bank auto')
@section('info_section_title', 'Nạp cám thành công')
@section('info_section_desc', 'Giao dịch nạp cám tự động đã được xử lý thành công')

@section('info_content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h3 class="text-success mb-3">Nạp cám thành công!</h3>
                    
                    <p class="text-muted mb-4">
                        Giao dịch nạp cám tự động của bạn đã được xử lý thành công.<br>
                        Cám đã được cộng vào tài khoản của bạn.
                    </p>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Thông tin giao dịch
                        </h6>
                        <p class="mb-0">
                            Bạn có thể kiểm tra số cám hiện tại trong tài khoản và lịch sử giao dịch 
                            trong phần <strong>Lịch sử cám</strong>.
                        </p>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ route('user.coin-history') }}" class="btn btn-primary">
                            <i class="fas fa-history me-2"></i>Xem lịch sử cám
                        </a>
                        <a href="{{ route('user.bank.auto.deposit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Nạp thêm cám
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
