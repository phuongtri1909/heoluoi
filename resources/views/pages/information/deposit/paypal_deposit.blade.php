{{-- filepath: resources/views/pages/information/deposit/paypal_deposit.blade.php --}}
@extends('layouts.information')

@section('info_title', 'Nạp xu bằng PayPal')
@section('info_description', 'Nạp xu bằng PayPal trên ' . request()->getHost())
@section('info_keyword', 'nạp xu, paypal, ' . request()->getHost())
@section('info_section_title', 'Nạp xu bằng PayPal')
@section('info_section_desc', 'Nạp xu bằng PayPal một cách nhanh chóng và an toàn')

@push('styles')
    <style>
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

        .paypal-form {
            background: linear-gradient(135deg, #0070ba 0%, #003087 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
        }

        .paypal-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .paypal-input:focus {
            border-color: #0070ba;
            box-shadow: 0 0 0 0.2rem rgba(0, 112, 186, 0.25);
        }

        .paypal-btn {
            background: linear-gradient(135deg, #ffc439 0%, #ffb800 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: #003087;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .paypal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 196, 57, 0.3);
            color: #003087;
        }

        .paypal-btn:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
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

        .preview-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .preview-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .preview-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .confirm-modal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .upload-modal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #0070ba;
            background-color: rgba(0, 112, 186, 0.05);
        }

        .evidence-preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .payment-content-box {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .payment-content-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: #155724;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            margin-bottom: 10px;
            user-select: all;
            cursor: text;
        }

        .pending-request-item {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            transition: all 0.3s ease;
        }

        .pending-request-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Copy button styles */
        .copy-button {
            border: none;
            background-color: transparent;
            color: #28a745;
            padding: 0.25rem 0.5rem;
            margin-left: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-button:hover {
            background-color: rgba(40, 167, 69, 0.1);
            color: #1e7e34;
        }

        .copy-button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
        }

        .copy-button i {
            font-size: 0.875rem;
        }

        /* Copy success animation */
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

        /* Toast styles */
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

        @media (max-width: 768px) {
            .deposit-tab {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .coins-balance {
                font-size: 2rem;
            }

            .paypal-form {
                padding: 20px;
            }

            .pending-request-item {
                padding: 20px;
            }
        }

        .payment-method-option {
            margin-bottom: 15px;
        }

        .payment-method-card {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }

        .payment-method-option .form-check-input:checked+.form-check-label .payment-method-card {
            border-color: #ffc439;
            background: rgba(255, 196, 57, 0.2);
            box-shadow: 0 0 15px rgba(255, 196, 57, 0.3);
        }

        .payment-method-card:hover {
            border-color: rgba(255, 196, 57, 0.7);
            background: rgba(255, 255, 255, 0.15);
        }

        .payment-method-option .form-check-input {
            margin-top: 0.5rem;
        }

        .payment-method-option .form-check-label {
            cursor: pointer;
            width: 100%;
        }

        #paymentMethodFeeRow {
            display: none;
        }

        #paymentMethodFeeRow.show {
            display: flex;
        }

        .preview-item.highlight {
            background: rgba(255, 196, 57, 0.2);
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
        }
    </style>
@endpush

@section('info_content')
    <!-- Deposit Tabs -->
    <div class="deposit-tabs d-flex">
        <a href="{{ route('user.deposit') }}" class="deposit-tab">
            <i class="fas fa-university me-2"></i>Bank
        </a>
        <a href="{{ route('user.card.deposit') }}" class="deposit-tab">
            <i class="fas fa-credit-card me-2"></i>Card
        </a>
        <a href="{{ route('user.paypal.deposit') }}" class="deposit-tab active">
            <i class="fab fa-paypal me-2"></i>PayPal
        </a>
    </div>

    <!-- PayPal Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="paypal-form">
                <div class="text-center mb-4">
                    <i class="fab fa-paypal fa-4x mb-3"></i>
                    <h4 class="mb-0">Nạp xu bằng PayPal</h4>
                    <p class="mb-0 opacity-75">Thanh toán nhanh chóng và an toàn</p>
                </div>

                <form id="paypalDepositForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usdAmount" class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign me-2"></i>Số tiền (USD)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control paypal-input" id="usdAmount" name="usd_amount"
                                        min="5" step="5" value="5" required>
                                </div>
                                <small class="text-light opacity-75">Tối thiểu: $5, phải là bội số của $5 (5, 10, 15, 20...)</small>
                                <div class="invalid-feedback" id="amountError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paypalEmail" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Email PayPal của bạn
                                </label>
                                <input type="email" class="form-control paypal-input" id="paypalEmail" name="paypal_email"
                                    placeholder="your-email@example.com" required>
                                <small class="text-light opacity-75">Email bạn dùng để gửi tiền</small>
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-handshake me-2"></i>Loại thanh toán PayPal
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-option">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="friendsFamily" value="friends_family" checked>
                                            <label class="form-check-label" for="friendsFamily">
                                                <div class="payment-method-card">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-users text-success me-2"></i>
                                                        <strong>Friends & Family</strong>
                                                    </div>
                                                    <p class="mb-1 small">Gửi tiền cho người thân, bạn bè</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check payment-method-option">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="goodsServices" value="goods_services">
                                            <label class="form-check-label" for="goodsServices">
                                                <div class="payment-method-card">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-shopping-cart text-warning me-2"></i>
                                                        <strong>Goods & Services</strong>
                                                    </div>
                                                    <p class="mb-1 small">Thanh toán hàng hóa, dịch vụ</p>

                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Lưu ý:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>Friends & Family:</strong> Miễn phí PayPal, giá gốc không đổi</li>
                                        <li><strong>Goods & Services:</strong> PayPal tính phí để bù phí</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preview-box" id="previewBox">
                        <h6 class="mb-3">
                            <i class="fas fa-calculator me-2"></i>Chi tiết giao dịch
                        </h6>
                        <div class="preview-item">
                            <span>Số tiền USD gốc:</span>
                            <span id="previewBaseUSD">$5.00</span>
                        </div>
                        <div class="preview-item" id="paymentMethodFeeRow">
                            <span>Phí loại thanh toán:</span>
                            <span id="previewMethodFee">$0.00</span>
                        </div>
                        <div class="preview-item">
                            <span>Tổng tiền cần gửi:</span>
                            <span id="previewTotalUSD">$5.00</span>
                        </div>
                        <div class="preview-item">
                            <span>Xu nhận được:</span>
                            <span id="previewCoins">1,000 xu</span>
                        </div>
                        <div class="preview-item">
                            <span>Loại thanh toán:</span>
                            <span id="previewPaymentMethod">Friends & Family</span>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="paypal-btn" id="submitBtn">
                            <i class="fab fa-paypal me-2"></i>Tạo yêu cầu thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="coins-panel">
                <div class="coins-balance">
                    <i class="fas fa-coins coins-icon"></i>{{ number_format(Auth::user()->coins ?? 0) }}
                </div>
                <div class="coins-label">Số xu hiện có trong tài khoản</div>

                <div class="coins-info">
                    <h6 class="text-white mb-3">
                        <i class="fab fa-paypal me-2"></i>Ưu điểm PayPal
                    </h6>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>Thanh toán quốc tế
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>Bảo mật cao
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-check-circle me-2 text-success"></i>Hỗ trợ nhiều loại thẻ
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-check-circle me-2 text-success"></i>Xử lý trong 24h
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Request Section (Only show latest pending after confirmed) -->
    @php
        $latestPendingRequest =
            isset($pendingRequests) && $pendingRequests->count() > 0 ? $pendingRequests->first() : null;
    @endphp

    @if ($latestPendingRequest)
        <div class="row mt-4">
            <div class="col-12">
                <div class="pending-request-item">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 text-warning">
                            <i class="fas fa-clock me-2"></i>Yêu cầu xác nhận thanh toán
                        </h5>
                        <span class="badge bg-warning text-dark fs-6">Chờ xác nhận</span>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="fw-bold text-primary fs-4">{{ $latestPendingRequest->transaction_code }}</div>
                                <small class="text-muted">Mã giao dịch</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="fw-bold text-success fs-5">
                                ${{ number_format($latestPendingRequest->usd_amount, 2) }}</div>
                            <div class="text-muted">{{ number_format($latestPendingRequest->coins) }} xu</div>
                            <small class="text-muted">{{ $latestPendingRequest->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <button class="btn btn-success btn-lg confirm-payment-btn mb-2"
                                data-code="{{ $latestPendingRequest->transaction_code }}"
                                data-amount="${{ number_format($latestPendingRequest->usd_amount, 2) }}"
                                data-content="{{ $latestPendingRequest->content }}"
                                data-paypal-url="{{ $latestPendingRequest->paypal_me_link }}"
                                data-coins="{{ number_format($latestPendingRequest->coins) }}">
                                <i class="fas fa-check me-2"></i>Đã thanh toán qua PayPal
                            </button>
                            <div class="text-danger small">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Hết hạn: {{ $latestPendingRequest->expired_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Hướng dẫn:</strong> Sau khi hoàn tất thanh toán qua PayPal với nội dung
                        "{{ $latestPendingRequest->content }}",
                        hãy nhấn nút "Đã thanh toán qua PayPal" để tải lên ảnh chứng minh.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Lịch sử giao dịch PayPal
                    </h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Làm mới
                    </button>
                </div>
                <div class="card-body">
                    @if (isset($paypalDeposits) && $paypalDeposits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã giao dịch</th>
                                        <th>Số tiền USD</th>
                                        <th>Xu nhận được</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paypalDeposits as $deposit)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $deposit->transaction_code }}</span>
                                                @if ($deposit->requestPaymentPaypal)
                                                    <br><small
                                                        class="text-muted">{{ $deposit->requestPaymentPaypal->paypal_email }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong
                                                    class="text-primary">${{ number_format($deposit->usd_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    <i class="fas fa-coins me-1"></i>{{ number_format($deposit->coins) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <div>{{ $deposit->created_at->format('d/m/Y H:i') }}</div>
                                                <small
                                                    class="text-muted">{{ $deposit->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if ($deposit->status == 'pending')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i>Đang xử lý
                                                    </span>
                                                @elseif($deposit->status == 'processing')
                                                    <span class="badge bg-info text-dark">
                                                        <i class="fas fa-spinner me-1"></i>Đang xử lý
                                                    </span>
                                                @elseif($deposit->status == 'approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Đã duyệt
                                                    </span>
                                                @elseif($deposit->status == 'rejected')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Từ chối
                                                    </span>
                                                @endif
                                                @if ($deposit->note)
                                                    <div class="small text-muted mt-1">
                                                        {{ Str::limit($deposit->note, 50) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($deposit->image)
                                                    <a href="{{ Storage::url($deposit->image) }}"
                                                        class="btn btn-sm btn-outline-success"
                                                        data-fancybox="paypal-images"
                                                        data-caption="Chứng từ #{{ $deposit->transaction_code }}">
                                                        <i class="fas fa-image me-1"></i>Xem ảnh
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($paypalDeposits->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $paypalDeposits->links('components.pagination') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fab fa-paypal fa-3x text-muted mb-3"></i>
                            <h5>Chưa có giao dịch PayPal nào</h5>
                            <p class="text-muted">Hãy thực hiện giao dịch đầu tiên để nạp xu vào tài khoản</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Payment Modal -->
    <div class="modal fade confirm-modal" id="confirmPaymentModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fab fa-paypal me-2"></i>Xác nhận thanh toán PayPal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Yêu cầu thanh toán đã được tạo thành công!</strong>
                    </div>

                    <div class="payment-content-box">
                        <h6 class="text-success mb-2">
                            <i class="fas fa-code me-2"></i>Nội dung giao dịch
                        </h6>
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="payment-content-text" id="confirmPaymentContent" tabindex="0"
                                onclick="this.focus();this.select()" onfocus="this.select()">PP1195E9EA</span>
                            <button type="button" class="copy-button"
                                onclick="copyToClipboard('#confirmPaymentContent')" title="Sao chép nội dung giao dịch">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row text-start">
                        <div class="col-6">
                            <small class="text-muted">Số tiền cần gửi:</small>
                            <div class="fw-bold text-primary" id="confirmAmount">$5.00</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Xu nhận được:</small>
                            <div class="fw-bold text-success" id="confirmCoins">1,000 xu</div>
                        </div>
                    </div>

                    <div class="row text-start mt-2">
                        <div class="col-12">
                            <small class="text-muted">Loại thanh toán:</small>
                            <div id="confirmPaymentMethod">
                                <span class="badge bg-success">Friends & Family</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary w-100" id="proceedPaymentBtn">
                                <i class="fab fa-paypal me-2"></i>Xác nhận thanh toán
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>
                            <strong>Hướng dẫn:</strong><br>
                            1. Nhấn "Xác nhận thanh toán" để mở PayPal<br>
                            2. Chọn đúng loại thanh toán như đã chọn<br>
                            3. Điền nội dung giao dịch vào phần Note<br>
                            4. Hoàn tất thanh toán và chụp ảnh màn hình<br>
                            5. Tải lên ảnh chứng minh để hoàn tất
                        </small>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Quan trọng:</strong> Vui lòng chọn đúng loại thanh toán để tránh sai số tiền!
                    </div>

                    
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Evidence Modal -->
    <div class="modal fade upload-modal" id="uploadEvidenceModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-image me-2"></i>Tải lên chứng minh thanh toán
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Vui lòng tải lên ảnh chứng minh đã thanh toán qua PayPal</strong>
                    </div>

                    <form id="evidenceForm">
                        @csrf
                        <input type="hidden" name="transaction_code" id="evidenceTransactionCode">

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-image me-2"></i>Ảnh chứng minh thanh toán
                            </label>
                            <div class="upload-area" onclick="document.getElementById('evidenceImage').click()">
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h6>Tải lên ảnh chứng minh</h6>
                                    <p class="text-muted small">
                                        Nhấp để chọn file hoặc kéo thả vào đây
                                        <br>Hỗ trợ: JPG, PNG, GIF (tối đa 4MB)
                                    </p>
                                </div>
                                <div id="uploadPreview" class="d-none">
                                    <img src="" id="evidencePreviewImg" class="evidence-preview">
                                </div>
                            </div>
                            <input type="file" class="d-none" id="evidenceImage" name="evidence_image"
                                accept="image/*" required>
                            <div class="invalid-feedback">Vui lòng tải lên ảnh chứng minh thanh toán</div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý:</strong> Ảnh phải rõ ràng và chứa:
                            <ul class="mb-0 mt-2">
                                <li>Số tiền đã gửi chính xác</li>
                                <li>Nội dung giao dịch: <strong id="requiredContent">-</strong></li>
                                <li>Thời gian giao dịch</li>
                                <li>Trạng thái "Completed" hoặc "Sent"</li>
                            </ul>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" id="evidenceSubmitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Gửi chứng minh
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            const coinExchangeRate = {{ $coinExchangeRate }};
            const coinPaypalRate = {{ $coinPaypalRate }};
            const coinPaypalPercent = {{ $coinPaypalPercent }};

            let currentPaymentData = null;

            // Update preview when amount or payment method changes
            $('#usdAmount, input[name="payment_method"]').on('input change', function() {
                updatePreview();
            });

            $('#usdAmount').on('blur', function() {
                let value = parseFloat($(this).val()) || 0;
                if (value > 0) {
                    value = Math.round(value / 5) * 5;
                    if (value < 5) value = 5;
                    $(this).val(value);
                    updatePreview();
                }
            });

            function updatePreview() {
                const baseUsdAmount = parseFloat($('#usdAmount').val()) || 0;
                const paymentMethod = $('input[name="payment_method"]:checked').val();

                // Calculate method fee and total
                let methodFee = 0;
                let totalUsdAmount = baseUsdAmount;
                let paymentMethodText = 'Friends & Family';

                if (paymentMethod === 'goods_services') {
                    methodFee = baseUsdAmount * 0.2; // 20% fee
                    totalUsdAmount = baseUsdAmount * 1.2;
                    paymentMethodText = 'Goods & Services';
                    $('#paymentMethodFeeRow').addClass('show');
                } else {
                    $('#paymentMethodFeeRow').removeClass('show');
                }

                // Calculate coins based on base amount (not total)
                const vndAmount = baseUsdAmount * coinPaypalRate;
                const feeAmount = (vndAmount * coinPaypalPercent) / 100;
                const amountAfterFee = vndAmount - feeAmount;
                const coins = Math.floor(amountAfterFee / coinExchangeRate);

                // Update preview
                $('#previewBaseUSD').text('$' + baseUsdAmount.toFixed(2));
                $('#previewMethodFee').text('$' + methodFee.toFixed(2));
                $('#previewTotalUSD').text('$' + totalUsdAmount.toFixed(2));
                $('#previewCoins').text(coins.toLocaleString('vi-VN') + ' xu');
                $('#previewPaymentMethod').text(paymentMethodText);

                // Highlight total amount if there's a method fee
                if (methodFee > 0) {
                    $('#previewTotalUSD').parent().addClass('highlight');
                } else {
                    $('#previewTotalUSD').parent().removeClass('highlight');
                }
            }

            // Initialize preview
            updatePreview();

            // Handle form submission - Updated to include payment method
            $('#paypalDepositForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                const baseUsdAmount = parseFloat($('#usdAmount').val());
                const paymentMethod = $('input[name="payment_method"]:checked').val();
                const paypalEmail = $('#paypalEmail').val();

                // Calculate total amount to send
                const totalUsdAmount = paymentMethod === 'goods_services' ? baseUsdAmount * 1.2 :
                    baseUsdAmount;

                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo yêu cầu...');

                $.ajax({
                    url: '{{ route('user.paypal.deposit.store') }}',
                    type: 'POST',
                    data: {
                        base_usd_amount: baseUsdAmount,
                        usd_amount: totalUsdAmount,
                        payment_method: paymentMethod,
                        paypal_email: paypalEmail,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Store payment data
                            currentPaymentData = response;

                            // Show confirm modal with payment content
                            showConfirmModal(response);

                            // Reset form
                            resetForm();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Có lỗi xảy ra khi tạo yêu cầu thanh toán';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors);
                            errorMessage = errors.flat().join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="fab fa-paypal me-2"></i>Tạo yêu cầu thanh toán');
                    }
                });
            });

            // Step 2: Proceed to PayPal and show upload modal
            $('#proceedPaymentBtn').on('click', function() {
                if (!currentPaymentData) {
                    return;
                }

                // Close confirm modal
                $('#confirmPaymentModal').modal('hide');

                // Open PayPal in new tab
                window.open(currentPaymentData.paypal_url, '_blank');

                // Show upload modal after a brief delay
                setTimeout(() => {
                    showUploadModal(currentPaymentData);
                }, 1000);
            });

            // Show confirm modal - Updated to show payment method info
            function showConfirmModal(data) {
                $('#confirmPaymentContent').text(data.payment_content);
                $('#confirmAmount').text(data.usd_amount_formatted);
                $('#confirmCoins').text(data.coins.toLocaleString() + ' xu');

                // Add payment method info to modal
                const paymentMethodBadge = data.payment_method === 'goods_services' ?
                    '<span class="badge bg-warning text-dark ms-2">Goods & Services</span>' :
                    '<span class="badge bg-success ms-2">Friends & Family</span>';

                $('#confirmPaymentMethod').html(paymentMethodBadge);

                $('#confirmPaymentModal').modal('show');
            }

            // Show upload modal
            function showUploadModal(data) {
                $('#evidenceTransactionCode').val(data.transaction_code);
                $('#requiredContent').text(data.payment_content);

                $('#uploadEvidenceModal').modal('show');
            }

            function validateForm() {
                let valid = true;

                // Validate USD amount
                const usdAmount = parseFloat($('#usdAmount').val());
                if (!usdAmount || usdAmount < 5) {
                    $('#usdAmount').addClass('is-invalid');
                    $('#amountError').text('Số tiền phải từ $5 trở lên').show();
                    valid = false;
                } else if (usdAmount % 5 !== 0) {
                    $('#usdAmount').addClass('is-invalid');
                    $('#amountError').text('Số tiền phải là bội số của $5 (ví dụ: $5, $10, $15, $20...)').show();
                    valid = false;
                } else {
                    $('#usdAmount').removeClass('is-invalid');
                    $('#amountError').hide();
                }

                // Validate email
                const email = $('#paypalEmail').val();
                if (!email || !email.includes('@')) {
                    $('#paypalEmail').addClass('is-invalid');
                    $('#emailError').text('Vui lòng nhập email PayPal hợp lệ').show();
                    valid = false;
                } else {
                    $('#paypalEmail').removeClass('is-invalid');
                    $('#emailError').hide();
                }

                return valid;
            }

            // Reset form - Updated to reset payment method
            function resetForm() {
                $('#paypalDepositForm')[0].reset();
                $('#usdAmount').val('5');
                $('input[name="payment_method"][value="friends_family"]').prop('checked', true);
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                updatePreview();
            }

            // Copy function similar to deposit bank
            function copyToClipboard(element) {
                const textToCopy = $(element).text().trim();
                const $button = $(element).next('.copy-button');
                const originalText = $button.html();

                // Show processing state
                $button.html('<i class="fas fa-spinner fa-spin"></i>');

                // Method 1: Clipboard API (works on HTTPS or localhost)
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(textToCopy)
                        .then(() => {
                            showCopySuccess($button, originalText, element);
                        })
                        .catch(() => {
                            // If method 1 fails, try method 2
                            copyUsingExecCommand(element, $button, originalText);
                        });
                }
                // Method 2: document.execCommand (legacy support)
                else {
                    copyUsingExecCommand(element, $button, originalText);
                }
            }

            // Copy method using execCommand
            function copyUsingExecCommand(element, $button, originalText) {
                try {
                    // Create text selection and select content
                    const range = document.createRange();
                    range.selectNode($(element)[0]);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);

                    // Execute copy command
                    const successful = document.execCommand('copy');

                    // Clear selection
                    window.getSelection().removeAllRanges();

                    if (successful) {
                        showCopySuccess($button, originalText, element);
                    } else {
                        // If not successful, try method 3
                        copyUsingTempTextarea($(element).text().trim(), $button, originalText, element);
                    }
                } catch (err) {
                    // If error occurs, try method 3
                    copyUsingTempTextarea($(element).text().trim(), $button, originalText, element);
                }
            }

            // Copy method using temporary textarea
            function copyUsingTempTextarea(text, $button, originalText, element) {
                try {
                    // Create temporary input element
                    const $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(text).select();

                    // Execute copy command
                    const successful = document.execCommand('copy');

                    // Cleanup
                    $temp.remove();

                    if (successful) {
                        showCopySuccess($button, originalText, element);
                    } else {
                        showCopyFailure($button, originalText);
                    }
                } catch (err) {
                    showCopyFailure($button, originalText);
                }
            }

            // Show success
            function showCopySuccess($button, originalText, element) {
                $button.html('<i class="fas fa-check"></i>');

                // Add success animation to the copied element
                $(element).addClass('copy-success');
                setTimeout(() => $(element).removeClass('copy-success'), 500);

                // Create small toast notification
                $('<div class="copy-toast success">Đã sao chép</div>')
                    .appendTo('body')
                    .fadeIn(200)
                    .delay(1500)
                    .fadeOut(200, function() {
                        $(this).remove();
                    });

                // Restore button after 1 second
                setTimeout(() => $button.html(originalText), 1000);
            }

            // Show failure
            function showCopyFailure($button, originalText) {
                $button.html('<i class="fas fa-times"></i>');

                // Show manual copy instruction
                $('<div class="copy-toast error">Không thể tự động sao chép. Vui lòng nhấp vào văn bản và chọn Sao chép.</div>')
                    .appendTo('body')
                    .fadeIn(200)
                    .delay(3000)
                    .fadeOut(200, function() {
                        $(this).remove();
                    });

                // Restore button after 1 second
                setTimeout(() => $button.html(originalText), 1000);
            }

            // Make copy function global
            window.copyToClipboard = copyToClipboard;

            // Handle evidence image upload
            $('#evidenceImage').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#evidencePreviewImg').attr('src', e.target.result);
                        $('#uploadPlaceholder').addClass('d-none');
                        $('#uploadPreview').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);

                    $('#evidenceImage').removeClass('is-invalid');
                }
            });

            // Handle evidence form submission
            $('#evidenceForm').on('submit', function(e) {
                e.preventDefault();

                if (!$('#evidenceImage').val()) {
                    $('#evidenceImage').addClass('is-invalid');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thiếu thông tin',
                        text: 'Vui lòng tải lên ảnh chứng minh thanh toán'
                    });
                    return;
                }

                const formData = new FormData(this);

                $('#evidenceSubmitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...');

                $.ajax({
                    url: '{{ route('user.paypal.deposit.confirm') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#uploadEvidenceModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Có lỗi xảy ra khi gửi xác nhận';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        $('#evidenceSubmitBtn').prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-2"></i>Gửi chứng minh');
                    }
                });
            });

            // Handle confirm payment buttons (for pending requests)
            $('.confirm-payment-btn').on('click', function() {
                const code = $(this).data('code');
                const amount = $(this).data('amount');
                const content = $(this).data('content');
                const paypalUrl = $(this).data('paypal-url');

                // Open PayPal immediately for existing requests
                window.open(paypalUrl, '_blank');

                // Show upload modal
                setTimeout(() => {
                    showUploadModal({
                        transaction_code: code,
                        payment_content: content
                    });
                }, 1000);
            });

            // Drag and drop for image upload
            const uploadArea = $('.upload-area');

            uploadArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            uploadArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            uploadArea.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    document.getElementById('evidenceImage').files = dt.files;
                    $('#evidenceImage').trigger('change');
                }
            });
        });
    </script>
@endpush
