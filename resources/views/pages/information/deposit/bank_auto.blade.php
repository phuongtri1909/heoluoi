@extends('layouts.information')

@section('info_title', 'Nạp cám')
@section('info_description', 'Nạp cám vào tài khoản của bạn trên ' . request()->getHost())
@section('info_keyword', 'nạp cám, thanh toán, ' . request()->getHost())
@section('info_section_title', 'Nạp cám')
@section('info_section_desc', 'Nạp cám vào tài khoản để sử dụng các dịch vụ cao cấp')

@push('styles')
    <style>
        /* Bank specific styles */
        .bank-logo {
            width: 80px;
            height: 40px;
            object-fit: contain;
        }

        .bank-info {
            font-size: 14px;
            color: #555;
        }

        /* Bank deposit specific styles */
        .transaction-image {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            border-radius: 5px;
        }

        .status-pending {
            color: #ff9800;
        }

        .status-approved {
            color: #4caf50;
        }

        .status-rejected {
            color: #f44336;
        }

        .deposit-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
        }

        /* Payment info value interactions */
        .payment-info-value {
            position: relative;
            user-select: all;
            cursor: text;
            padding: 3px 5px;
            border-radius: 3px;
            transition: background-color 0.2s;
        }

        .payment-info-value:hover {
            background-color: rgba(var(--primary-rgb), 0.05);
        }

        .payment-info-value:focus {
            background-color: rgba(var(--primary-rgb), 0.1);
            outline: none;
        }

        /* Bank specific reason modal */
        .reason-content {
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
        }

        #reasonText {
            white-space: pre-line;
            color: #444;
            font-size: 15px;
        }

        .show-reason-btn {
            cursor: pointer;
        }

        .show-reason-btn:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('info_content')

    <div class="deposit-tabs d-flex mb-4">
        <a href="{{ route('user.deposit') }}" class="deposit-tab ">
            <i class="fas fa-university me-2"></i>Bank
        </a>
        <a href="" class="deposit-tab active">
            <i class="fas fa-university me-2"></i>Bank auto
        </a>
        <a href="{{ route('user.card.deposit') }}" class="deposit-tab">
            <i class="fas fa-credit-card me-2"></i>Card
        </a>
        <a href="{{ route('user.paypal.deposit') }}" class="deposit-tab">
            <i class="fab fa-paypal me-2"></i>PayPal
        </a>
    </div>

    <div class="deposit-container" id="depositContainer">
        
       
    </div>
@endsection

@once
    @push('info_scripts')
      
    @endpush
@endonce
