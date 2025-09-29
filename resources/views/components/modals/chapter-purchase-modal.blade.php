<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Mua chương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="purchase-info text-center mb-4">
                    <div class="purchase-item-info">
                        <h5 id="purchase-item-title"></h5>
                        <p class="text-muted">Để đọc nội dung này, bạn cần mua với giá <span id="purchase-item-price" class="fw-bold text-primary"></span> xu.</p>
                    </div>
                    <div class="user-balance mt-3 alert alert-info">
                        <i class="fas fa-coins me-2"></i> Số dư của bạn: <span id="user-balance" class="fw-bold">{{ auth()->check() ? number_format(auth()->user()->coins) : 0 }}</span> xu
                    </div>
                    <div id="insufficient-balance" class="alert alert-warning d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i> Bạn không đủ xu để mua. Vui lòng nạp thêm.
                        <div class="mt-2">
                            <a href="{{ route('user.deposit') }}" class="btn btn-sm btn-warning">Nạp xu ngay</a>
                        </div>
                    </div>
                </div>
                <form id="purchase-form" method="POST">
                    @csrf
                    <input type="hidden" id="purchase-type" name="purchase_type" value="chapter">
                    <input type="hidden" id="purchase-item-id" name="chapter_id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-purchase-btn">Xác nhận mua</button>
            </div>
        </div>
    </div>
</div>

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
        
        // Update modal content based on purchase type
        if (type === 'chapter') {
            modalTitle.textContent = 'Xác nhận mua chương';
            itemTitle.textContent = title;
            purchaseForm.action = "{{ route('purchase.chapter') }}";
            itemId.name = 'chapter_id';
        } else if (type === 'story') {
            modalTitle.textContent = 'Xác nhận mua trọn bộ';
            itemTitle.textContent = 'Trọn bộ: ' + title;
            purchaseForm.action = "{{ route('purchase.story.combo') }}";
            itemId.name = 'story_id';
        }
        
        // Update price and ID
        itemPrice.textContent = new Intl.NumberFormat().format(price);
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
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';
        
        // Prepare form data
        const formData = new FormData(purchaseForm);
        
        // Determine the correct endpoint
        const endpoint = purchaseType === 'chapter' 
            ? "{{ route('purchase.chapter') }}" 
            : "{{ route('purchase.story.combo') }}";
        
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
            } catch(e) {
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
            } catch(e) {
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