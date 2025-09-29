@extends('layouts.information')

@section('info_title', 'Cập nhật giá hàng loạt')
@section('info_description', 'Cập nhật giá hàng loạt cho các chương của truyện ' . $story->title)
@section('info_keyword', 'cập nhật giá, chương, tác giả, ' . request()->getHost())
@section('info_section_title', 'Cập nhật giá hàng loạt')
@section('info_section_desc', 'Truyện: ' . $story->title)

@push('styles')
    <style>
        .chapter-card {
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .chapter-card.selected {
            border-color: #0d6efd;
            background-color: #e3f2fd;
        }

        .chapter-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .price-input {
            max-width: 120px;
        }

        .update-type-card {
            border: 2px solid #e9ecef;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .update-type-card.selected {
            border-color: #0d6efd;
            background-color: #f8f9ff;
        }

        .selection-toolbar {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('info_content')
    <div class="mb-4">
        <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if ($chapters->count() === 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Không có chương nào đã xuất bản để cập nhật giá.
        </div>
    @else
        <form action="{{ route('user.author.stories.chapters.bulk-price.update', $story->id) }}" method="POST"
            id="bulkPriceForm">
            @csrf
            @method('PUT')

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-outline-dark text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $chapters->count() }}</h5>
                            <p class="card-text">Chương đã xuất bản</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info">{{ number_format($chapters->sum('price')) }} xu</h5>
                            <p class="card-text">Tổng giá hiện tại</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success" id="selectedCount">0</h5>
                            <p class="card-text">Chương được chọn</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selection Toolbar -->
            <div class="selection-toolbar">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-2">Chọn chương:</h6>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                                <i class="fas fa-check-double me-1"></i> Chọn tất cả
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectNone()">
                                <i class="fas fa-times me-1"></i> Bỏ chọn
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="selectPaid()">
                                <i class="fas fa-coins me-1"></i> Chỉ chương có phí
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" id="showPriceOnly">
                            <label class="form-check-label" for="showPriceOnly">
                                Chỉ hiện chương có phí
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Type Selection -->
            <div class="mb-4">
                <h5 class="mb-3">Chọn phương thức cập nhật:</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="update-type-card card h-100" onclick="selectUpdateType('all_same')">
                            <div class="card-body text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="update_type" id="all_same"
                                        value="all_same" {{ old('update_type') == 'all_same' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="all_same">
                                        <i class="fas fa-equals text-primary fa-2x mb-2 d-block"></i>
                                        <h6>Áp dụng cùng giá</h6>
                                        <p class="text-muted small mb-0">Tất cả chương được chọn sẽ có cùng giá</p>
                                    </label>
                                </div>
                                <div class="mt-3" id="allPriceField" style="display: none;">
                                    <label for="all_price" class="form-label">Giá áp dụng (xu):</label>
                                    <input type="number"
                                        class="form-control price-input mx-auto @error('all_price') is-invalid @enderror"
                                        id="all_price" name="all_price" value="{{ old('all_price', '') }}" min="0"
                                        step="1" placeholder="Để trống = miễn phí">
                                    <small class="form-text text-muted">Để trống hoặc nhập 0 để đặt chương miễn phí</small>
                                    @error('all_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="update-type-card card h-100" onclick="selectUpdateType('individual')">
                            <div class="card-body text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="update_type" id="individual"
                                        value="individual" {{ old('update_type') == 'individual' ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="individual">
                                        <i class="fas fa-list-ol text-warning fa-2x mb-2 d-block"></i>
                                        <h6>Tùy chỉnh từng chương</h6>
                                        <p class="text-muted small mb-0">Có thể đặt giá khác nhau cho từng chương</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @error('update_type')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Chapters List -->
            <div class="mb-4">
                <h5 class="mb-3">Danh sách chương:</h5>
                <div class="row g-1" id="chaptersContainer">
                    @foreach ($chapters as $chapter)
                        <div class="col-12 chapter-item" data-is-free="{{ $chapter->is_free ? 'true' : 'false' }}">
                            <div class="chapter-card card h-100" onclick="toggleChapter({{ $chapter->id }})">
                                <div class="card-body d-flex justify-content-between align-items-center py-2 ">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input chapter-checkbox" type="checkbox"
                                            name="selected_chapters[]" value="{{ $chapter->id }}"
                                            id="chapter_{{ $chapter->id }}"
                                            {{ in_array($chapter->id, old('selected_chapters', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="chapter_{{ $chapter->id }}">
                                            Chương {{ $chapter->number }} :
                                        </label>

                                        <h6 class="card-title mb-0">{{ $chapter->title }}</h6>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            @if ($chapter->is_free)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-gift me-1"></i>Miễn phí
                                                </span>
                                                @if ($chapter->password)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-lock"></i> Có mật khẩu
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-coins me-1"></i>{{ number_format($chapter->price) }}
                                                    xu
                                                </span>
                                            @endif
                                        </div>


                                        <!-- Individual Price Input (hidden by default) -->
                                        <div class="individual-price-field" style="display: none;">
                                            <label class="form-label small">Giá mới (xu):</label>
                                            <input type="number"
                                                class="form-control form-control-sm price-input @error('chapter_prices.' . $chapter->id) is-invalid @enderror"
                                                name="chapter_prices[{{ $chapter->id }}]"
                                                value="{{ old('chapter_prices.' . $chapter->id, $chapter->is_free ? '' : $chapter->price) }}"
                                                min="0" step="1" placeholder="Trống = miễn phí">
                                            <small class="form-text text-muted">Trống/0 = miễn phí</small>
                                            @error('chapter_prices.' . $chapter->id)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('selected_chapters')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Section -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">Đã chọn: <strong id="selectedCountText">0</strong> chương</span>
                </div>
                <div>
                    <a href="{{ route('user.author.stories.chapters', $story->id) }}"
                        class="btn btn-outline-danger me-2">
                        Hủy
                    </a>
                    <button type="submit" class="btn btn-outline-dark" id="submitBtn" disabled>
                        Cập nhật giá
                    </button>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            // Initialize
            updateSelectedCount();

            // Update type change handler
            $('input[name="update_type"]').change(function() {
                togglePriceFields();
            });

            // Chapter selection change handler
            $('.chapter-checkbox').change(function() {
                updateChapterCard(this);
                updateSelectedCount();
            });

            // Show price only filter
            $('#showPriceOnly').change(function() {
                filterChapters();
            });

            // Initialize state
            togglePriceFields();
            filterChapters();
        });

        function selectUpdateType(type) {
            $(`#${type}`).prop('checked', true);
            $('.update-type-card').removeClass('selected');
            $(`#${type}`).closest('.update-type-card').addClass('selected');
            togglePriceFields();
        }

        function togglePriceFields() {
            const updateType = $('input[name="update_type"]:checked').val();

            // Update card styling
            $('.update-type-card').removeClass('selected');
            if (updateType) {
                $(`#${updateType}`).closest('.update-type-card').addClass('selected');
            }

            if (updateType === 'all_same') {
                $('#allPriceField').show();
                $('.individual-price-field').hide();
            } else if (updateType === 'individual') {
                $('#allPriceField').hide();
                $('.individual-price-field').show();
            } else {
                $('#allPriceField').hide();
                $('.individual-price-field').hide();
            }
        }

        function toggleChapter(chapterId) {
            const checkbox = $(`#chapter_${chapterId}`);
            checkbox.prop('checked', !checkbox.prop('checked'));
            updateChapterCard(checkbox[0]);
            updateSelectedCount();
        }

        function updateChapterCard(checkbox) {
            const card = $(checkbox).closest('.chapter-card');
            if (checkbox.checked) {
                card.addClass('selected');
            } else {
                card.removeClass('selected');
            }
        }

        function updateSelectedCount() {
            const selectedCount = $('.chapter-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);
            $('#selectedCountText').text(selectedCount);

            // Enable/disable submit button
            $('#submitBtn').prop('disabled', selectedCount === 0);
        }

        function selectAll() {
            $('.chapter-item:visible .chapter-checkbox').each(function() {
                $(this).prop('checked', true);
                updateChapterCard(this);
            });
            updateSelectedCount();
        }

        function selectNone() {
            $('.chapter-checkbox').each(function() {
                $(this).prop('checked', false);
                updateChapterCard(this);
            });
            updateSelectedCount();
        }

        function selectPaid() {
            $('.chapter-checkbox').each(function() {
                const chapterItem = $(this).closest('.chapter-item');
                const isFree = chapterItem.data('is-free');

                if (!isFree && chapterItem.is(':visible')) {
                    $(this).prop('checked', true);
                    updateChapterCard(this);
                } else {
                    $(this).prop('checked', false);
                    updateChapterCard(this);
                }
            });
            updateSelectedCount();
        }

        function filterChapters() {
            const showPriceOnly = $('#showPriceOnly').is(':checked');

            $('.chapter-item').each(function() {
                const isFree = $(this).data('is-free');

                if (showPriceOnly && isFree) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });

            updateSelectedCount();
        }

        // Form validation
        $('#bulkPriceForm').submit(function(e) {
            e.preventDefault();

            const selectedCount = $('.chapter-checkbox:checked').length;
            const updateType = $('input[name="update_type"]:checked').val();

            if (selectedCount === 0) {
                showToast('Vui lòng chọn ít nhất một chương để cập nhật giá.', 'warning');
                return false;
            }

            if (!updateType) {
                showToast('Vui lòng chọn phương thức cập nhật.', 'warning');
                return false;
            }

            if (updateType === 'all_same') {
                const allPrice = $('#all_price').val();
                // Cho phép giá trị rỗng (miễn phí) hoặc >= 0
                if (allPrice !== '' && (isNaN(allPrice) || parseFloat(allPrice) < 0)) {
                    showToast('Vui lòng nhập giá hợp lệ (≥ 0 xu hoặc để trống cho miễn phí).', 'warning');
                    $('#all_price').focus();
                    return false;
                }
            }

            // Validation cho individual prices
            if (updateType === 'individual') {
                let hasInvalidPrice = false;
                $('.individual-price-field input').each(function() {
                    const price = $(this).val();
                    if (price !== '' && (isNaN(price) || parseFloat(price) < 0)) {
                        hasInvalidPrice = true;
                        $(this).focus();
                        return false;
                    }
                });

                if (hasInvalidPrice) {
                    showToast('Vui lòng nhập giá hợp lệ (≥ 0 xu hoặc để trống cho miễn phí).', 'warning');
                    return false;
                }
            }

            // Confirmation with SweetAlert2
            Swal.fire({
                title: 'Xác nhận cập nhật?',
                html: `
                    <p>Cập nhật giá cho <strong>${selectedCount}</strong> chương được chọn</p>
                    <small class="text-muted">
                        • Để trống hoặc nhập 0: Chương miễn phí<br>
                        • Nhập giá > 0: Chương có phí
                    </small>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Cập nhật',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    $('#submitBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Đang cập nhật...');
                    $('#submitBtn').prop('disabled', true);

                    // Submit form
                    this.submit();
                }
            });
        });
    </script>
@endpush
