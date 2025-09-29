@extends('layouts.information')

@section('info_title', 'Nạp xu')
@section('info_description', 'Nạp xu vào tài khoản của bạn trên ' . request()->getHost())
@section('info_keyword', 'nạp xu, thanh toán, ' . request()->getHost())
@section('info_section_title', 'Nạp xu')
@section('info_section_desc', 'Nạp xu vào tài khoản để sử dụng các dịch vụ cao cấp')

@push('styles')
    <style>
        .bank-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .bank-card:hover {
            border-color: #ced4da;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .bank-card.selected {
            border-color: var(--primary-color-3);
            background-color: rgba(var(--primary-rgb), 0.05);
        }

        .bank-logo {
            width: 80px;
            height: 40px;
            object-fit: contain;
        }

        .bank-info {
            font-size: 14px;
            color: #555;
        }

        .transaction-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .transaction-item:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

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

        /* Cải thiện hiệu ứng nút copy */
        .copy-button {
            border: none;
            background-color: transparent;
            color: var(--primary-color-3);
            padding: 0.25rem 0.5rem;
            margin-left: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-button:hover {
            background-color: rgba(var(--primary-rgb), 0.1);
            color: var(--primary-color-2);
        }

        .copy-button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.25);
        }

        .copy-button i {
            font-size: 0.875rem;
        }

        /* Đảm bảo phần nội dung có thể sao chép có thể chọn được */
        .payment-info-value {
            user-select: all;
            cursor: text;
        }

        /* Thiết kế cho thông báo copy */
        .copy-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 9999;
            opacity: 0.9;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.3s ease;
        }

        .copy-toast.success {
            background-color: #198754;
            color: white;
        }

        .copy-toast.error {
            background-color: #dc3545;
            color: white;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 0.9;
                transform: translateY(0);
            }
        }

        /* Cải thiện khả năng tương tác cho thông tin thanh toán */
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

        /* Hiệu ứng khi sao chép thành công */
        .copy-success {
            animation: flashSuccess 0.5s;
        }

        @keyframes flashSuccess {

            0%,
            100% {
                background-color: transparent;
            }

            50% {
                background-color: rgba(25, 135, 84, 0.1);
            }
        }

        /* Kiểu dáng cho modal lý do */
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
    </style>
@endpush

@section('info_content')

    <div class="deposit-tabs d-flex mb-4">
        <a href="{{ route('user.deposit') }}" class="deposit-tab active">
            <i class="fas fa-university me-2"></i>Bank
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
                <div class="">
                    <div class="deposit-card-header">
                        <h5 class="mb-0">Nạp xu qua chuyển khoản ngân hàng</h5>
                    </div>
                    <div class="deposit-card-body">
                        <!-- Debug info (remove in production) -->
                        <div class="alert alert-info d-none">
                            <p>Tỷ giá hiện tại: 1 xu = {{ number_format($coinExchangeRate) }} VNĐ</p>
                            <p>Phí giao dịch: {{ $coinBankPercent }}%</p>
                        </div>

                        <form id="depositForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3">Chọn ngân hàng</label>
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
                                <label for="amount" class="form-label fw-bold mb-3">Nhập số tiền muốn nạp (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control deposit-amount-input" id="amount"
                                        name="amount" value="{{ old('amount', 50000) }}"
                                        data-raw="{{ old('amount', 50000) }}"
                                        min="50000" step="10000">

                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="form-text">Số tiền tối thiểu: 50.000 VNĐ, phải là bội số của 10.000 (50.000, 60.000, 70.000...)</div>
                                <div class="invalid-feedback amount-error">Vui lòng nhập số tiền hợp lệ</div>

                                <div class="deposit-coin-preview mt-4">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="small text-white opacity-75">Xu nhận được:</div>
                                            <div class="coin-preview-value">
                                                <i class="fas fa-coins me-2"></i> <span id="coinsPreview">50</span>
                                            </div>
                                        </div>

                                    </div>
                                    {{-- <div class="small text-white opacity-75 mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Tỷ giá:
                                        {{ number_format($coinExchangeRate) }} VNĐ = 1 xu
                                    </div> --}}
                                    {{-- <div class="small text-white opacity-75 mt-1">
                                        <i class="fas fa-percentage me-1"></i> Phí giao dịch:
                                        {{ $coinBankPercent }}%
                                    </div> --}}
                                </div>

                                <button type="button" id="proceedToPaymentBtn" class="btn payment-btn w-100">
                                    <i class="fas fa-wallet"></i> Tiến hành thanh toán
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
                    <div class="coins-label">Số xu hiện có trong tài khoản</div>

                    <div class="coins-info">
                        <p class="mb-2"><i class="fas fa-chevron-right me-2"></i> Dùng xu để đọc truyện trả phí</p>
                        <p class="mb-2"><i class="fas fa-chevron-right me-2"></i> Dùng xu để mở khóa các tính năng cao cấp
                        </p>
                        <p class="mb-0"><i class="fas fa-chevron-right me-2"></i> Thời gian xử lý nạp xu: 24h sau khi
                            thanh toán</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="transaction-history">
            <div class="deposit-card">
                <div class="deposit-card-header">
                    <h5 class="mb-0">Lịch sử giao dịch</h5>
                </div>
                <div class="deposit-card-body">
                    @if ($deposits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">Mã giao dịch</th>
                                        <th style="width: 15%">Ngân hàng</th>
                                        <th style="width: 15%">Số tiền</th>
                                        <th style="width: 15%">Xu</th>
                                        <th style="width: 15%">Ngày tạo</th>
                                        <th style="width: 10%">Trạng thái</th>
                                        <th style="width: 15%">Biên lai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deposits as $deposit)
                                        <tr>
                                            <td class="align-middle">
                                                <small class="text-muted">{{ $deposit->transaction_code }}</small>
                                            </td>
                                            <td class="align-middle">{{ $deposit->bank->name }}</td>
                                            <td class="align-middle">{{ number_format($deposit->amount) }} VNĐ</td>
                                            <td class="align-middle">{{ number_format($deposit->coins) }}</td>
                                            <td class="align-middle">
                                                <div>{{ $deposit->created_at->format('d/m/Y H:i') }}</div>
                                                <small
                                                    class="text-muted">{{ $deposit->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td class="align-middle">
                                                @if ($deposit->status == 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i> Đang xử lý
                                                    </span>
                                                @elseif($deposit->status == 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i> Đã duyệt
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i> Từ chối
                                                    </span>
                                                @endif

                                                @if (($deposit->status == 'rejected' || $deposit->status == 'cancelled') && $deposit->note)
                                                    <div class="mt-1">
                                                        <a href="#" class="small text-danger show-reason-btn"
                                                            data-reason="{{ $deposit->note }}">
                                                            <i class="fas fa-info-circle"></i> Xem lý do
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($deposit->image)
                                                    <a href="{{ Storage::url($deposit->image) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-fancybox="transaction-images"
                                                        data-caption="Biên lai #{{ $deposit->transaction_code }}">
                                                        <i class="fas fa-image me-1"></i> Xem
                                                    </a>
                                                @elseif ($deposit->note)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary show-reason-btn"
                                                        data-reason="{{ $deposit->note }}">
                                                        <i class="fas fa-comment-alt me-1"></i> Xem ghi chú
                                                    </button>
                                                @else
                                                    <span class="text-muted small">
                                                        <i class="fas fa-ban me-1"></i> Không có
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <x-pagination :paginator="$deposits" />
                        </div>
                    @else
                        <div class="empty-transactions text-center py-5">
                            <div>
                                <i class="fas fa-exchange-alt empty-transactions-icon"></i>
                            </div>
                            <h5>Chưa có giao dịch nào</h5>
                            <p class="empty-transactions-text">Bạn chưa thực hiện giao dịch nạp xu nào. Hãy nạp xu để sử
                                dụng các tính năng trả phí.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade payment-modal" id="paymentModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="closePaymentModal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="payment-qr-code mb-3" id="qrCodeContainer">
                            <img src="" alt="QR Code" id="bankQrCode" class="d-none">
                            <div class="d-flex align-items-center justify-content-center h-100" id="qrCodePlaceholder">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted">Quét mã QR để thực hiện thanh toán</p>
                    </div>

                    <div class="payment-confirmation">
                        <button type="button" class="confirm-payment-btn" id="confirmPaymentBtn">
                            <i class="fas fa-check-circle me-2"></i> Tôi đã chuyển khoản
                        </button>
                    </div>

                    <div class="payment-info">
                        <div class="payment-info-item">
                            <span class="payment-info-label">Ngân hàng:</span>
                            <span class="payment-info-value" id="bankName"></span>
                        </div>

                        <div class="payment-info-item">
                            <span class="payment-info-label">Số tài khoản:</span>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="payment-info-value" id="bankAccountNumber" tabindex="0"
                                        onclick="this.focus();this.select()" onfocus="this.select()"></span>
                                    <button type="button" class="copy-button"
                                        onclick="copyToClipboard('#bankAccountNumber')" title="Sao chép số tài khoản">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="payment-info-item">
                            <span class="payment-info-label">Chủ tài khoản:</span>
                            <span class="payment-info-value" id="bankAccountName"></span>
                        </div>

                        <div class="payment-info-item">
                            <span class="payment-info-label">Số tiền:</span>
                            <div class="d-flex align-items-center">
                                <span class="payment-info-value" id="paymentAmount" tabindex="0"
                                    onclick="this.focus();this.select()" onfocus="this.select()"></span>
                                <span class="ms-1 fw-bold">VNĐ</span>
                                <button type="button" class="copy-button" onclick="copyToClipboard('#paymentAmount')"
                                    title="Sao chép số tiền">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="payment-info-item">
                            <span class="payment-info-label">Nội dung chuyển khoản:</span>
                            <div class="d-flex align-items-center">
                                <span class="payment-info-value" id="transactionCode" tabindex="0"
                                    onclick="this.focus();this.select()" onfocus="this.select()"></span>
                                <button type="button" class="copy-button" onclick="copyToClipboard('#transactionCode')"
                                    title="Sao chép nội dung chuyển khoản">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="payment-info-item" id="expiryContainer">
                            <span class="payment-info-label">Thời hạn:</span>
                            <div class="d-flex align-items-center">
                                <span class="payment-info-value" id="paymentExpiry"></span>
                                <span class="ms-2 badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> <span id="countdownTimer">Đang tính toán...</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Lưu ý:</strong> Vui lòng nhập chính xác
                        nội dung chuyển khoản để hệ thống có thể xác nhận giao dịch của bạn.
                        <br> Giữ biên lai để làm minh chứng.
                    </div>

                    
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Evidence Modal -->
    <div class="modal fade" id="uploadEvidenceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tải lên chứng từ thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="evidenceForm">
                        @csrf
                        <input type="hidden" name="request_payment_id" id="evidenceRequestPaymentId">

                        <div class="transaction-image-upload text-center">
                            <div id="uploadIconContainer">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <h6>Tải lên ảnh chứng minh chuyển khoản</h6>
                                <p class="text-muted small">Hỗ trợ định dạng: JPG, PNG, GIF (tối đa 4MB)</p>
                            </div>

                            <div id="previewContainer" class="mt-3 d-none">
                                <img src="" id="evidencePreview" class="transaction-image-preview">
                            </div>

                            <div class="mt-3">
                                <input type="file" class="form-control" id="transaction_image"
                                    name="transaction_image" accept="image/*" required>
                                <div class="invalid-feedback">Vui lòng tải lên ảnh chứng minh chuyển khoản</div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i> Sau khi gửi chứng từ thanh toán, yêu cầu nạp xu của bạn
                            sẽ được xử lý trong vòng 24 giờ làm việc.
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn payment-btn" id="evidenceSubmitBtn">
                                <i class="fas fa-paper-plane me-2"></i> Gửi yêu cầu nạp xu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reason Modal -->
    <div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin chi tiết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reason-content p-3">
                        <p id="reasonText" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@once
    @push('info_scripts')
        <script>

            // Unhandled promise rejection handler
            window.addEventListener('unhandledrejection', function(event) {
                console.error('Unhandled promise rejection:', event.reason);
            });

            // Global variables to store payment info
            window.paymentInfo = {
                bank: null,
                amount: 0,
                coins: 0,
                fee: 0,
                transactionCode: '',
                requestPaymentId: null,
                expiredAt: null
            };

            // Biến đánh dấu đã submit thanh toán thành công
            window.paymentSubmitted = false;

            window.coinExchangeRate = {{ $coinExchangeRate }};
            window.coinBankPercent = {{ $coinBankPercent }};

            // Xử lý khi người dùng rời trang trong quá trình thanh toán
            window.addEventListener('beforeunload', function(e) {
                // Chỉ hiện cảnh báo khi modal thanh toán đang mở và chưa submit evidence thành công
                if ($('#paymentModal').hasClass('show') && !window.paymentSubmitted) {
                    e.preventDefault();
                    e.returnValue =
                        'Bạn đang trong quá trình thanh toán. Nếu rời khỏi trang, thông tin thanh toán sẽ bị mất.';
                    return e.returnValue;
                }
            });

            const firstBank = $('.bank-option').first();
            if (firstBank.length > 0) {
                firstBank.addClass('selected');
                $('#bankId').val(firstBank.data('bank-id'));
                $('.bank-error').hide();
            }

            // Xử lý chọn ngân hàng
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

            // Cập nhật preview số xu
            $('#amount').on('input', function() {
                updateCoinPreview();
            });

            function updateCoinPreview() {
                try {
                    const amount = parseInt($('#amount').data('raw')) || 0;

                    const feeAmount = (amount * window.coinBankPercent) / 100;
                    const amountAfterFee = amount - feeAmount;
                    const baseCoins = Math.floor(amountAfterFee / window.coinExchangeRate);
                    const totalCoins = baseCoins;

                    $('#coinsPreview').text(totalCoins.toLocaleString('vi-VN'));
                } catch (error) {
                    console.error("Error updating coin preview:", error);
                    $('#coinsPreview').text('0');
                }
            }

            // Initialize coin preview
            updateCoinPreview();

            // Xử lý nút thanh toán
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
                    $('.amount-error').show().text('Số tiền phải là bội số của 10.000 VNĐ (ví dụ: 50.000, 60.000, 70.000...)');
                    valid = false;
                } else {
                    $('.amount-error').hide();
                }

                if (valid) {
                    const bankId = $('#bankId').val();

                    $(this).prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

                    $.ajax({
                        url: '{{ route('user.request.payment.store') }}',
                        type: 'POST',
                        data: {
                            bank_id: bankId,
                            amount: amount,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                window.paymentInfo = {
                                    bank: response.bank,
                                    amount: response.payment.amount,
                                    coins: response.payment.coins,
                                    fee: response.payment.fee,
                                    transactionCode: response.payment.transaction_code,
                                    requestPaymentId: response.request_payment_id,
                                    expiredAt: response.payment.expired_at
                                };

                                populatePaymentModal();

                                var paymentModalEl = document.getElementById('paymentModal');
                                var paymentModal = new bootstrap.Modal(paymentModalEl);

                                // Reset biến đánh dấu thanh toán khi mở modal mới
                                window.paymentSubmitted = false;

                                paymentModal.show();
                            } else {
                                showToast('Có lỗi xảy ra: ' + (response.message ||
                                    'Không thể xử lý thanh toán'), 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Đã xảy ra lỗi khi xử lý yêu cầu';

                            console.error("XHR Error:", xhr);

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    // Laravel validation errors
                                    const errors = xhr.responseJSON.errors;
                                    const firstError = Object.values(errors)[0];
                                    errorMessage = firstError[0] || errorMessage;
                                } else if (xhr.responseJSON.message) {
                                    // Direct error message
                                    errorMessage = xhr.responseJSON.message;
                                }
                            } else if (xhr.status === 500) {
                                errorMessage = 'Lỗi máy chủ nội bộ. Vui lòng thử lại sau.';
                            } else if (xhr.status === 422) {
                                errorMessage = 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.';
                            } else if (xhr.status === 0) {
                                errorMessage =
                                    'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.';
                            }

                            showToast(errorMessage, 'error');
                        },
                        complete: function() {
                            $('#proceedToPaymentBtn').prop('disabled', false).html(
                                '<i class="fas fa-wallet"></i> Tiến hành thanh toán');
                        }
                    });
                }
            });

            // Đếm ngược thời gian
            let countdownInterval;

            function startCountdown(expiredDate) {
                // Xóa interval cũ nếu có
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }

                // Cập nhật đếm ngược mỗi giây
                function updateCountdown() {
                    const now = new Date().getTime();
                    const expiredTime = new Date(expiredDate).getTime();
                    const timeRemaining = expiredTime - now;

                    if (timeRemaining <= 0) {
                        // Hết thời gian
                        clearInterval(countdownInterval);
                        $('#countdownTimer').html('<span class="text-danger">Đã hết hạn</span>');
                        // Có thể thêm xử lý khi hết hạn ở đây (ví dụ: ẩn nút xác nhận)
                        $('#confirmPaymentBtn').prop('disabled', true)
                            .html('<i class="fas fa-exclamation-circle me-2"></i> Đã hết hạn thanh toán');

                        // Hiển thị thông báo
                        showNotification('Yêu cầu thanh toán đã hết hạn. Vui lòng tạo yêu cầu mới.', 'warning');
                        return;
                    }

                    // Tính toán thời gian còn lại
                    const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                    // Định dạng chuỗi hiển thị
                    let countdownText = '';
                    if (hours > 0) {
                        countdownText += hours + ' giờ ';
                    }
                    if (hours > 0 || minutes > 0) {
                        countdownText += minutes + ' phút ';
                    }
                    countdownText += seconds + ' giây';

                    // Cập nhật giao diện
                    $('#countdownTimer').text(countdownText);

                    // Đổi màu khi gần hết hạn (dưới 10 phút)
                    if (timeRemaining < 10 * 60 * 1000) {
                        $('#countdownTimer').addClass('text-danger fw-bold');
                    } else {
                        $('#countdownTimer').removeClass('text-danger fw-bold');
                    }
                }

                // Cập nhật ngay lập tức
                updateCountdown();

                // Cập nhật mỗi giây
                countdownInterval = setInterval(updateCountdown, 1000);
            }

            // Populate payment modal with data
            function populatePaymentModal() {
                $('#bankName').text(window.paymentInfo.bank.name + ' (' + window.paymentInfo.bank.code + ')');
                $('#bankAccountNumber').text(window.paymentInfo.bank.account_number);
                $('#bankAccountName').text(window.paymentInfo.bank.account_name);
                $('#paymentAmount').text(window.paymentInfo.amount.toLocaleString('vi-VN'));
                $('#transactionCode').text(window.paymentInfo.transactionCode);

                // Hiển thị thời gian hết hạn
                if (window.paymentInfo.expiredAt) {
                    // Tạo Date object từ ISO string - JavaScript sẽ tự động convert về local timezone
                    const expiredDate = new Date(window.paymentInfo.expiredAt);

                    // Hiển thị thời gian theo múi giờ local
                    $('#paymentExpiry').text(expiredDate.toLocaleString('vi-VN', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }));

                    $('#expiryContainer').removeClass('d-none');
                    startCountdown(expiredDate);
                } else {
                    $('#expiryContainer').addClass('d-none');
                }

                // Display QR code if available
                if (window.paymentInfo.bank.qr_code) {
                    $('#bankQrCode').attr('src', window.paymentInfo.bank.qr_code).removeClass('d-none');
                    $('#qrCodePlaceholder').addClass('d-none');
                } else {
                    $('#bankQrCode').addClass('d-none');
                    $('#qrCodePlaceholder').removeClass('d-none')
                        .html(
                            '<div class="text-center text-muted"><i class="fas fa-qrcode fa-3x mb-2"></i><p>QR code không khả dụng</p></div>'
                        );
                }
            }

            // Xử lý nút "tôi đã chuyển khoản"
            $('#confirmPaymentBtn').on('click', function() {
                // Hide payment modal and show upload evidence modal
                $('#paymentModal').modal('hide');

                // Populate evidence form with data
                $('#evidenceRequestPaymentId').val(window.paymentInfo.requestPaymentId);

                setTimeout(function() {
                    $('#uploadEvidenceModal').modal('show');
                }, 500);
            });

            // Xử lý preview hình ảnh
            $('#transaction_image').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#evidencePreview').attr('src', e.target.result);
                        $('#previewContainer').removeClass('d-none');
                        $('#uploadIconContainer').addClass('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#previewContainer').addClass('d-none');
                    $('#uploadIconContainer').removeClass('d-none');
                }
            });


            // Submit xác nhận đã chuyển khoản với upload ảnh
            $('#evidenceForm').on('submit', function(e) {
                e.preventDefault();

                if (!$('#transaction_image').val()) {
                    $('#transaction_image').addClass('is-invalid');
                    return;
                }

                $('#transaction_image').removeClass('is-invalid');

                // Sử dụng FormData để gửi file
                var formData = new FormData(this);

                // Hiển thị trạng thái loading
                $('#evidenceSubmitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i> Đang xử lý...');


                // Gọi API xác nhận thanh toán
                $.ajax({
                    url: '{{ route('user.request.payment.confirm') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Đánh dấu đã thanh toán thành công để không hiện cảnh báo khi reload
                            window.paymentSubmitted = true;

                            // Đóng modal
                            $('#uploadEvidenceModal').modal('hide');

                            // Hiển thị thông báo thành công và tự động reload trang sau 1.5 giây
                            setTimeout(function() {
                                // Hiển thị toast thông báo thành công
                                window.showToast(response.message, 'success');

                                // Tự động reload trang sau 1.5 giây
                                setTimeout(function() {
                                    window.location.href = window.location.href;
                                }, 1500);
                            }, 500);
                        } else {

                            showToast(response.message || 'Có lỗi xảy ra khi xử lý yêu cầu.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error("Evidence submission error:", xhr);

                        let errorMessage = 'Đã xảy ra lỗi khi xử lý yêu cầu';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Laravel validation errors
                                const errors = xhr.responseJSON.errors;
                                const firstError = Object.values(errors)[0];
                                errorMessage = firstError[0] || errorMessage;

                                // Highlight specific form fields with errors
                                if (errors.transaction_image) {
                                    $('#transaction_image').addClass('is-invalid');
                                    $('#transaction_image').next('.invalid-feedback').text(errors
                                        .transaction_image[0]);
                                }

                                if (errors.request_payment_id) {
                                    errorMessage = 'Yêu cầu thanh toán không hợp lệ hoặc đã hết hạn';
                                }
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        } else if (xhr.status === 500) {
                            errorMessage = 'Lỗi máy chủ nội bộ. Vui lòng thử lại sau.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Bạn không có quyền thực hiện thao tác này.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.';
                        }

                        showToast(errorMessage, 'error');
                    },
                    complete: function() {
                        // Khôi phục trạng thái nút
                        $('#evidenceSubmitBtn').prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-2"></i> Gửi yêu cầu nạp xu');
                    }
                });
            });

            // Tiện ích copy vào clipboard - phiên bản nâng cao
            function copyToClipboard(element) {
                const textToCopy = $(element).text().trim();
                const $button = $(element).next('.copy-button');
                const originalText = $button.html();

                // Hiển thị trạng thái đang xử lý
                $button.html('<i class="fas fa-spinner fa-spin"></i>');

                // Phương pháp 1: Clipboard API (chỉ hoạt động trên HTTPS hoặc localhost)
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy)
                        .then(() => {
                            showCopySuccess($button, originalText);
                        })
                        .catch(() => {
                            // Nếu phương pháp 1 thất bại, thử phương pháp 2
                            copyUsingExecCommand(element, $button, originalText);
                        });
                }
                // Phương pháp 2: document.execCommand (hỗ trợ cũ)
                else {
                    copyUsingExecCommand(element, $button, originalText);
                }
            }

            // Phương pháp sao chép bằng execCommand
            function copyUsingExecCommand(element, $button, originalText) {
                try {
                    // Tạo vùng chọn văn bản và chọn nội dung
                    const range = document.createRange();
                    range.selectNode($(element)[0]);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);

                    // Thực hiện lệnh sao chép
                    const successful = document.execCommand('copy');

                    // Xóa vùng chọn
                    window.getSelection().removeAllRanges();

                    if (successful) {
                        showCopySuccess($button, originalText);
                    } else {
                        // Nếu không thành công, thử phương pháp 3
                        copyUsingTempTextarea($(element).text().trim(), $button, originalText);
                    }
                } catch (err) {
                    // Nếu có lỗi, thử phương pháp 3
                    copyUsingTempTextarea($(element).text().trim(), $button, originalText);
                }
            }

            // Phương pháp sao chép bằng textarea tạm thời
            function copyUsingTempTextarea(text, $button, originalText) {
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
                $button.html('<i class="fas fa-check"></i>');

                // // Tạo toast thông báo nhỏ
                // $('<div class="copy-toast success">Đã sao chép</div>')
                //     .appendTo('body')
                //     .fadeIn(200)
                //     .delay(1500)
                //     .fadeOut(200, function() {
                //         $(this).remove();
                //     });

                // Khôi phục nút sau 1 giây
                setTimeout(() => $button.html(originalText), 1000);
            }

            // Hiển thị thất bại
            function showCopyFailure($button, originalText) {
                $button.html('<i class="fas fa-times"></i>');

                // Hiển thị hướng dẫn sao chép thủ công
                $('<div class="copy-toast error">Không thể tự động sao chép. Vui lòng nhấp vào văn bản và chọn Sao chép.</div>')
                    .appendTo('body')
                    .fadeIn(200)
                    .delay(3000)
                    .fadeOut(200, function() {
                        $(this).remove();
                    });

                // Khôi phục nút sau 1 giây
                setTimeout(() => $button.html(originalText), 1000);
            }

            // Hiển thị thông báo nhỏ
            function showNotification(message, type = 'info') {
                window.showToast(message, type);
            }

            // Xử lý khi nhấn nút đóng modal thanh toán
            $(document).ready(function() {
                // Xử lý sự kiện khi nhấn nút X hoặc nút đóng
                $('#closePaymentModal, #paymentModal .btn-close').on('click', function(e) {
                    e.preventDefault();
                    // Hiển thị xác nhận hủy bằng SweetAlert2
                    window.showConfirm(
                        'Xác nhận hủy thanh toán',
                        'Bạn có chắc chắn muốn hủy giao dịch này? Thông tin giao dịch sẽ không được lưu lại.',
                        function() {
                            // Đánh dấu modal để cho phép đóng
                            $('#paymentModal').data('force-close', true);

                            // Đóng modal thanh toán
                            $('#paymentModal').modal('hide');

                            // Xóa backdrop và reset body
                            setTimeout(function() {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                                $('body').css('padding-right', '');
                                $('#paymentModal').data('force-close', false);
                            }, 300);

                            // Hiển thị thông báo đã hủy
                            showNotification('Đã hủy giao dịch thanh toán', 'info');
                        }
                    );
                });

                // Ngăn chặn sự kiện đóng khi người dùng click ra ngoài modal
                $('#paymentModal').on('hide.bs.modal', function(e) {
                    if (e.namespace === 'bs.modal' && !e.relatedTarget) {
                        if ($(this).data('force-close') !== true) {
                            e.preventDefault();
                            if (!$(document.activeElement).is('#confirmPaymentBtn')) {
                                $('#cancelPaymentModal').modal('show');
                            }
                        }
                    }
                });

                // Kích hoạt tooltip cho lý do từ chối
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Xử lý hiển thị modal lý do
                $(document).on('click', '.show-reason-btn', function(e) {
                    e.preventDefault();
                    const reason = $(this).data('reason');
                    $('#reasonText').text(reason);
                    const reasonModal = new bootstrap.Modal(document.getElementById('reasonModal'));
                    reasonModal.show();
                });
            });

            $(document).ready(function() {
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
                        // Reset về trạng thái an toàn
                        input.data('raw', 0);
                        updateCoinPreview();
                    }
                });

                $('.deposit-amount-input').on('blur', function() {
                    try {
                        const input = $(this);
                        let rawValue = input.data('raw') || 0;
                        
                        // Làm tròn về bội số của 10.000 gần nhất
                        if (rawValue > 0) {
                            rawValue = Math.round(rawValue / 10000) * 10000;
                            if (rawValue < 50000) rawValue = 50000; // Đảm bảo tối thiểu 50.000
                            
                            const formatted = formatVndCurrency(rawValue.toString());
                            input.val(formatted);
                            input.data('raw', rawValue);
                            updateCoinPreview();
                        }
                    } catch (error) {
                        console.error('Error in blur handler:', error);
                    }
                });

                function updateCoinPreview() {
                    try {
                        const amount = parseInt($('#amount').data('raw')) || 0;

                        if (amount > 0) {
                            const feeAmount = (amount * window.coinBankPercent) / 100;
                            const amountAfterFee = amount - feeAmount;
                            const baseCoins = Math.floor(amountAfterFee / window.coinExchangeRate);
                            const totalCoins = baseCoins;

                            $('#coinsPreview').text(totalCoins.toLocaleString('vi-VN'));
                        } else {
                            $('#coinsPreview').text('0');
                        }
                    } catch (error) {
                        console.error("Error updating coin preview:", error);
                        $('#coinsPreview').text('0');
                    }
                }

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
                        $('.amount-error').show().text('Số tiền phải là bội số của 10.000 VNĐ (ví dụ: 50.000, 60.000, 70.000...)');
                        valid = false;
                    } else {
                        $('.amount-error').hide();
                    }

                    if (valid) {
                        const bankId = $('#bankId').val();

                        $(this).prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

                        $.ajax({
                            url: '{{ route('user.request.payment.store') }}',
                            type: 'POST',
                            data: {
                                bank_id: bankId,
                                amount: amount,
                                _token: $('input[name="_token"]').val()
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    window.paymentInfo = {
                                        bank: response.bank,
                                        amount: response.payment.amount,
                                        coins: response.payment.coins,
                                        fee: response.payment.fee,
                                        transactionCode: response.payment.transaction_code,
                                        requestPaymentId: response.request_payment_id,
                                        expiredAt: response.payment.expired_at
                                    };

                                    populatePaymentModal();

                                    var paymentModalEl = document.getElementById('paymentModal');
                                    var paymentModal = new bootstrap.Modal(paymentModalEl);

                                    // Reset biến đánh dấu thanh toán khi mở modal mới
                                    window.paymentSubmitted = false;

                                    paymentModal.show();
                                } else {
                                    showToast('Có lỗi xảy ra: ' + (response.message ||
                                        'Không thể xử lý thanh toán'), 'error');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Đã xảy ra lỗi khi xử lý yêu cầu';

                                console.error("XHR Error:", xhr);

                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.errors) {
                                        // Laravel validation errors
                                        const errors = xhr.responseJSON.errors;
                                        const firstError = Object.values(errors)[0];
                                        errorMessage = firstError[0] || errorMessage;
                                    } else if (xhr.responseJSON.message) {
                                        // Direct error message
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Lỗi máy chủ nội bộ. Vui lòng thử lại sau.';
                                } else if (xhr.status === 422) {
                                    errorMessage = 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.';
                                } else if (xhr.status === 0) {
                                    errorMessage =
                                        'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.';
                                }

                                showToast(errorMessage, 'error');
                            },
                            complete: function() {
                                $('#proceedToPaymentBtn').prop('disabled', false).html(
                                    '<i class="fas fa-wallet"></i> Tiến hành thanh toán');
                            }
                        });
                    }
                });

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

                // Gọi tính toán xu ngay khi trang tải xong để hiển thị số xu tính từ 50.000
                updateCoinPreview();
            });
        </script>
    @endpush
@endonce
