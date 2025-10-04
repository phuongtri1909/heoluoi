@extends('layouts.information')

@section('info_title', 'Giao dịch đã hủy')
@section('info_description', 'Giao dịch nạp cám tự động đã bị hủy')
@section('info_keyword', 'hủy giao dịch, bank auto')
@section('info_section_title', 'Giao dịch đã hủy')
@section('info_section_desc', 'Giao dịch nạp cám tự động đã bị hủy')

@section('info_content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="cancel-icon mb-4">
                        <i class="fas fa-times-circle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h3 class="text-warning mb-3">Giao dịch đã hủy</h3>
                    
                    <p class="text-muted mb-4">
                        Giao dịch nạp cám tự động của bạn đã bị hủy.<br>
                        Không có khoản phí nào được tính.
                    </p>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Thông tin
                        </h6>
                        <p class="mb-0">
                            Nếu bạn muốn nạp cám, vui lòng thử lại với phương thức thanh toán khác 
                            hoặc liên hệ hỗ trợ nếu gặp vấn đề.
                        </p>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ route('user.bank.auto.deposit') }}" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>Thử lại
                        </a>
                        <a href="{{ route('user.deposit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-university me-2"></i>Nạp bank thường
                        </a>
                        <a href="{{ route('user.card.deposit') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-credit-card me-2"></i>Nạp thẻ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
