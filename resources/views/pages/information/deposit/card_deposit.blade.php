@extends('layouts.information')

@section('info_title', 'Nạp xu bằng thẻ cào')
@section('info_description', 'Nạp xu bằng thẻ cào điện thoại trên ' . request()->getHost())
@section('info_keyword', 'nạp xu, thẻ cào, ' . request()->getHost())
@section('info_section_title', 'Nạp xu bằng thẻ cào')
@section('info_section_desc', 'Nạp xu bằng thẻ cào điện thoại Viettel, Mobifone, Vinaphone')

@push('styles')
    <style>
        /* Loại bỏ các CSS không cần thiết cho card-type-option */
        /* Giữ lại chỉ những styles cần thiết */

        .amount-option {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            margin-bottom: 10px;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .amount-option:hover {
            border-color: var(--primary-color-3);
            background-color: rgba(13, 110, 253, 0.05);
        }

        .amount-option.selected {
            border-color: var(--primary-color-3);
            background-color: rgba(13, 110, 253, 0.1);
        }

        .card-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .card-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-select.card-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .form-select.card-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .coins-preview {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        .submit-card-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .submit-card-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .submit-card-btn:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
        }

        .deposit-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }

        .deposit-tab {
            padding: 15px 25px;
            background: transparent;
            border: none;
            color: #6c757d;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .deposit-tab:hover {
            color: var(--primary-color-3);
            background-color: rgba(13, 110, 253, 0.05);
            text-decoration: none;
        }

        .deposit-tab.active {
            color: var(--primary-color-3);
            border-bottom-color: var(--primary-color-3);
            background-color: rgba(13, 110, 253, 0.05);
        }

        .history-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .history-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .coins-panel {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }

        .coins-balance {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .coins-icon {
            color: #ffd700;
            margin-right: 10px;
        }

        .coins-label {
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .coins-info {
            text-align: left;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .invalid-feedback.show {
            display: block;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            border-radius: 15px 15px 0 0;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .card-type-option {
                min-height: 120px;
                padding: 15px;
            }

            .card-type-logo {
                width: 50px;
                height: 50px;
            }

            .deposit-tab {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .coins-balance {
                font-size: 2rem;
            }
        }

        .amount-option.selected::before {
            content: '✓';
            position: absolute;
            top: 0px;
            right: 4px;
            color: white;
            font-size: 14px;
            z-index: 1;
        }

        .amount-option.selected::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0px;
            width: 20px;
            height: 20px;
            border-radius: 0 6px 0 12px;
            background-color: var(--primary-color-3);
        }
    </style>
@endpush

@section('info_content')

    <!-- Deposit Tabs -->
    <div class="deposit-tabs d-flex mb-4">
        <a href="{{ route('user.deposit') }}" class="deposit-tab ">
            <i class="fas fa-university me-2"></i>Bank
        </a>
        <a href="{{ route('user.card.deposit') }}" class="deposit-tab active">
            <i class="fas fa-credit-card me-2"></i>Card
        </a>
        <a href="{{ route('user.paypal.deposit') }}" class="deposit-tab">
            <i class="fab fa-paypal me-2"></i>PayPal
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Card Info Section -->
            <div class="card-info-section mb-3">
                <div class="deposit-card-header">
                    <h5 class="mb-0">Nạp xu bằng thẻ cào</h5>
                </div>
            </div>

            <!-- Card Form -->
            <div class="">
                <div class="card-body">
                    <form id="cardDepositForm">
                        @csrf

                        <div class="mb-4">
                            <label for="cardType" class="form-label fw-bold mb-3">
                                <i class="fas fa-sim-card me-2"></i>Chọn loại thẻ
                            </label>
                            <select class="form-select card-input" id="cardType" name="telco" required>
                                @foreach (\App\Models\CardDeposit::CARD_TYPES as $key => $name)
                                    <option value="{{ $key }}" {{ $key === 'VIETTEL' ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="typeError">Vui lòng chọn loại thẻ</div>
                        </div>

                        <!-- Chọn mệnh giá -->
                        <div class="">
                            <label class="form-label fw-bold mb-3">
                                <i class="fas fa-money-bill-wave me-2"></i>Chọn mệnh giá
                            </label>
                            <div class="row">
                                @foreach (\App\Models\CardDeposit::CARD_VALUES as $value => $label)
                                    <div class="col-md-3 col-6">
                                        <div class="amount-option position-relative {{ $value === 50000 ? 'selected' : '' }}"
                                            data-amount="{{ $value }}">
                                            <div class="fw-bold">{{ $label }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="amount" id="cardAmount" value="50000" required>
                            <div class="invalid-feedback" id="amountError">Vui lòng chọn mệnh giá</div>
                        </div>

                        <div class="deposit-coin-preview mb-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="small opacity-75">Xu nhận được:</div>
                                    <div class="h4 mb-0">
                                        <i class="fas fa-coins me-2"></i>
                                        <span id="coinsPreview">0</span> xu
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin thẻ -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="serial" class="form-label fw-bold">
                                        <i class="fas fa-barcode me-2"></i>Số serial
                                    </label>
                                    <input type="text" class="form-control card-input" id="serial" name="serial"
                                        placeholder="Nhập số serial thẻ" maxlength="20" required>
                                    <div class="invalid-feedback" id="serialError">Vui lòng nhập số serial (10-20 ký tự)
                                    </div>
                                    <small class="form-text text-muted">Số serial in ở mặt sau thẻ</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label fw-bold">
                                        <i class="fas fa-key me-2"></i>Mã thẻ
                                    </label>
                                    <input type="text" class="form-control card-input" id="code" name="code"
                                        placeholder="Nhập mã thẻ" maxlength="20" required>
                                    <div class="invalid-feedback" id="codeError">Vui lòng nhập mã thẻ (10-20 ký tự)</div>
                                    <small class="form-text text-muted">Mã PIN cào để lộ</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hướng dẫn -->
                        <div class="alert alert-info border-0">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Hướng dẫn nạp thẻ
                            </h6>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <p class="small mb-1"><strong>Bước 1:</strong> Chọn đúng loại thẻ</p>
                                    <p class="small mb-1"><strong>Bước 2:</strong> Chọn mệnh giá thẻ</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="small mb-1"><strong>Bước 3:</strong> Nhập số serial</p>
                                    <p class="small mb-1"><strong>Bước 4:</strong> Nhập mã PIN</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-danger mb-4">
                            <h6><i class="fas fa-info-circle me-2"></i>Thông tin phí và chính sách</h6>
                            <ul class="mb-0">
                             
                                <li class="text-danger">
                                    <strong>Lưu ý:</strong> Nếu nạp sai mệnh giá thẻ, bạn sẽ bị trừ thêm
                                    <strong>{{ \App\Models\Config::getConfig('card_wrong_amount_penalty', 50) }}%</strong>
                                    phí phạt trên mệnh giá thực của thẻ.
                                </li>
                                <li class="text-warning">
                                    <strong>Ví dụ:</strong> Thẻ 100k nhưng thực tế chỉ có 50k → Nhận được xu tương ứng với
                                    25k (50k - 50% phạt - phí hệ thống)
                                </li>
                            </ul>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="submit-card-btn" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Nạp thẻ ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="coins-panel">
                <div class="coins-balance">
                    <i class="fas fa-coins coins-icon"></i>{{ number_format(Auth::user()->coins ?? 0) }}
                </div>
                <div class="coins-label">Số xu hiện có trong tài khoản</div>

                <div class="coins-info">
                    <h6 class="text-white mb-3">
                        <i class="fas fa-star me-2"></i>Ưu điểm nạp thẻ
                    </h6>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>Nhanh chóng và tiện lợi
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>Hỗ trợ nhiều loại thẻ
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>Xử lý tự động 24/7
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-check-circle me-2 text-success"></i>Bảo mật cao
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử nạp thẻ -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card history-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Lịch sử nạp thẻ
                    </h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </button>
                </div>
                <div class="card-body">
                    @if ($cardDeposits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <i class="fas fa-mobile-alt me-1"></i>Loại thẻ
                                        </th>
                                        <th>
                                            <i class="fas fa-money-bill me-1"></i>Mệnh giá
                                        </th>
                                        <th>
                                            <i class="fas fa-coins me-1"></i>Xu nhận được
                                        </th>
                                        <th>
                                            <i class="fas fa-clock me-1"></i>Thời gian
                                        </th>
                                        <th>
                                            <i class="fas fa-info-circle me-1"></i>Trạng thái
                                        </th>
                                        <th>
                                            <i class="fas fa-cog me-1"></i>Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cardDeposits as $deposit)
                                        <tr
                                            class="{{ $deposit->status === 'success' ? 'table-success' : ($deposit->status === 'failed' ? 'table-danger' : 'table-warning') }}">
                                            <td>{{ $deposit->id }}</td>
                                            <td>{{ $deposit->card_type_text }}</td>
                                            <td>{{ $deposit->amount_formatted }}</td>

                                            {{-- Cột xu nhận được với thông tin penalty --}}
                                            <td>
                                                <strong>{{ number_format($deposit->coins) }} xu</strong>

                                                @if ($deposit->status === 'success')
                                                    <br>
                                                    <small class="text-muted">
                                                        Phí hệ thống: {{ number_format($deposit->fee_amount) }}đ
                                                        ({{ $deposit->fee_percent }}%)
                                                        @if ($deposit->hasPenalty())
                                                            <br><span class="text-danger">
                                                                Phí phạt sai mệnh giá:
                                                                {{ $deposit->penalty_amount_formatted }}
                                                                ({{ $deposit->penalty_percent }}%)
                                                            </span>
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>

                                            <td>
                                                <span
                                                    class="badge bg-{{ $deposit->status === 'success' ? 'success' : ($deposit->status === 'failed' ? 'danger' : 'warning') }}">
                                                    {{ $deposit->status_text }}
                                                </span>

                                                @if ($deposit->hasPenalty())
                                                    <br><small class="badge bg-warning mt-1">Sai mệnh giá</small>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $deposit->created_at->format('d/m/Y H:i') }}
                                                @if ($deposit->processed_at)
                                                    <br><small class="text-muted">Xử lý:
                                                        {{ $deposit->processed_at->format('d/m/Y H:i') }}</small>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($deposit->note)
                                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                        title="{{ $deposit->note }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($cardDeposits->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $cardDeposits->links('components.pagination') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5>Chưa có giao dịch nạp thẻ nào</h5>
                            <p class="text-muted">Hãy nạp thẻ đầu tiên để sử dụng các tính năng trả phí</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>Chi tiết giao dịch
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="depositDetails"></div>
                    <hr>
                    <div>
                        <strong>Ghi chú:</strong>
                        <p id="noteContent" class="mt-2 mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            const coinExchangeRate = {{ $coinExchangeRate }};
            const coinCardPercent = {{ $coinCardPercent }};

            // **Tự động load preview khi page load với default values**
            updateCoinsPreview();

            // Chọn loại thẻ từ dropdown
            $('#cardType').on('change', function() {
                $('#typeError').removeClass('show');
                updateCoinsPreview();
            });

            // Chọn mệnh giá
            $('.amount-option').on('click', function() {
                $('.amount-option').removeClass('selected');
                $(this).addClass('selected');
                $('#cardAmount').val($(this).data('amount'));
                $('#amountError').removeClass('show');
                updateCoinsPreview();
            });

            // Cập nhật preview xu
            function updateCoinsPreview() {
                const amount = parseInt($('#cardAmount').val()) || 0;
                if (amount > 0) {
                    const feeAmount = (amount * coinCardPercent) / 100;
                    const amountAfterFee = amount - feeAmount;
                    const coins = Math.floor(amountAfterFee / coinExchangeRate);
                    $('#coinsPreview').text(coins.toLocaleString());
                } else {
                    $('#coinsPreview').text('0');
                }
            }

            // Validation real-time
            $('#serial').on('input', function() {
                const value = $(this).val();
                if (value.length >= 10 && value.length <= 20) {
                    $(this).removeClass('is-invalid');
                    $('#serialError').removeClass('show');
                }
            });

            $('#code').on('input', function() {
                const value = $(this).val();
                if (value.length >= 10 && value.length <= 20) {
                    $(this).removeClass('is-invalid');
                    $('#codeError').removeClass('show');
                }
            });

            // Submit form
            $('#cardDepositForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                showConfirmationDialog();
            });

            function validateForm() {
                let valid = true;

                // Validate card type
                if (!$('#cardType').val()) {
                    $('#cardType').addClass('is-invalid');
                    $('#typeError').addClass('show');
                    valid = false;
                } else {
                    $('#cardType').removeClass('is-invalid');
                    $('#typeError').removeClass('show');
                }

                // Validate amount
                if (!$('#cardAmount').val()) {
                    $('#amountError').addClass('show');
                    valid = false;
                }

                // Validate serial
                const serial = $('#serial').val();
                if (!serial || serial.length < 10 || serial.length > 20) {
                    $('#serial').addClass('is-invalid');
                    $('#serialError').addClass('show');
                    valid = false;
                } else {
                    $('#serial').removeClass('is-invalid');
                    $('#serialError').removeClass('show');
                }

                // Validate code
                const code = $('#code').val();
                if (!code || code.length < 10 || code.length > 20) {
                    $('#code').addClass('is-invalid');
                    $('#codeError').addClass('show');
                    valid = false;
                } else {
                    $('#code').removeClass('is-invalid');
                    $('#codeError').removeClass('show');
                }

                return valid;
            }

            function showConfirmationDialog() {
                const cardType = $('#cardType option:selected').text();
                const amount = parseInt($('#cardAmount').val());
                const feeAmount = (amount * coinCardPercent) / 100;
                const coins = Math.floor((amount - feeAmount) / coinExchangeRate);

                Swal.fire({
                    title: 'Xác nhận nạp thẻ',
                    html: `
                        <div class="text-start">
                            <div class="row mb-2">
                                <div class="col-4"><strong>Loại thẻ:</strong></div>
                                <div class="col-8">${cardType}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Mệnh giá:</strong></div>
                                <div class="col-8">${amount.toLocaleString()} VNĐ</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Phí giao dịch:</strong></div>
                                <div class="col-8">${feeAmount.toLocaleString()} VNĐ (${coinCardPercent}%)</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Xu nhận được:</strong></div>
                                <div class="col-8 text-primary"><strong>${coins.toLocaleString()} xu</strong></div>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0">
                            <small><i class="fas fa-exclamation-triangle me-1"></i> Vui lòng kiểm tra kỹ thông tin. Thẻ sai sẽ không được hoàn tiền!</small>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Nạp thẻ',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Hủy',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-wide'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitCard();
                    }
                });
            }

            function submitCard() {

                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi thẻ...');

                $.ajax({
                    url: '{{ route('user.card.deposit.store') }}',
                    type: 'POST',
                    data: $('#cardDepositForm').serialize(),
                    timeout: 30000,
                    success: function(response) {


                        if (response.success) {
                            let icon = 'success';
                            let title = 'Thẻ đã được gửi!';
                            let text = response.message;

                            if (response.status == 1) {
                                title = 'Nạp thẻ thành công!';
                                text = 'Thẻ hợp lệ và xu đã được cộng vào tài khoản.';
                            } else if (response.status == 2) {
                                title = 'Thẻ đúng nhưng sai mệnh giá!';
                                text = 'Xu sẽ được cộng theo mệnh giá thực của thẻ.';
                            } else if (response.status == 99) {
                                icon = 'info';
                                title = 'Thẻ đang xử lý!';
                                text = 'Thẻ đã được gửi xử lý, kết quả sẽ cập nhật trong 1-5 phút.';
                            }

                            Swal.fire({
                                icon: icon,
                                title: title,
                                text: text,
                                confirmButtonText: 'Đã hiểu',
                                timer: icon === 'success' ? 3000 : null,
                                timerProgressBar: icon === 'success'
                            }).then(() => {
                                if (response.status == 1 || response.status == 2) {
                                    window.location.reload();
                                } else {
                                    resetFormToDefaults();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message,
                                confirmButtonText: 'Đã hiểu'
                            });
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {


                        let message = 'Có lỗi xảy ra khi xử lý thẻ';

                        if (textStatus === 'timeout') {
                            message =
                                'Timeout! Vui lòng kiểm tra lại trạng thái trong lịch sử giao dịch.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors);
                            message = errors.flat().join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: message,
                            confirmButtonText: 'Đã hiểu'
                        });
                    },
                    complete: function() {

                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-2"></i>Nạp thẻ ngay');
                    }
                });
            }

            function resetFormToDefaults() {
                // Reset form và set lại default values
                $('#cardDepositForm')[0].reset();

                // Set lại default selections
                $('#cardType').val('VIETTEL');

                $('.amount-option').removeClass('selected');
                $('.amount-option[data-amount="50000"]').addClass('selected');
                $('#cardAmount').val('50000');

                // Clear validation states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').removeClass('show');

                // Update preview
                updateCoinsPreview();
            }

            // Kiểm tra trạng thái
            $('.check-status-btn').on('click', function() {
                const id = $(this).data('id');
                const btn = $(this);

                btn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i>Đang kiểm tra...');

                $.ajax({
                    url: `{{ url('/user/card-deposit/status') }}/${id}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Trạng thái cập nhật',
                                text: 'Trạng thái: ' + response.status_text,
                                confirmButtonText: 'Đã hiểu'
                            }).then(() => {
                                setTimeout(() => window.location.reload(), 500);
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không thể kiểm tra trạng thái',
                            confirmButtonText: 'Đã hiểu'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-sync me-1"></i>Kiểm tra');
                    }
                });
            });

            // Hiển thị ghi chú chi tiết
            $('.show-note-btn').on('click', function() {
                const note = $(this).data('note');
                const deposit = $(this).data('deposit');

                // Build detail HTML
                let detailHtml = '';
                if (deposit) {
                    detailHtml = `
                        <div class="row">
                            <div class="col-4"><strong>Loại thẻ:</strong></div>
                            <div class="col-8">${deposit.type}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong>Mệnh giá:</strong></div>
                            <div class="col-8">${deposit.amount}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong>Xu nhận:</strong></div>
                            <div class="col-8">${deposit.coins}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong>Trạng thái:</strong></div>
                            <div class="col-8">${deposit.status}</div>
                        </div>
                        <div class="row">
                            <div class="col-4"><strong>Thời gian:</strong></div>
                            <div class="col-8">${deposit.time}</div>
                        </div>
                    `;
                }

                $('#depositDetails').html(detailHtml);
                $('#noteContent').text(note);
                $('#detailModal').modal('show');
            });

            // Auto refresh status every 30 seconds if there are processing transactions
            if ($('.check-status-btn').length > 0) {
                setInterval(function() {
                    $('.check-status-btn').each(function() {
                        $(this).trigger('click');
                    });
                }, 30000); // 30 seconds
            }
        });
    </script>

    <style>
        .swal-wide {
            width: 600px !important;
        }

        @media (max-width: 768px) {
            .swal-wide {
                width: 95% !important;
            }
        }
    </style>
@endpush
