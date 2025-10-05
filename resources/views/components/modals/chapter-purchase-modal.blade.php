<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0 text-center">
                <div class="w-100">
                    <h5 class="modal-title fw-bold color-7 mb-0" id="purchaseModalLabel">Xác nhận mua chương</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="purchase-info text-center">
                    <div class="purchase-item-info mb-3">
                        <h5 id="purchase-item-title" class="fw-bold mb-2"></h5>
                        <p class="fw-semibold mb-0 " id="purchase-item-price">Bạn cần ủng hộ <span class="fw-bold color-7"></span> Cám để đọc chương này</p>
                    </div>

                    <div class="user-balance mt-3 p-3 bg-light rounded-3">
                        <p class="mb-0 fw-semibold">
                            <i class="fas fa-coins me-2 text-warning"></i>
                            Hiện bạn đang có <span id="user-balance"
                                class="fw-bold color-7">{{ auth()->check() ? number_format(auth()->user()->coins) : 0 }}</span>
                            Cám
                        </p>
                    </div>

                    <div id="insufficient-balance" class="alert alert-warning mt-3 d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i> Bạn không đủ Cám để đọc chương này. Vui lòng
                        nạp thêm.
                        <div class="mt-2">
                            <a href="{{ route('user.deposit') }}" class="btn btn-sm btn-warning">Nạp Cám ngay</a>
                        </div>
                    </div>

                    <div class="dots mt-3 mb-3">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="purchase-info-list text-start">
                        <ul class="fw-semibold small text-muted mb-0" id="purchase-info-list">
                            <li>Sau khi mua, bạn có thể [Đọc chương] này không giới hạn số lần.</li>
                            <li>Bạn chỉ bị trừ Cám khi [Đọc chương] này lần đầu tiên.</li>
                            <li>Kiểm tra Cám hiện tại <a href="{{ route('user.profile') }}" class="color-7">Tài
                                    khoản</a>. Nạp thêm Cám tại <a href="{{ route('user.deposit') }}"
                                    class="color-7">Nạp Cám</a>.</li>
                        </ul>
                    </div>
                </div>
                <form id="purchase-form" method="POST">
                    @csrf
                    <input type="hidden" id="purchase-type" name="purchase_type" value="chapter">
                    <input type="hidden" id="purchase-item-id" name="chapter_id" value="">
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn bg-7 fw-bold text-dark px-4" id="confirm-purchase-btn">
                    <span id="purchase-item-icon"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Purchase Modal Dots Animation */
        .dots span {
            width: 10px;
            height: 10px;
            background: var(--primary-color-7);
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .purchase-info-list ul {
            padding-left: 1.2rem;
        }

        .purchase-info-list li {
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        /* Dark mode support for modal */
        body.dark-mode .modal-content {
            background-color: #2d2d2d;
            color: #e0e0e0;
        }

        body.dark-mode .modal-header {
            background-color: #404040 !important;
        }

        body.dark-mode .user-balance {
            background-color: #404040 !important;
            color: #e0e0e0;
        }

        body.dark-mode .purchase-info-list {
            color: #ccc;
        }

        body.dark-mode .dots span {
            background: var(--primary-color-7);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variables to store current purchase information
        window.userCoins = {{ auth()->check() ? auth()->user()->coins : 0 }};

        // Function to open purchase modal
        function showPurchaseModal(type, id, title, price) {
            const modalTitle = document.getElementById('purchaseModalLabel');
            const itemTitle = document.getElementById('purchase-item-title');
            const itemPrice = document.getElementById('purchase-item-price');
            const itemId = document.getElementById('purchase-item-id');
            const purchaseType = document.getElementById('purchase-type');
            const purchaseForm = document.getElementById('purchase-form');
            const userBalance = document.getElementById('user-balance');
            const insufficientBalance = document.getElementById('insufficient-balance');
            const confirmBtn = document.getElementById('confirm-purchase-btn');
            const itemIcon = document.getElementById('purchase-item-icon');
            const purchaseInfoList = document.getElementById('purchase-info-list');

            // Update modal content based on purchase type
            if (type === 'chapter') {
                modalTitle.textContent = 'Xác nhận mua chương';
                itemTitle.textContent = title;
                purchaseForm.action = "{{ route('purchase.chapter') }}";
                itemId.name = 'chapter_id';
                itemPrice.innerHTML = 'Bạn cần ủng hộ ' + '<span class="fw-bold color-7">' + new Intl.NumberFormat().format(price) + '</span>' + ' Cám để đọc chương này';
                itemIcon.innerHTML = '<i class="fas fa-shopping-cart me-1"></i> Mua chương';
                purchaseInfoList.innerHTML = `
                    <li>Sau khi mua, bạn có thể <span class="fw-semibold color-7">[Đọc chương]</span> này không giới hạn số lần.</li>
                    <li>Bạn chỉ bị trừ Cám khi <span class="fw-semibold color-7">[Đọc chương]</span> này lần đầu tiên.</li>
                    <li>Kiểm tra Cám hiện tại <a href="{{ route('user.profile') }}" class="color-7">Tài khoản</a>. Nạp thêm Cám tại <a href="{{ route('user.deposit') }}" class="color-7">Nạp Cám</a>.</li>
                `;
            } else if (type === 'story') {
                modalTitle.textContent = 'Xác nhận mua trọn bộ';
                itemTitle.textContent = 'Trọn bộ: ' + title;
                purchaseForm.action = "{{ route('purchase.story.combo') }}";
                itemId.name = 'story_id';
                itemPrice.innerHTML = 'Bạn cần ủng hộ ' + '<span class="fw-bold color-7">' + new Intl.NumberFormat().format(price) + '</span>' + ' Cám để đọc truyện này';
                itemIcon.innerHTML = '<i class="fas fa-shopping-cart me-1"></i> Mua truyện';
                purchaseInfoList.innerHTML = `
                    <li>Sau khi mua, bạn có thể <span class="fw-semibold color-7">[Đọc truyện]</span> này không giới hạn số lần.</li>
                    <li>Sau khi full truyện, bạn sẽ không tốn <span class="fw-semibold color-7">Cám</span> khi đọc các chương lẻ</li>
                    <li>Kiểm tra Cám hiện tại <a href="{{ route('user.profile') }}" class="color-7">Tài khoản</a>. Nạp thêm Cám tại <a href="{{ route('user.deposit') }}" class="color-7">Nạp Cám</a>.</li>
                `;
            }

            // Update price and ID
            
            itemId.value = id;
            purchaseType.value = type;

            // Check if user has enough balance
            if (window.userCoins < price) {
                insufficientBalance.classList.remove('d-none');
                confirmBtn.disabled = true;
            } else {
                insufficientBalance.classList.add('d-none');
                confirmBtn.disabled = false;
            }

            // Open the modal
            const purchaseModal = new bootstrap.Modal(document.getElementById('purchaseModal'));
            purchaseModal.show();
        }

        // Handle purchase confirmation
        document.getElementById('confirm-purchase-btn').addEventListener('click', function() {
            const purchaseForm = document.getElementById('purchase-form');
            const purchaseType = document.getElementById('purchase-type').value;
            const itemId = document.getElementById('purchase-item-id').value;

            if (!itemId) return;

            // Show loading state
            this.disabled = true;
            this.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';

            // Prepare form data
            const formData = new FormData(purchaseForm);

            // Determine the correct endpoint
            const endpoint = purchaseType === 'chapter' ?
                "{{ route('purchase.chapter') }}" :
                "{{ route('purchase.story.combo') }}";

            // Send purchase request
            fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Try to close modal
                    try {
                        bootstrap.Modal.getInstance(document.getElementById('purchaseModal')).hide();
                    } catch (e) {
                        console.warn('Không thể đóng modal:', e);
                    }

                    if (data.success) {
                        // Success - show message
                        Swal.fire({
                            title: 'Thành công!',
                            text: data.message || 'Mua thành công! Đang tải nội dung...',
                            icon: 'success',
                            confirmButtonText: 'Đọc ngay',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            // Redirect to the page
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        // Error
                        Swal.fire({
                            title: 'Lỗi',
                            text: data.message || 'Có lỗi xảy ra khi xử lý giao dịch.',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Try to close modal
                    try {
                        bootstrap.Modal.getInstance(document.getElementById('purchaseModal')).hide();
                    } catch (e) {
                        console.error('Error closing modal:', e);
                    }

                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi kết nối đến máy chủ. Vui lòng thử lại sau.',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                })
                .finally(() => {
                    // Reset button state
                    this.disabled = false;
                    this.innerHTML = 'Xác nhận mua';
                });
        });
    </script>
@endpush
