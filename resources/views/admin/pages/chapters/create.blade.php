@extends('admin.layouts.app')

@push('styles-admin')
    <!-- Thêm các style tùy chỉnh nếu cần -->
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0 px-3">
                    <h5 class="mb-0">Tạo chương</h5>
                </div>
                <div class="card-body pt-4 p-3">

                    

                    <form action="{{ route('admin.stories.chapters.store', $story) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="number">Số chương</label>
                                    <input type="number" name="number" id="number" class="form-control"
                                        value="{{ old('number', $nextChapterNumber) }}" required>
                                    @error('number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="views">Trạng thái</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="published">Hiển thị</option>
                                        <option value="draft">Viết nháp</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="mb-0 me-3" for="is_free">Nội dung miễn phí</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_free" class="form-check-input" id="is_free"
                                                role="switch" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" id="price-container" style="display: none;">
                                <div class="form-group">
                                    <label for="price">Giá (xu)</label>
                                    <input type="number" name="price" id="price" class="form-control"
                                        value="{{ old('price', 0) }}" min="0">
                                    <small class="text-muted">Số xu cần để đọc chương này</small>
                                    @error('price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="title">Tên chương</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="autoGenerateTitle">
                                            <label class="form-check-label" for="autoGenerateTitle">Tự động đặt tên</label>
                                        </div>
                                    </div>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ old('title') }}" required>
                                    <small class="text-muted">Khi chọn tự động, tên chương sẽ là "Chương {số
                                        chương}"</small>
                                    @error('title')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Nội dung</label>
                                    <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content') }}</textarea>
                                    @error('content-')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                                <a href="{{ route('admin.stories.chapters.index', $story) }}" class="btn btn-secondary">Trở về</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles-admin')
    <style>
        .form-check-input {
            width: 3em;
        }

        .form-switch .form-check-input {
            height: 1.5em;
        }

        .form-switch .form-check-input:checked {
            background-color: #5e72e4;
            border-color: #5e72e4;
        }

        .form-switch .form-check-input:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255, 255, 255, 0.25%29'/%3e%3c/svg%3e");
        }

        .form-switch .form-check-input:after {
            top: 3px !important;
        }
    </style>
@endpush

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const numberInput = document.getElementById('number');
            const titleInput = document.getElementById('title');
            const autoGenerateCheckbox = document.getElementById('autoGenerateTitle');

            // Function to update the title when number changes
            function updateTitle() {
                if (autoGenerateCheckbox.checked) {
                    titleInput.value = 'Chương ' + numberInput.value;
                    titleInput.readOnly = true;
                } else {
                    titleInput.readOnly = false;
                }
            }

            // Update title when checkbox is clicked
            autoGenerateCheckbox.addEventListener('change', updateTitle);

            // Update title when number changes if checkbox is checked
            numberInput.addEventListener('input', function() {
                if (autoGenerateCheckbox.checked) {
                    updateTitle();
                }
            });

            // Check if we should restore auto-generate state
            const oldTitle = "{{ old('title', '') }}";
            const oldNumber = "{{ old('number', $nextChapterNumber) }}";
            if (oldTitle === 'Chương ' + oldNumber) {
                autoGenerateCheckbox.checked = true;
                updateTitle();
            }

            // Handle is_free toggle for price field
            const isFreeCheckbox = document.getElementById('is_free');
            const priceContainer = document.getElementById('price-container');

            isFreeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    priceContainer.style.display = 'none';
                    document.getElementById('price').value = '0';
                } else {
                    priceContainer.style.display = 'block';
                }
            });

            // Initialize based on old input if form was submitted with errors
            if ("{{ old('is_free') }}" === '0') {
                isFreeCheckbox.checked = false;
                priceContainer.style.display = 'block';
            }
        });
    </script>
@endpush
