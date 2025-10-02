<!-- Chapter Report Modal -->
<div class="modal fade" id="reportChapterModal" tabindex="-1" aria-labelledby="reportChapterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="reportChapterModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Báo lỗi chương
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Thông tin:</strong> Chương {{ $chapter->number }} từ truyện "{{ $story->title }}"
                </div>

                <form id="chapterReportForm">
                    @csrf
                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">

                    <div class="mb-3">
                        <label for="reportDescription" class="form-label fw-bold">
                            Mô tả chi tiết về lỗi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="reportDescription" name="description"
                            rows="6" placeholder="Vui lòng mô tả chi tiết về lỗi bạn gặp phải trong chương này (ít nhất 10 ký tự)..."
                            required minlength="10" maxlength="1000"></textarea>
                        <div class="form-text">
                            Số ký tự: <span id="charCount">0</span>/1000
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Lưu ý:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Báo cáo phải chính xác và có cơ sở</li>
                                <li>Không spam báo cáo không đúng</li>
                                <li>Admin sẽ xem xét và xử lý trong thời gian sớm nhất</li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Hủy
                </button>
                <button type="button" class="btn btn-danger" id="submitReportBtn">
                    <i class="fas fa-paper-plane me-1"></i>Gửi báo cáo
                </button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('reportChapterModal');
                const form = document.getElementById('chapterReportForm');
                const textarea = document.getElementById('reportDescription');
                const charCount = document.getElementById('charCount');
                const submitBtn = document.getElementById('submitReportBtn');

                // Character counter
                textarea.addEventListener('input', function() {
                    const count = this.value.length;
                    charCount.textContent = count;

                    if (count < 10) {
                        charCount.style.color = '#dc3545';
                    } else {
                        charCount.style.color = '#198754';
                    }
                });

                // Submit report
                submitBtn.addEventListener('click', function() {
                    const description = textarea.value.trim();

                    if (description.length < 10) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Mô tả quá ngắn',
                            text: 'Vui lòng mô tả chi tiết về lỗi (ít nhất 10 ký tự).'
                        });
                        return;
                    }

                    if (description.length > 1000) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Mô tả quá dài',
                            text: 'Mô tả không được vượt quá 1000 ký tự.'
                        });
                        return;
                    }

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang gửi...';

                    const formData = new FormData(form);

                    fetch('{{ route('chapter.report.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công!',
                                    text: data.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    modal.querySelector('.btn-close').click();
                                    form.reset();
                                    charCount.textContent = '0';
                                    charCount.style.color = '';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Đã xảy ra lỗi khi gửi báo cáo. Vui lòng thử lại.'
                            });
                        })
                        .finally(() => {
                            // Restore button
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Gửi báo cáo';
                        });
                });

                // Reset form when modal is closed
                modal.addEventListener('hidden.bs.modal', function() {
                    form.reset();
                    charCount.textContent = '0';
                    charCount.style.color = '';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Gửi báo cáo';
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .modal-header.bg-danger {
                background-color: #dc3545 !important;
            }

            .form-control:focus {
                border-color: #dc3545;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            }

            .btn-danger {
                background-color: #dc3545;
                border-color: #dc3545;
            }

            .btn-danger:hover {
                background-color: #c82333;
                border-color: #bd2130;
            }
        </style>
    @endpush
@endonce
