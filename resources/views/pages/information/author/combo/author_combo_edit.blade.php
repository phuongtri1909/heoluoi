@extends('layouts.information')

@section('info_title', 'Chỉnh sửa combo truyện')
@section('info_description', 'Chỉnh sửa combo truyện để người đọc mua trọn bộ với giá ưu đãi')
@section('info_keyword', 'chỉnh sửa combo, truyện, giá combo, truyện full')
@section('info_section_title', 'Chỉnh sửa combo truyện')
@section('info_section_desc', 'Truyện: ' . $story->title)

@section('info_content')
    <div class="mb-4">
        <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0">Chỉnh sửa combo trọn bộ</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('user.author.stories.combo.update', $story->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_price" class="form-label">Tổng giá nếu mua lẻ từng chương</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="total_price" value="{{ number_format($totalChapterPrice) }}" readonly>
                                <span class="input-group-text"><i class="fas fa-coins"></i></span>
                            </div>
                            <div class="form-text">Tổng số xu nếu độc giả mua lẻ từng chương.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="combo_price" class="form-label">Giá combo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('combo_price') is-invalid @enderror" id="combo_price" name="combo_price" 
                                       value="{{ old('combo_price', $story->combo_price) }}" min="1" max="{{ $totalChapterPrice - 1 }}">
                                <span class="input-group-text"><i class="fas fa-coins"></i></span>
                            </div>
                            <div class="form-text">Giá combo phải thấp hơn tổng giá các chương.</div>
                            @error('combo_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="has_combo" name="has_combo" 
                           {{ old('has_combo', $story->has_combo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_combo">Hiển thị combo cho độc giả</label>
                    <div class="form-text">Bỏ chọn nếu bạn muốn tạm ẩn combo.</div>
                </div>
                
                <div class="alert alert-success" id="discount-info">
                    @php
                        $discountPercentage = $totalChapterPrice > 0 ? round(($totalChapterPrice - $story->combo_price) / $totalChapterPrice * 100) : 0;
                    @endphp
                    <i class="fas fa-percentage me-2"></i> Mức giảm giá: <strong><span id="discount-percentage">{{ $discountPercentage }}</span>%</strong>
                    <br>
                    <i class="fas fa-piggy-bank me-2"></i> Độc giả sẽ tiết kiệm: <strong><span id="saving-amount">{{ number_format($totalChapterPrice - $story->combo_price) }}</span> xu</strong>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteComboModal">
                        <i class="fas fa-trash-alt me-1"></i> Xóa combo
                    </button>
                    
                    <div>
                        <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-outline-secondary me-2">Hủy</a>
                        <button type="submit" class="btn action-btn-primary">Cập nhật combo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal xác nhận xóa combo -->
    <div class="modal fade" id="deleteComboModal" tabindex="-1" aria-labelledby="deleteComboModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteComboModalLabel">Xác nhận xóa combo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa combo này?</p>
                    <p>Sau khi xóa, bạn vẫn có thể tạo lại combo mới nếu muốn.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('user.author.stories.combo.destroy', $story->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
<script>
    $(document).ready(function() {
        const totalPrice = {{ $totalChapterPrice }};
        
        // Calculate and update discount information when price changes
        $('#combo_price').on('input', function() {
            const comboPrice = parseInt($(this).val()) || 0;
            
            // Prevent division by zero
            if (totalPrice <= 0) {
                $('#discount-percentage').text('0');
                $('#saving-amount').text('0');
                return;
            }
            
            // Calculate discount percentage
            const discountAmount = totalPrice - comboPrice;
            const discountPercentage = (discountAmount / totalPrice * 100).toFixed(0);
            
            // Update UI
            $('#discount-percentage').text(discountPercentage);
            $('#saving-amount').text(discountAmount.toLocaleString('vi-VN'));
            
            // Change alert color based on discount value
            const discountAlert = $('#discount-info');
            if (discountPercentage < 10) {
                discountAlert.removeClass('alert-success alert-warning').addClass('alert-danger');
            } else if (discountPercentage < 20) {
                discountAlert.removeClass('alert-success alert-danger').addClass('alert-warning');
            } else {
                discountAlert.removeClass('alert-warning alert-danger').addClass('alert-success');
            }
        });
        
        // Validate max price on form submit
        $('form').submit(function() {
            const price = parseInt($('#combo_price').val());
            if (!price || price <= 0) {
                alert('Vui lòng nhập giá combo hợp lệ.');
                return false;
            }
            
            if (totalPrice > 0 && price >= totalPrice) {
                alert('Giá combo phải thấp hơn tổng giá các chương riêng lẻ (' + totalPrice + ' xu).');
                return false;
            }
            return true;
        });
    });
</script>
@endpush