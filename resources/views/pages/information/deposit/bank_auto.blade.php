@extends('layouts.information')

@section('info_title', 'Nạp cám tự động')
@section('info_description', 'Nạp cám tự động qua Casso trên ' . request()->getHost())
@section('info_keyword', 'nạp cám, thanh toán tự động, Casso, ' . request()->getHost())
@section('info_section_title', 'Nạp cám tự động')
@section('info_section_desc', 'Nạp cám tự động qua Casso với nhiều ưu đãi hấp dẫn')

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

        /* Deposit table styles */
        .deposit-table .table {
            margin-bottom: 0;
        }

        .deposit-table .table th {
            border-top: none;
            border-bottom: 2px solid #495057;
            font-size: 0.85rem;
            padding: 0.5rem;
        }

        .deposit-table .table td {
            border-top: 1px solid #495057;
            padding: 0.4rem 0.5rem;
            font-size: 0.8rem;
        }

        .deposit-table .table-primary {
            background-color: rgba(13, 110, 253, 0.2) !important;
        }

        .deposit-table .table-primary td {
            border-color: rgba(13, 110, 253, 0.3);
        }
    </style>
@endpush

@section('info_content')

    <div class="deposit-tabs d-flex mb-4">
        <a href="{{ route('user.deposit') }}" class="deposit-tab ">
            <i class="fas fa-university me-2"></i>Bank
        </a>
        <a href="{{ route('user.bank.auto.deposit') }}" class="deposit-tab active">
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
        <div class="row">
            <div class="col-lg-8">
                <!-- Bank Auto Info Section -->
                <div class="card-info-section mb-3">
                    <div class="deposit-card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-robot me-2"></i>Nạp cám tự động qua Casso
                        </h5>
                        <p class="text-muted mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Thanh toán tự động, nhận cám ngay lập tức với nhiều ưu đãi hấp dẫn
                        </p>
                    </div>
                </div>

                <!-- Bank Auto Form -->
                <div class="">
                    <div class="card-body">
                        <form id="bankAutoDepositForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3">
                                    <i class="fas fa-university me-2"></i>Chọn ngân hàng
                                </label>
                                <div class="row">
                                    @foreach ($banks as $bank)
                                        <div class="col-6">
                                            <div class="bank-option" data-bank-id="{{ $bank->id }}">
                                                <div class="d-flex align-items-center">
                                                    @if ($bank->logo)
                                                        <img src="{{ Storage::url($bank->logo) }}" alt="{{ $bank->name }}"
                                                            class="bank-logo me-3">
                                                    @else
                                                        <div
                                                            class="bank-logo me-3 d-flex align-items-center justify-content-center bg-light">
                                                            <i class="fas fa-university fa-2x"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1">{{ $bank->name }}</h6>
                                                        <div class="small text-muted">{{ $bank->code }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="bank_id" id="bankId" required>
                                <div class="invalid-feedback bank-error">Vui lòng chọn ngân hàng</div>
                            </div>

                            <div class="deposit-amount-container">
                                <label for="amount" class="form-label fw-bold mb-3">
                                    <i class="fas fa-money-bill-wave me-2"></i>Nhập số tiền muốn nạp (VNĐ)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control deposit-amount-input" id="amount"
                                        name="amount" value="{{ old('amount', 100000) }}"
                                        data-raw="{{ old('amount', 100000) }}"
                                        min="50000" step="10000">

                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="form-text">Số tiền tối thiểu: 50.000 VNĐ, phải là bội số của 10.000</div>
                                <div class="invalid-feedback amount-error">Vui lòng nhập số tiền hợp lệ</div>

                                <!-- Coin Preview với Bonus -->
                                <div class="deposit-coin-preview mt-4">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="small text-white opacity-75">Cám nhận được:</div>
                                            <div class="coin-preview-value">
                                                <i class="fas fa-coins me-2"></i> 
                                                <span id="totalCoinsPreview">0</span>
                                            </div>
                                            <div class="coin-breakdown mt-2">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-white opacity-75">
                                                            <i class="fas fa-coins me-1"></i>Cám cơ bản: 
                                                            <span id="baseCoinsPreview">0</span>
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-success">
                                                            <i class="fas fa-gift me-1"></i>Bonus: 
                                                            <span id="bonusCoinsPreview">0</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bonus Information -->
                                <div class="bonus-info mt-3">
                                    <div class="alert alert-success border-0">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-gift me-2"></i>Chương trình khuyến mãi
                                        </h6>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <p class="small mb-1">
                                                    <strong>Nạp {{ number_format($bonusBaseAmount) }}đ:</strong> 
                                                    Tặng {{ number_format($bonusBaseCam) }} cám
                                                </p>
                                                <p class="small mb-1">
                                                    <strong>Nạp {{ number_format($bonusDoubleAmount) }}đ:</strong> 
                                                    Tặng {{ number_format($bonusDoubleCam) }} cám
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="small mb-1">
                                                    <strong>Nạp càng nhiều:</strong> Bonus càng cao
                                                </p>
                                                <p class="small mb-0">
                                                    <strong>Ví dụ:</strong> 1 triệu = ~15.100 cám
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="proceedToPaymentBtn" class="btn payment-btn w-100">
                                    <i class="fas fa-robot"></i> Thanh toán tự động với Casso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="coins-panel">
                    <div class="coins-balance">
                        <i class="fas fa-coins coins-icon"></i> {{ number_format(Auth::user()->coins ?? 0) }}
                    </div>
                    <div class="coins-label">Số cám hiện có trong tài khoản</div>

                    <!-- Bảng mức nạp tiền -->
                    <div class="deposit-table mt-4">
                        <h6 class="text-white mb-3">
                            <i class="fas fa-table me-2"></i>Bảng mức nạp tiền
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">Số tiền</th>
                                        <th class="text-center">Cám cơ bản</th>
                                        <th class="text-center">Cám bonus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $amounts = [50000, 100000, 150000, 200000, 250000, 300000, 400000, 500000, 1000000];
                                    @endphp
                                    @foreach($amounts as $amount)
                                        @php
                                            // Tính toán cám cơ bản
                                            $feeAmount = ($amount * $coinBankAutoPercent) / 100;
                                            $amountAfterFee = $amount - $feeAmount;
                                            $baseCoins = floor($amountAfterFee / $coinExchangeRate);
                                            
                                            // Tính toán bonus
                                            $bonusCoins = 0;
                                            if ($amount >= $bonusBaseAmount) {
                                                $bonusCoins += $bonusBaseCam;
                                            }
                                            if ($amount >= $bonusDoubleAmount) {
                                                $bonusCoins += $bonusDoubleCam;
                                            }
                                            if ($amount > $bonusDoubleAmount) {
                                                $excessAmount = $amount - $bonusDoubleAmount;
                                                $excessBonus = floor($excessAmount / 100000) * 50;
                                                $bonusCoins += $excessBonus;
                                            }
                                            
                                            $totalCoins = $baseCoins + $bonusCoins;
                                        @endphp
                                        <tr class="{{ $amount == 100000 ? 'table-primary' : '' }}">
                                            <td class="text-center fw-bold">{{ number_format($amount) }}đ</td>
                                            <td class="text-center">{{ number_format($baseCoins) }}</td>
                                            <td class="text-center text-success">{{ number_format($bonusCoins) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Tổng cám = Cám cơ bản + Cám bonus
                            </small>
                        </div>
                    </div>

                    <!-- Casso Info -->
                    <div class="casso-info mt-4">
                        <div class="alert alert-info border-0">
                            <h6 class="alert-heading">
                                <i class="fas fa-shield-alt me-2"></i>Được bảo vệ bởi Casso
                            </h6>
                            <p class="small mb-0">
                                Casso là đối tác tin cậy với hơn 1000+ khách hàng và 90% sự hài lòng
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@once
    @push('info_scripts')
        <script>
            $(document).ready(function() {
                // Global variables
                window.coinExchangeRate = {{ $coinExchangeRate }};
                window.coinBankAutoPercent = {{ $coinBankAutoPercent }};
                window.bonusBaseAmount = {{ $bonusBaseAmount }};
                window.bonusBaseCam = {{ $bonusBaseCam }};
                window.bonusDoubleAmount = {{ $bonusDoubleAmount }};
                window.bonusDoubleCam = {{ $bonusDoubleCam }};

                // Auto select first bank
                const firstBank = $('.bank-option').first();
                if (firstBank.length > 0) {
                    firstBank.addClass('selected');
                    $('#bankId').val(firstBank.data('bank-id'));
                    $('.bank-error').hide();
                }

                // Bank selection
                $(document).on('click', '.bank-option', function() {
                    $('.bank-option').removeClass('selected');
                    $(this).addClass('selected');
                    $('#bankId').val($(this).data('bank-id'));
                    $('.bank-error').hide();

                    $(this).animate({
                        opacity: 0.7
                    }, 100).animate({
                        opacity: 1
                    }, 100);
                });

                // Amount input formatting
                $('.deposit-amount-input').on('input', function() {
                    try {
                        const input = $(this);
                        const currentValue = input.val();
                        
                        if (currentValue && currentValue.trim() !== '') {
                            const formatted = formatVndCurrency(currentValue);
                            if (formatted !== currentValue) {
                                input.val(formatted);
                            }
                            
                            const rawValue = parseVndCurrency(formatted);
                            input.data('raw', rawValue);
                            updateCoinPreview();
                        } else {
                            input.data('raw', 0);
                            updateCoinPreview();
                        }
                    } catch (error) {
                        console.error('Error in input handler:', error);
                        input.data('raw', 0);
                        updateCoinPreview();
                    }
                });

                $('.deposit-amount-input').on('blur', function() {
                    try {
                        const input = $(this);
                        let rawValue = input.data('raw') || 0;
                        
                        // Round to nearest 10,000
                        if (rawValue > 0) {
                            rawValue = Math.round(rawValue / 10000) * 10000;
                            if (rawValue < 50000) rawValue = 50000;
                            
                            const formatted = formatVndCurrency(rawValue.toString());
                            input.val(formatted);
                            input.data('raw', rawValue);
                            updateCoinPreview();
                        }
                    } catch (error) {
                        console.error('Error in blur handler:', error);
                    }
                });

                // Calculate coin preview with bonus
                function updateCoinPreview() {
                    try {
                        const amount = parseInt($('#amount').data('raw')) || 0;

                        if (amount > 0) {
                            // Calculate base coins
                            const feeAmount = (amount * window.coinBankAutoPercent) / 100;
                            const amountAfterFee = amount - feeAmount;
                            const baseCoins = Math.floor(amountAfterFee / window.coinExchangeRate);

                            // Calculate bonus
                            let bonusCoins = 0;
                            
                            if (amount >= window.bonusBaseAmount) {
                                bonusCoins += window.bonusBaseCam;
                            }
                            
                            if (amount >= window.bonusDoubleAmount) {
                                bonusCoins += window.bonusDoubleCam;
                            }
                            
                            // Progressive bonus for amounts above double threshold
                            if (amount > window.bonusDoubleAmount) {
                                const excessAmount = amount - window.bonusDoubleAmount;
                                const excessBonus = Math.floor(excessAmount / 100000) * 50; // 50 coins per 100k
                                bonusCoins += excessBonus;
                            }

                            const totalCoins = baseCoins + bonusCoins;

                            // Update UI
                            $('#baseCoinsPreview').text(baseCoins.toLocaleString('vi-VN'));
                            $('#bonusCoinsPreview').text(bonusCoins.toLocaleString('vi-VN'));
                            $('#totalCoinsPreview').text(totalCoins.toLocaleString('vi-VN'));
                        } else {
                            $('#baseCoinsPreview').text('0');
                            $('#bonusCoinsPreview').text('0');
                            $('#totalCoinsPreview').text('0');
                        }
                    } catch (error) {
                        console.error("Error updating coin preview:", error);
                        $('#baseCoinsPreview').text('0');
                        $('#bonusCoinsPreview').text('0');
                        $('#totalCoinsPreview').text('0');
                    }
                }

                // Initialize coin preview
                updateCoinPreview();

                // Proceed to payment
                $('#proceedToPaymentBtn').off('click').on('click', function() {
                    let valid = true;

                    if (!$('#bankId').val()) {
                        $('.bank-error').show();
                        valid = false;
                    } else {
                        $('.bank-error').hide();
                    }

                    const amount = parseInt($('#amount').data('raw')) || 0;
                    if (amount < 50000) {
                        $('.amount-error').show().text('Số tiền tối thiểu là 50.000 VNĐ');
                        valid = false;
                    } else if (amount % 10000 !== 0) {
                        $('.amount-error').show().text('Số tiền phải là bội số của 10.000 VNĐ');
                        valid = false;
                    } else {
                        $('.amount-error').hide();
                    }

                    if (valid) {
                        const bankId = $('#bankId').val();

                        $(this).prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

                        $.ajax({
                            url: '{{ route('user.bank.auto.deposit.store') }}',
                            type: 'POST',
                            data: {
                                bank_id: bankId,
                                amount: amount,
                                _token: $('input[name="_token"]').val()
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Redirect to Casso payment
                                    window.location.href = response.payment_url;
                                } else {
                                    showToast('Có lỗi xảy ra: ' + (response.message || 'Không thể xử lý thanh toán'), 'error');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Đã xảy ra lỗi khi xử lý yêu cầu';

                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.errors) {
                                        const errors = xhr.responseJSON.errors;
                                        const firstError = Object.values(errors)[0];
                                        errorMessage = firstError[0] || errorMessage;
                                    } else if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                }

                                showToast(errorMessage, 'error');
                            },
                            complete: function() {
                                $('#proceedToPaymentBtn').prop('disabled', false).html(
                                    '<i class="fas fa-robot"></i> Thanh toán tự động với Casso');
                            }
                        });
                    }
                });

                // Utility functions
                function formatVndCurrency(value) {
                    try {
                        if (!value || value === '' || value === null || value === undefined) return '';
                        const number = value.toString().replace(/\D/g, '');
                        if (number === '' || number === '0') return '';
                        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    } catch (error) {
                        console.error('Error in formatVndCurrency:', error);
                        return '';
                    }
                }

                function parseVndCurrency(formatted) {
                    try {
                        if (!formatted || formatted === '' || formatted === null || formatted === undefined) return 0;
                        return parseInt(formatted.toString().replace(/\./g, '')) || 0;
                    } catch (error) {
                        console.error('Error in parseVndCurrency:', error);
                        return 0;
                    }
                }

                // Initialize with default values
                $('.deposit-amount-input').each(function() {
                    const input = $(this);
                    let raw = input.data('raw');
                    if (raw) {
                        raw = Math.round(raw / 10000) * 10000;
                        if (raw < 50000) raw = 50000;
                        input.data('raw', raw);
                        input.val(formatVndCurrency(raw));
                    }
                });

                // Initial calculation
                updateCoinPreview();
            });
        </script>
    @endpush
@endonce
