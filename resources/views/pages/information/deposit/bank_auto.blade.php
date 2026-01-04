@extends('layouts.information')

@section('info_title', 'Nạp cám tự động')
@section('info_description', 'Nạp cám tự động qua trên ' . request()->getHost())
@section('info_keyword', 'nạp cám, thanh toán tự động, Casso, ' . request()->getHost())
@section('info_section_title', 'Nạp cám tự động')
@section('info_section_desc', 'Nạp cám tự động với nhiều ưu đãi hấp dẫn')

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

        .deposit-info-alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 12px 15px;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .deposit-info-alert:hover {
            background-color: #ffeaa7;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .deposit-info-alert i {
            color: #ff9800;
            font-size: 16px;
        }

        .deposit-info-alert a {
            text-decoration: none;
            font-weight: 600;
        }

        .deposit-info-alert a:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('info_content')

    <div class="deposit-tabs d-flex mb-4">
        <a href="{{ route('user.bank.auto.deposit') }}" class="deposit-tab active">
            <i class="fas fa-robot me-2"></i>Bank auto
        </a>
        {{-- <a href="{{ route('user.deposit') }}" class="deposit-tab">
            <i class="fas fa-university me-2"></i>Bank
        </a> --}}
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

                <div class="mb-2">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Thanh toán tự động, nhận cám ngay lập tức với nhiều ưu đãi hấp dẫn.
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <a class="color-7 fw-semibold" href="{{ route('guide.show') }}" target="_blank" rel="noopener noreferrer">Hướng dẫn nạp</a>
                    </p>
                    
                </div>


                <!-- Bank Auto Form -->
                <div id="depositContainer">
                    <div class="card-body">
                        <form id="bankAutoDepositForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-1">
                                    <i class="fas fa-university me-2"></i>Chọn ngân hàng
                                </label>
                                <br>
                                <span class="text-muted font-italic mb-3">Đây là ngân hàng thụ hưởng, không phải ngân hàng
                                    của bạn</span>
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
                                        name="amount" value="{{ old('amount', '50.000') }}"
                                        data-raw="{{ old('amount', 50000) }}" placeholder="Nhập số tiền (ví dụ: 100.000)"
                                        pattern="[0-9.,]+" inputmode="numeric">

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
                                                        <small class="text-white fw-bold">
                                                            <i class="fas fa-coins me-1"></i>Cám cộng:
                                                            <span id="baseCoinsPreview">0</span>
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="color-text fw-bold">
                                                            <i class="fas fa-gift me-1"></i>Cám tặng:
                                                            <span id="bonusCoinsPreview">0</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="proceedToPaymentBtn" class="btn payment-btn w-100">
                                    <i class="fas fa-robot"></i> Thanh toán tự động
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="coins-panel">

                    <!-- Bảng mức nạp tiền -->
                    <div class="deposit-table">
                        <h6 class="text-dark mb-3">
                            Mức quy định đổi cám hiên tại:
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">Số tiền</th>
                                        <th class="text-center">Cám cộng</th>
                                        <th class="text-center">Cám tặng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $amounts = [50000, 100000, 200000, 300000, 400000, 500000, 1000000];
                                    @endphp
                                    @foreach ($amounts as $amount)
                                        @php
                                            // Tính toán cám cơ bản
                                            $feeAmount = ($amount * $coinBankAutoPercent) / 100;
                                            $amountAfterFee = $amount - $feeAmount;
                                            $baseCoins = floor($amountAfterFee / $coinExchangeRate);

                                            // Tính toán bonus theo công thức hàm mũ
                                            $bonusCoins = 0;
                                            if ($amountAfterFee >= $bonusBaseAmount) {
                                                // Tính số mũ b
                                                $ratioAmount = $bonusDoubleAmount / $bonusBaseAmount; // 200000/100000 = 2
                                                $ratioBonus = $bonusDoubleCam / $bonusBaseCam; // 1000/300 = 3.333...
                                                $b = log($ratioBonus) / log($ratioAmount); // ≈ 1.737

                                                // Tính hệ số a
                                                $a = $bonusBaseCam / pow($bonusBaseAmount, $b);

                                                // Tính bonus theo công thức: bonus = a * (amountAfterFee)^b
                                                $bonusCoins = floor($a * pow($amountAfterFee, $b));
                                            }

                                            $totalCoins = $baseCoins + $bonusCoins;
                                        @endphp
                                        <tr class="{{ $amount == 100000 ? 'table-primary' : '' }}">
                                            <td class="text-center fw-bold">{{ number_format($amount) }}đ</td>
                                            <td class="text-center fw-bold">{{ number_format($baseCoins) }}</td>
                                            <td class="text-center color-7 fw-bold">+ {{ number_format($bonusCoins) }}</td>
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

@once
    @push('info_scripts')
        <script>
            $(document).ready(function() {
                const firstBank = $('.bank-option').first();
                if (firstBank.length > 0) {
                    firstBank.addClass('selected');
                    $('#bankId').val(firstBank.data('bank-id'));
                    $('.bank-error').hide();
                }

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

                $('.deposit-amount-input').on('input', function() {
                    try {
                        const input = $(this);
                        const currentValue = input.val();

                        if (currentValue && currentValue.trim() !== '') {
                            const cleanValue = currentValue.replace(/[^\d.]/g, '');

                            // Format with dots
                            const formatted = formatVndCurrency(cleanValue);

                            if (formatted !== currentValue) {
                                const cursorPos = input.prop('selectionStart');
                                input.val(formatted);

                                setTimeout(() => {
                                    const newLength = formatted.length;
                                    const newPos = Math.min(cursorPos + (formatted.length - currentValue
                                        .length), newLength);
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
                            input.val('2.000');
                            input.data('raw', 2000);
                            updateCoinPreview();
                        }
                    } catch (error) {
                        console.error('Error in blur handler:', error);
                    }
                });

                function updateCoinPreview() {
                    try {
                        const amount = parseInt($('#amount').data('raw')) || 0;

                        if (amount > 0 && amount >= 50000) {
                            $.ajax({
                                url: '{{ route('user.bank.auto.deposit.calculate') }}',
                                type: 'POST',
                                data: {
                                    amount: amount,
                                    _token: $('input[name="_token"]').val()
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        const data = response.data;
                                        $('#baseCoinsPreview').text(data.base_coins.toLocaleString(
                                        'vi-VN'));
                                        $('#bonusCoinsPreview').text(data.bonus_coins.toLocaleString(
                                            'vi-VN'));
                                        $('#totalCoinsPreview').text(data.total_coins.toLocaleString(
                                            'vi-VN'));
                                    } else {
                                        $('#baseCoinsPreview').text('0');
                                        $('#bonusCoinsPreview').text('0');
                                        $('#totalCoinsPreview').text('0');
                                    }
                                },
                                error: function() {
                                    $('#baseCoinsPreview').text('0');
                                    $('#bonusCoinsPreview').text('0');
                                    $('#totalCoinsPreview').text('0');
                                }
                            });
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

                updateCoinPreview();

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

                    if (amount < 50000) {
                        $('.amount-error').show().text('Số tiền tối thiểu là 50.000 VNĐ');
                        valid = false;
                    } else if (amount % 10000 !== 0) {
                        $('.amount-error').show().text(
                            'Số tiền phải là bội số của 10.000 VNĐ (ví dụ: 50.000, 100.000, 200.000, 500.000, 1.000.000...)'
                        );
                        valid = false;
                    } else if (amount > 99999999) {
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
                                    showToast('Có lỗi xảy ra: ' + (response.message ||
                                        'Không thể xử lý thanh toán'), 'error');
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

                updateCoinPreview();
            });

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
                                                        <img src="${bankInfo.qr_code}" alt="QR Code" class="img-fluid" style="max-height: 240px;">
                                                    </div>
                                                    <p class="text-muted">Quét mã QR để thực hiện thanh toán</p>
                                                    <p class="text-muted mb-0 deposit-info-alert">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Bạn đã chuyển khoản nhưng không thấy cộng cám? => liên hệ <a class="color-7 fw-semibold" href="https://www.facebook.com/profile.php?id=61572454674711" target="_blank" rel="noopener noreferrer">fan page</a> gửi bill và tài khoản của bạn để hỗ trợ.
                                                    </p>
                                                </div>
                                                ` : ''}
                                
                                <div class="alert alert-warning mt-3">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Lưu ý quan trọng:</h6>
                                    <ul class="mb-0">
                                        <li>Nội dung chuyển khoản phải chính xác: <strong>${transactionCode}</strong></li>
                                        <li>Số tiền chuyển khoản phải đúng: <strong>${amount.toLocaleString('vi-VN')} VNĐ</strong></li>
                                        <li>Sau khi chuyển khoản, hệ thống sẽ tự động cộng cám trong vòng 1-5 phút</li>
                                        <li>Nếu không nhận được cám sau 10 phút, vui lòng liên hệ <a class="color-7 fw-semibold" href="https://www.facebook.com/profile.php?id=61572454674711" target="_blank" rel="noopener noreferrer">fan page</a> hỗ trợ</li>
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

                $('#depositContainer').html(transferInfoHtml);

                startSSEConnection(transactionCode);
            }

            function copyToClipboard(text) {
                const $button = event.target.closest('.copy-button');
                const originalText = $button.innerHTML;

                $button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text)
                        .then(() => {
                            showCopySuccess($button, originalText);
                        })
                        .catch(() => {
                            copyUsingExecCommand(text, $button, originalText);
                        });
                } else {
                    copyUsingExecCommand(text, $button, originalText);
                }
            }

            function copyUsingExecCommand(text, $button, originalText) {
                try {
                    const $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(text).select();

                    const successful = document.execCommand('copy');

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

            function showCopySuccess($button, originalText) {
                $button.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => $button.innerHTML = originalText, 1000);
            }

            function showCopyFailure($button, originalText) {
                $button.innerHTML = '<i class="fas fa-times"></i>';

                setTimeout(() => $button.innerHTML = originalText, 1000);
            }

            let currentTransactionCode = null;
            let sseConnection = null;

            function startSSEConnection(transactionCode) {
                if (sseConnection) {
                    sseConnection.close();
                }

                currentTransactionCode = transactionCode;
                const sseUrl = '{{ route('user.bank.auto.sse') }}?transaction_code=' + encodeURIComponent(transactionCode);

                sseConnection = new EventSource(sseUrl);

                sseConnection.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);

                        if (data.type === 'close') {
                            sseConnection.close();
                            return;
                        }

                        if (data.status === 'success') {
                            showSuccessNotification(data);
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);

                            sseConnection.close();
                        }
                    } catch (error) {
                        console.error('SSE parsing error:', error);
                    }
                };

                sseConnection.onerror = function(event) {
                    console.error('SSE connection error:', event);
                    setTimeout(() => {
                        if (currentTransactionCode) {
                            startSSEConnection(currentTransactionCode);
                        }
                    }, 5000);
                };
            }

            function showSuccessNotification(data) {
                const toast = `
                    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>
                                Giao dịch thành công! Bạn đã nhận được ${data.total_coins ? data.total_coins.toLocaleString('vi-VN') : 'cám'} cám.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                if (!$('#toast-container').length) {
                    $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
                }

                $('#toast-container').append(toast);

                const toastElement = $('#toast-container .toast').last();
                const toastInstance = new bootstrap.Toast(toastElement[0]);
                toastInstance.show();
            }
        </script>
    @endpush
@endonce
