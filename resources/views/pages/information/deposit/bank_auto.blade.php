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

        .copy-button {
            padding: 2px 6px;
            font-size: 12px;
        }

        .payment-qr-code {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
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
                <div id="depositContainer">
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
                                    <input type="text" class="form-control deposit-amount-input" id="amount"
                                        name="amount"                                         value="{{ old('amount', '2.000') }}"
                                        data-raw="{{ old('amount', 2000) }}"
                                        placeholder="Nhập số tiền (ví dụ: 100.000)"
                                        pattern="[0-9.,]+"
                                        inputmode="numeric">

                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="form-text">Số tiền tối thiểu: 2.000 VNĐ (tạm thời tắt validation bội số để test)</div>
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
                                        $amounts = [2000, 10000, 50000, 100000, 200000, 300000, 500000, 1000000];
                                    @endphp
                                    @foreach($amounts as $amount)
                                        @php
                                            // Tính toán cám cơ bản
                                            $feeAmount = ($amount * $coinBankAutoPercent) / 100;
                                            $amountAfterFee = $amount - $feeAmount;
                                            $baseCoins = floor($amountAfterFee / $coinExchangeRate);
                                            
                                            // Tính toán bonus theo công thức hàm mũ
                                            $bonusCoins = 0;
                                            if ($amount >= $bonusBaseAmount) {
                                                // Tính số mũ b
                                                $ratioAmount = $bonusDoubleAmount / $bonusBaseAmount; // 300000/100000 = 3
                                                $ratioBonus = $bonusDoubleCam / $bonusBaseCam; // 1000/300 = 3.333...
                                                $b = log($ratioBonus) / log($ratioAmount); // ≈ 1.096
                                                
                                                // Tính hệ số a
                                                $a = $bonusBaseCam / pow($bonusBaseAmount, $b);
                                                
                                                // Tính bonus theo công thức: bonus = a * (amount)^b
                                                $bonusCoins = floor($a * pow($amount, $b));
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
                            // Remove all non-numeric characters except dots
                            const cleanValue = currentValue.replace(/[^\d.]/g, '');
                            
                            // Format with dots
                            const formatted = formatVndCurrency(cleanValue);
                            
                            // Only update if different to avoid cursor jumping
                            if (formatted !== currentValue) {
                                const cursorPos = input.prop('selectionStart');
                                input.val(formatted);
                                
                                // Try to maintain cursor position
                                setTimeout(() => {
                                    const newLength = formatted.length;
                                    const newPos = Math.min(cursorPos + (formatted.length - currentValue.length), newLength);
                                    input.prop('selectionStart', newPos);
                                    input.prop('selectionEnd', newPos);
                                }, 0);
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
                            if (rawValue < 2000) rawValue = 2000;
                            
                            const formatted = formatVndCurrency(rawValue.toString());
                            input.val(formatted);
                            input.data('raw', rawValue);
                            updateCoinPreview();
                        } else {
                            // If empty or invalid, set to minimum
                            input.val('2.000');
                            input.data('raw', 2000);
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

                            // Calculate bonus theo công thức hàm mũ
                            // Công thức: bonus = a * (amount)^b
                            let bonusCoins = 0;
                            
                            if (amount >= window.bonusBaseAmount) {
                                // Tính số mũ b
                                // b = log(300000/100000)(1000/300) = log3(3.333...) ≈ 1.096
                                const ratioAmount = window.bonusDoubleAmount / window.bonusBaseAmount; // 300000/100000 = 3
                                const ratioBonus = window.bonusDoubleCam / window.bonusBaseCam; // 1000/300 = 3.333...
                                const b = Math.log(ratioBonus) / Math.log(ratioAmount); // ≈ 1.096
                                
                                // Tính hệ số a
                                // a = 300/(100000)^b
                                const a = window.bonusBaseCam / Math.pow(window.bonusBaseAmount, b);
                                
                                // Tính bonus theo công thức: bonus = a * (amount)^b
                                bonusCoins = Math.floor(a * Math.pow(amount, b));
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
                    
                    // Debug logging
                    console.log('Validation check:', {
                        amount: amount,
                        bankId: $('#bankId').val(),
                        amountRaw: $('#amount').data('raw'),
                        amountMod10000: amount % 10000
                    });
                    
                    if (amount < 2000) {
                        $('.amount-error').show().text('Số tiền tối thiểu là 2.000 VNĐ');
                        valid = false;
                    } 
                    // Tạm thời tắt validation bội số của 10.000 để test
                    // else if (amount % 10000 !== 0) {
                    //     $('.amount-error').show().text('Số tiền phải là bội số của 10.000 VNĐ (ví dụ: 10.000, 20.000, 50.000, 100.000, 1.000.000...)');
                    //     valid = false;
                    // } 
                    else if (amount > 99999999) {
                        $('.amount-error').show().text('Số tiền tối đa là 99.999.999 VNĐ');
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
                                    showBankTransferInfo(response);
                                } else {
                                    showToast('Có lỗi xảy ra: ' + (response.message || 'Không thể xử lý thanh toán'), 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error Details:', {
                                    status: xhr.status,
                                    statusText: xhr.statusText,
                                    responseText: xhr.responseText,
                                    responseJSON: xhr.responseJSON,
                                    error: error
                                });
                                
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
                        if (raw < 2000) raw = 2000;
                        input.data('raw', raw);
                        input.val(formatVndCurrency(raw));
                    }
                });

                // Initial calculation
                updateCoinPreview();
            });

            // Function to show bank transfer info
            function showBankTransferInfo(response) {
                const bankInfo = response.bank_info;
                const transactionCode = response.transaction_code;
                const amount = response.amount;
                const coins = response.coins;

                const transferInfoHtml = `
                    <div class="bank-transfer-info">
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle me-2"></i>Tạo giao dịch thành công!</h5>
                            <p class="mb-3">Vui lòng chuyển khoản theo thông tin bên dưới:</p>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-university me-2"></i>Thông tin chuyển khoản
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ngân hàng:</label>
                                            <div class="fw-bold">${bankInfo.name}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Số tài khoản:</label>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold text-primary payment-info-value" tabindex="0" onclick="this.focus();this.select()" onfocus="this.select()">${bankInfo.account_number}</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 copy-button" onclick="copyToClipboard('${bankInfo.account_number}')" title="Sao chép số tài khoản">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tên chủ tài khoản:</label>
                                            <div class="fw-bold">${bankInfo.account_name}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số tiền:</label>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold text-success payment-info-value" tabindex="0" onclick="this.focus();this.select()" onfocus="this.select()">${amount.toLocaleString('vi-VN')}</span>
                                                <span class="ms-1 fw-bold">VNĐ</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 copy-button" onclick="copyToClipboard('${amount.toLocaleString('vi-VN')}')" title="Sao chép số tiền">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nội dung chuyển khoản:</label>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold text-warning payment-info-value" tabindex="0" onclick="this.focus();this.select()" onfocus="this.select()">${transactionCode}</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2 copy-button" onclick="copyToClipboard('${transactionCode}')" title="Sao chép nội dung chuyển khoản">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cám nhận được:</label>
                                            <div class="fw-bold text-info">${coins.toLocaleString('vi-VN')} cám</div>
                                        </div>
                                    </div>
                                </div>
                                
                                ${bankInfo.qr_code ? `
                                <div class="text-center mb-3">
                                    <div class="payment-qr-code mb-3">
                                        <img src="${bankInfo.qr_code}" alt="QR Code" class="img-fluid" style="max-height: 200px;">
                                    </div>
                                    <p class="text-muted">Quét mã QR để thực hiện thanh toán</p>
                                </div>
                                ` : ''}
                                
                                <div class="alert alert-warning mt-3">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Lưu ý quan trọng:</h6>
                                    <ul class="mb-0">
                                        <li>Nội dung chuyển khoản phải chính xác: <strong>${transactionCode}</strong></li>
                                        <li>Số tiền chuyển khoản phải đúng: <strong>${amount.toLocaleString('vi-VN')} VNĐ</strong></li>
                                        <li>Sau khi chuyển khoản, hệ thống sẽ tự động cộng cám trong vòng 1-5 phút</li>
                                        <li>Nếu không nhận được cám sau 10 phút, vui lòng liên hệ hỗ trợ</li>
                                    </ul>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                    <button class="btn btn-primary" onclick="location.reload()">
                                        <i class="fas fa-plus me-2"></i>Tạo giao dịch mới
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Thay thế nội dung form
                $('#depositContainer').html(transferInfoHtml);
                
                // Start SSE connection để listen updates
                startSSEConnection(transactionCode);
            }

            // Function to copy text to clipboard
            function copyToClipboard(text) {
                const $button = event.target.closest('.copy-button');
                const originalText = $button.innerHTML;

                // Hiển thị trạng thái đang xử lý
                $button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Phương pháp 1: Clipboard API (chỉ hoạt động trên HTTPS hoặc localhost)
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text)
                        .then(() => {
                            showCopySuccess($button, originalText);
                        })
                        .catch(() => {
                            // Nếu phương pháp 1 thất bại, thử phương pháp 2
                            copyUsingExecCommand(text, $button, originalText);
                        });
                }
                // Phương pháp 2: document.execCommand (hỗ trợ cũ)
                else {
                    copyUsingExecCommand(text, $button, originalText);
                }
            }

            // Phương pháp sao chép bằng execCommand
            function copyUsingExecCommand(text, $button, originalText) {
                try {
                    // Tạo phần tử input tạm thời
                    const $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(text).select();

                    // Thực hiện lệnh sao chép
                    const successful = document.execCommand('copy');

                    // Dọn dẹp
                    $temp.remove();

                    if (successful) {
                        showCopySuccess($button, originalText);
                    } else {
                        showCopyFailure($button, originalText);
                    }
                } catch (err) {
                    showCopyFailure($button, originalText);
                }
            }

            // Hiển thị thành công
            function showCopySuccess($button, originalText) {
                $button.innerHTML = '<i class="fas fa-check"></i>';
                
                // Khôi phục nút sau 1 giây
                setTimeout(() => $button.innerHTML = originalText, 1000);
            }

            // Hiển thị thất bại
            function showCopyFailure($button, originalText) {
                $button.innerHTML = '<i class="fas fa-times"></i>';
                
                // Khôi phục nút sau 1 giây
                setTimeout(() => $button.innerHTML = originalText, 1000);
            }
            
            // SSE để listen transaction updates
            let currentTransactionCode = null;
            let sseConnection = null;
            
            // Start SSE connection khi có transaction code
            function startSSEConnection(transactionCode) {
                if (sseConnection) {
                    sseConnection.close();
                }
                
                currentTransactionCode = transactionCode;
                const sseUrl = '{{ route("bank.auto.sse") }}?transaction_code=' + encodeURIComponent(transactionCode);
                
                sseConnection = new EventSource(sseUrl);
                
                sseConnection.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.type === 'close') {
                            sseConnection.close();
                            return;
                        }
                        
                        if (data.status === 'success') {
                            // Hiển thị thông báo thành công
                            showSuccessNotification(data);
                            
                            // Reload trang sau 2 giây
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                            
                            // Close SSE connection
                            sseConnection.close();
                        }
                    } catch (error) {
                        console.error('SSE parsing error:', error);
                    }
                };
                
                sseConnection.onerror = function(event) {
                    console.error('SSE connection error:', event);
                    // Retry connection sau 5 giây
                    setTimeout(() => {
                        if (currentTransactionCode) {
                            startSSEConnection(currentTransactionCode);
                        }
                    }, 5000);
                };
            }
            
            // Hiển thị thông báo thành công
            function showSuccessNotification(data) {
                // Tạo toast notification
                const toast = `
                    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>
                                Giao dịch thành công! Bạn đã nhận được ${data.total_coins.toLocaleString('vi-VN')} cám.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                // Thêm toast vào container
                if (!$('#toast-container').length) {
                    $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
                }
                
                $('#toast-container').append(toast);
                
                // Show toast
                const toastElement = $('#toast-container .toast').last();
                const toastInstance = new bootstrap.Toast(toastElement[0]);
                toastInstance.show();
            }
        </script>
    @endpush
@endonce
