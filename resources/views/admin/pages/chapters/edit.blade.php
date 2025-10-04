@extends('admin.layouts.app')

@push('styles-admin')
    <!-- Thêm các style tùy chỉnh nếu cần -->
@endpush

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0 px-3">
                    <h5 class="mb-0">Chỉnh sửa chương {{ $chapter->number }}</h5>
                </div>
                <div class="card-body pt-4 p-3">

                    

                    <form action="{{ route('admin.stories.chapters.update', ['story' => $story, 'chapter' => $chapter]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="number">Số chương</label>
                                    <input type="number" name="number" id="number" class="form-control"
                                        value="{{ old('number', $chapter->number) }}" required>
                                    @error('number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="views">Trạng thái</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="published" {{ $chapter->status == 'published' ? 'selected' : '' }}>
                                            Hiển thị</option>
                                        <option value="draft" {{ $chapter->status == 'draft' ? 'selected' : '' }}>Viết nháp
                                        </option>
                                    </select>
                                </div>
                            </div>

                            @if($chapter->status === 'draft' && $chapter->scheduled_publish_at)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scheduled_publish_at">Thời gian hẹn đăng</label>
                                    <input type="datetime-local" name="scheduled_publish_at" id="scheduled_publish_at" 
                                        class="form-control" 
                                        value="{{ old('scheduled_publish_at', $chapter->scheduled_publish_at ? $chapter->scheduled_publish_at->format('Y-m-d\TH:i') : '') }}">
                                    <small class="text-muted">Chỉ có thể chỉnh sửa khi chương đang ở trạng thái nháp</small>
                                    @error('scheduled_publish_at')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="form-group">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="mb-0 me-3" for="is_free">Nội dung miễn phí</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_free" class="form-check-input" id="is_free"
                                                role="switch" {{ $chapter->is_free ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" id="price-container" style="{{ $chapter->is_free ? 'display: none;' : '' }}">
                                <div class="form-group">
                                    <label for="price">Giá (cám)</label>
                                    <input type="number" name="price" id="price" class="form-control"
                                        value="{{ old('price', $chapter->price) }}" min="0">
                                    <small class="text-muted">Số cám cần để đọc chương này</small>
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
                                        value="{{ old('title', $chapter->title) }}" required>
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
                                    <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content', $chapter->content) }}</textarea>
                                    @error('content')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
                                <a href="{{ route('admin.stories.chapters.index', $story) }}" class="btn btn-secondary">Trở
                                    về</a>
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

            // Check if the current title matches the auto-generated pattern
            const currentTitle = "{{ $chapter->title }}";
            const currentNumber = "{{ $chapter->number }}";
            if (currentTitle === 'Chương ' + currentNumber) {
                autoGenerateCheckbox.checked = true;
                titleInput.readOnly = true;
            }

            // Update title when checkbox is clicked
            autoGenerateCheckbox.addEventListener('change', updateTitle);

            // Update title when number changes if checkbox is checked
            numberInput.addEventListener('input', function() {
                if (autoGenerateCheckbox.checked) {
                    updateTitle();
                }
            });

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

            // Handle status change for scheduled_publish_at field
            const statusSelect = document.getElementById('status');
            const scheduledField = document.getElementById('scheduled_publish_at');
            
            if (scheduledField) {
                function toggleScheduledField() {
                    if (statusSelect.value === 'draft') {
                        scheduledField.parentElement.parentElement.style.display = 'block';
                    } else {
                        scheduledField.parentElement.parentElement.style.display = 'none';
                    }
                }
                
                statusSelect.addEventListener('change', toggleScheduledField);
                toggleScheduledField(); // Initial call
            }
        });
    </script>
@endpush
