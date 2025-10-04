{{-- filepath: resources/views/pages/information/deposit/paypal_deposit.blade.php --}}
@extends('layouts.information')

@section('info_title', 'Nạp cám bằng PayPal')
@section('info_description', 'Nạp cám bằng PayPal trên ' . request()->getHost())
@section('info_keyword', 'nạp cám, paypal, ' . request()->getHost())
@section('info_section_title', 'Nạp cám bằng PayPal')
@section('info_section_desc', 'Nạp cám bằng PayPal một cách nhanh chóng và an toàn')

@push('styles')
    <style>

        /* PayPal specific unique styles */
        #paymentMethodFeeRow {
            display: none;
        }

        #paymentMethodFeeRow.show {
            display: flex;
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
    <!-- Deposit Tabs -->
    <div class="deposit-tabs d-flex">
        <a href="{{ route('user.bank.auto.deposit') }}" class="deposit-tab">
            <i class="fas fa-robot me-2"></i>Bank auto
        </a>
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
                    <h4 class="mb-0">Nạp cám bằng PayPal</h4>
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
                            <span>Cám nhận được:</span>
                            <span id="previewTotalCoins">1,000 cám</span>
                        </div>
                        <div class="preview-item">
                            <span>Cám cộng:</span>
                            <span id="previewBaseCoins">1,000 cám</span>
                        </div>
                        <div class="preview-item">
                            <span>Cám tặng:</span>
                            <span id="previewBonusCoins">0 cám</span>
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
                    <!-- Bảng mức nạp tiền -->
                    <div class="deposit-table">
                        <h6 class="text-dark mb-3">
                            Mức quy định đổi cám hiên tại:
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">Số tiền USD</th>
                                        <th class="text-center">Cám cộng</th>
                                        <th class="text-center">Cám tặng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $usdAmounts = [5, 10, 15, 20, 25, 50];
                                    @endphp
                                    @foreach ($usdAmounts as $usdAmount)
                                        @php
                                            $vndAmount = $usdAmount * $coinPaypalRate;
                                            // Tính toán cám cơ bản
                                            $feeAmount = ($vndAmount * $coinPaypalPercent) / 100;
                                            $amountAfterFee = $vndAmount - $feeAmount;
                                            $baseCoins = floor($amountAfterFee / $coinExchangeRate);

                                            // Tính toán bonus theo công thức hàm mũ
                                            $bonusCoins = calculateBonusCoins($amountAfterFee, $bonusBaseAmount, $bonusBaseCam, $bonusDoubleAmount, $bonusDoubleCam);

                                            $totalCoins = $baseCoins + $bonusCoins;
                                        @endphp
                                        <tr class="{{ $usdAmount == 10 ? 'table-primary' : '' }}">
                                            <td class="text-center fw-bold">${{ $usdAmount }}</td>
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
                            <div class="text-muted">{{ number_format($latestPendingRequest->coins) }} cám</div>
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
                                        <th>Cám cộng</th>
                                        <th>Cám tặng</th>
                                        <th>Tổng cám</th>
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
                                                @if(isset($deposit->bonus_coins) && $deposit->bonus_coins > 0)
                                                    <span class="text-success">+{{ number_format($deposit->bonus_coins) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ number_format($deposit->coins) }}</strong>
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
                            <p class="text-muted">Hãy thực hiện giao dịch đầu tiên để nạp cám vào tài khoản</p>
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
                            <small class="text-muted">Cám nhận được:</small>
                            <div class="fw-bold text-success" id="confirmCoins">1,000 cám</div>
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
            const bonusBaseAmount = {{ $bonusBaseAmount }};
            const bonusBaseCam = {{ $bonusBaseCam }};
            const bonusDoubleAmount = {{ $bonusDoubleAmount }};
            const bonusDoubleCam = {{ $bonusDoubleCam }};

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
                const baseCoins = Math.floor(amountAfterFee / coinExchangeRate);

                // Calculate bonus theo công thức hàm mũ
                let bonusCoins = 0;

                if (amountAfterFee >= bonusBaseAmount) {
                    // Tính số mũ b
                    const ratioAmount = bonusDoubleAmount / bonusBaseAmount; // 200000/100000 = 2
                    const ratioBonus = bonusDoubleCam / bonusBaseCam; // 1000/300 = 3.333...
                    const b = Math.log(ratioBonus) / Math.log(ratioAmount); // ≈ 1.737

                    // Tính hệ số a
                    const a = bonusBaseCam / Math.pow(bonusBaseAmount, b);

                    // Tính bonus theo công thức: bonus = a * (amountAfterFee)^b
                    bonusCoins = Math.floor(a * Math.pow(amountAfterFee, b));
                }

                const totalCoins = baseCoins + bonusCoins;

                // Update preview
                $('#previewBaseUSD').text('$' + baseUsdAmount.toFixed(2));
                $('#previewMethodFee').text('$' + methodFee.toFixed(2));
                $('#previewTotalUSD').text('$' + totalUsdAmount.toFixed(2));
                $('#previewBaseCoins').text(baseCoins.toLocaleString('vi-VN') + ' cám');
                $('#previewBonusCoins').text(bonusCoins.toLocaleString('vi-VN') + ' cám');
                $('#previewTotalCoins').text(totalCoins.toLocaleString('vi-VN') + ' cám');
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
                $('#confirmCoins').text(data.coins.toLocaleString() + ' cám');

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
