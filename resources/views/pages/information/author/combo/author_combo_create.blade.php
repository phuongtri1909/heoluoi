@extends('layouts.information')

@section('info_title', 'Tạo combo truyện')
@section('info_description', 'Tạo combo truyện để người đọc mua trọn bộ với giá ưu đãi')
@section('info_keyword', 'tạo combo, truyện, giá combo, truyện full')
@section('info_section_title', 'Tạo combo truyện')
@section('info_section_desc', 'Truyện: ' . $story->title)

@section('info_content')
    <div class="mb-4">
        <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0">Tạo combo trọn bộ</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Combo truyện cho phép độc giả mua trọn bộ tất cả các chương với mức giá ưu đãi so với mua từng chương.
            </div>
            
            <form action="{{ route('user.author.stories.combo.store', $story->id) }}" method="POST">
                @csrf
                
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
                                       value="{{ old('combo_price', floor($totalChapterPrice * 0.8)) }}" min="1" max="{{ $totalChapterPrice - 1 }}">
                                <span class="input-group-text"><i class="fas fa-coins"></i></span>
                            </div>
                            <div class="form-text">Giá combo phải thấp hơn tổng giá các chương.</div>
                            @error('combo_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-success" id="discount-info">
                    <i class="fas fa-percentage me-2"></i> Mức giảm giá: <strong><span id="discount-percentage">20</span>%</strong>
                    <br>
                    <i class="fas fa-piggy-bank me-2"></i> Độc giả sẽ tiết kiệm: <strong><span id="saving-amount">{{ number_format(floor($totalChapterPrice * 0.2)) }}</span> xu</strong>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-outline-secondary me-2">Hủy</a>
                    <button type="submit" class="btn action-btn-primary">Tạo combo</button>
                </div>
            </form>
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
        
        // Trigger the input event to initialize values
        $('#combo_price').trigger('input');
        
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