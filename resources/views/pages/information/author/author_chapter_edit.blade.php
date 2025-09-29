@extends('layouts.information')

@section('info_title', 'Chỉnh sửa chương')
@section('info_description', 'Chỉnh sửa chương của truyện ' . $story->title)
@section('info_keyword', 'sửa chương, đăng truyện, tác giả, ' . request()->getHost())
@section('info_section_title', 'Chỉnh sửa chương')
@section('info_section_desc', 'Truyện: ' . $story->title)

@push('styles')
    <style>
        .cke_contents {
            min-height: 300px;
        }

        .chapter-options {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .toggle-password {
            cursor: pointer;
        }

        .pricing-options,
        .password-options,
        .publish-options {
            padding-top: 15px;
        }

        .form-text.text-muted {
            font-size: 0.85em;
        }

        .content-stats {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .content-stats .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .content-stats .stat-number {
            font-weight: 600;
            color: #495057;
        }
    </style>
@endpush

@section('info_content')
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <a href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}"
                class="btn btn-sm btn-outline-primary ms-2">
               Xem Chương
            </a>
        </div>

        <div class="d-flex gap-2">
            @if ($prevChapter)
                <a href="{{ route('user.author.stories.chapters.edit', [$story->id, $prevChapter->id]) }}"
                    class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-angle-left me-1"></i> Chương trước
                </a>
            @endif

            @if ($nextChapter)
                <a href="{{ route('user.author.stories.chapters.edit', [$story->id, $nextChapter->id]) }}"
                    class="btn btn-sm btn-outline-primary">
                    Chương sau <i class="fas fa-angle-right ms-1"></i>
                </a>
            @endif
        </div>
    </div>


    <form action="{{ route('user.author.stories.chapters.update', ['story' => $story->id, 'chapter' => $chapter->id]) }}"
        method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="number" class="form-label">Số chương <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('number') is-invalid @enderror" id="number"
                        name="number" value="{{ old('number', $chapter->number) }}" required>
                    @error('number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-8">
                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề chương </label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title', $chapter->title) }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Nội dung chương <span class="text-danger">*</span></label>
            <textarea class="form-control rounded-4 @error('content') is-invalid @enderror" id="content" name="content"
                rows="25">{{ old('content', $chapter->content) }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <!-- Thêm thống kê từ và ký tự -->
            <div class="mt-2 d-flex justify-content-between">
                <div class="text-muted small">
                    <i class="fas fa-file-word me-1 text-primary"></i>
                    <span id="wordCount">0</span> từ
                </div>
                <div class="text-muted small">
                    <i class="fas fa-keyboard me-1 text-info"></i>
                    <span id="charCount">0</span> ký tự
                </div>
            </div>
        </div>

        <!-- Phần tùy chọn chương -->
        <div class="chapter-options">
            <h5 class="mb-3">Tùy chọn</h5>

            <div class="row">
                <div class="col-12 col-md-4">
                    <!-- Hình thức chương -->
                    <div class="mb-3">
                        <label class="form-label">Hình thức chương <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_free" id="is_free_yes" value="1"
                                {{ old('is_free', $chapter->is_free ? '1' : '0') == '1' ? 'checked' : '' }}
                                onchange="togglePricingOptions()">
                            <label class="form-check-label" for="is_free_yes">
                                <i class="fas fa-unlock text-success me-1"></i> Miễn phí
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_free" id="is_free_no" value="0"
                                {{ old('is_free', $chapter->is_free ? '1' : '0') == '0' ? 'checked' : '' }}
                                onchange="togglePricingOptions()">
                            <label class="form-check-label" for="is_free_no">
                                <i class="fas fa-coins text-warning me-1"></i> Có phí
                            </label>
                        </div>
                        @error('is_free')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Giá chương (hiển thị khi chọn Có phí) -->
                    <div class="pricing-options" id="pricingOptions">
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá chương (Xu) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                                name="price" value="{{ old('price', $chapter->price ?? 5) }}" min="1">
                            <div class="form-text text-muted">Mức giá tối thiểu là 1 xu.</div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <!-- Tùy chọn xuất bản -->
                    <div class="">
                        <div class="mb-3">
                            <label class="form-label">Xuất bản chương <span class="text-danger">*</span></label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="status" id="status_published"
                                    value="published"
                                    {{ old('status', $chapter->status) == 'published' ? 'checked' : '' }}
                                    onchange="toggleScheduleOptions()">
                                <label class="form-check-label" for="status_published">
                                    <i class="fas fa-check-circle text-success me-1"></i> Xuất bản ngay
                                </label>

                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_draft"
                                    value="draft" {{ old('status', $chapter->status) == 'draft' ? 'checked' : '' }}
                                    onchange="toggleScheduleOptions()">
                                <label class="form-check-label" for="status_draft">
                                    <i class="fas fa-edit text-secondary me-1"></i> Lưu nháp
                                </label>

                            </div>

                        </div>

                        <!-- Thêm tùy chọn lịch xuất bản -->
                        <div class="mb-3" id="scheduleOptionsContainer"
                            style="{{ old('status', $chapter->status) == 'published' ? 'display: none;' : '' }}">
                            <label class="form-label d-flex align-items-center">
                                <input type="checkbox" class="form-check-input me-2" id="enableSchedule"
                                    onchange="toggleScheduleField()"
                                    {{ old('published_at', $chapter->published_at) ? 'checked' : '' }}>
                                Hẹn giờ xuất bản
                            </label>
                            <div id="scheduleField"
                                style="{{ old('published_at', $chapter->published_at) ? '' : 'display: none;' }}">
                                <input type="datetime-local"
                                    class="form-control @error('published_at') is-invalid @enderror"
                                    id="published_at" name="published_at"
                                    value="{{ old('published_at', $chapter->published_at ? $chapter->published_at->format('Y-m-d\TH:i') : '') }}">
                                <div class="form-text text-muted">Chương sẽ tự động xuất bản vào thời gian đã chọn.</div>
                            </div>
                            @error('published_at')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <!-- Mật khẩu (hiển thị khi chọn Miễn phí) -->
                    <div class="" id="passwordOptions">
                        <div class="mb-3">
                            <label class="form-label">Chương có mật khẩu không?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="has_password" id="has_password_no"
                                    value="0"
                                    {{ old('has_password', !empty($chapter->password) ? '1' : '0') == '0' ? 'checked' : '' }}
                                    onchange="togglePasswordField()">
                                <label class="form-check-label" for="has_password_no">
                                    <i class="fas fa-lock-open text-success me-1"></i> Không có mật khẩu
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="has_password" id="has_password_yes"
                                    value="1"
                                    {{ old('has_password', !empty($chapter->password) ? '1' : '0') == '1' ? 'checked' : '' }}
                                    onchange="togglePasswordField()">
                                <label class="form-check-label" for="has_password_yes">
                                    <i class="fas fa-lock text-warning me-1"></i> Có mật khẩu
                                </label>
                            </div>

                        </div>

                        <div class="mb-3" id="passwordField"
                            style="{{ !empty($chapter->password) ? '' : 'display: none;' }}">
                            <label for="password" class="form-label">Mật khẩu chương <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password"
                                    placeholder="{{ !empty($chapter->password) ? '********' : '' }}">
                                <span class="input-group-text toggle-password" onclick="togglePasswordVisibility()">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @if (!empty($chapter->password))
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Chương này đã có mật khẩu. Để giữ nguyên mật
                                    khẩu hiện
                                    tại, hãy để trống trường này.
                                </div>
                            @else
                                <div class="form-text text-muted">Người đọc cần nhập đúng mật khẩu để xem chương này.</div>
                            @endif
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            
                            <div class="mt-2">
                                <label for="password_hint" class="form-label">Hướng dẫn lấy mật khẩu</label>
                                <textarea class="form-control @error('password_hint') is-invalid @enderror" 
                                    id="password_hint" name="password_hint" rows="2" 
                                    placeholder="Ví dụ: Nhập phép tính 1 + 1 = ? hoặc hướng dẫn tìm mật khẩu...">{{ old('password_hint', $chapter->password_hint) }}</textarea>
                                <div class="form-text text-muted">Hướng dẫn người đọc cách lấy mật khẩu để xem chương</div>
                                @error('password_hint')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-outline-danger me-2">Hủy</a>
            <button type="submit" class="btn btn-outline-dark">
                Cập nhật
            </button>
        </div>
    </form>
@endsection

@push('info_scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Khởi tạo hiển thị tùy thuộc vào trạng thái ban đầu
            togglePricingOptions();
            togglePasswordField();
            toggleScheduleOptions();

            // Khởi tạo đếm từ và ký tự
            updateContentStats();

            // Bind event listeners cho content textarea
            $('#content').on('input paste keyup', function() {
                updateContentStats();
            });
        });

        // Function to count words in Vietnamese text
        function countWords(text) {
            if (!text || typeof text !== 'string') return 0;

            // Remove extra whitespace and normalize
            const normalizedText = text.replace(/\s+/g, ' ').trim();

            if (normalizedText === '') return 0;

            // Count words by splitting on whitespace
            const words = normalizedText.split(' ').filter(word => word.length > 0);

            return words.length;
        }

        // Function to update content statistics
        function updateContentStats() {
            const content = $('#content').val();
            const wordCount = countWords(content);
            const charCount = content.length;

            // Update display with number formatting
            $('#wordCount').text(wordCount.toLocaleString());
            $('#charCount').text(charCount.toLocaleString());

            // Add color coding based on content length
            const wordCountElement = $('#wordCount');
            const charCountElement = $('#charCount');

            // Reset classes
            wordCountElement.removeClass('text-danger text-warning text-success');
            charCountElement.removeClass('text-danger text-warning text-success');

            // Word count color coding
            if (wordCount === 0) {
                wordCountElement.addClass('text-danger');
            } else if (wordCount < 100) {
                wordCountElement.addClass('text-warning');
            } else {
                wordCountElement.addClass('text-success');
            }

            // Character count color coding
            if (charCount === 0) {
                charCountElement.addClass('text-danger');
            } else if (charCount < 500) {
                charCountElement.addClass('text-warning');
            } else {
                charCountElement.addClass('text-success');
            }
        }

        function togglePricingOptions() {
            var isFree = $('#is_free_yes').is(':checked');
            if (isFree) {
                $('#pricingOptions').hide();
                $('#passwordOptions').show();
            } else {
                $('#pricingOptions').show();
                $('#passwordOptions').hide();
            }
        }

        function togglePasswordField() {
            var hasPassword = $('#has_password_yes').is(':checked');
            if (hasPassword) {
                $('#passwordField').show();
            } else {
                $('#passwordField').hide();
            }
        }

        function toggleScheduleOptions() {
            var isDraft = $('#status_draft').is(':checked');
            if (isDraft) {
                $('#scheduleOptionsContainer').show();
            } else {
                $('#scheduleOptionsContainer').hide();
                // Uncheck and reset schedule fields when publishing immediately
                $('#enableSchedule').prop('checked', false);
                $('#scheduleField').hide();
                $('#published_at').val('');
            }
        }

        function togglePasswordVisibility() {
            var passwordField = document.getElementById('password');
            var icon = document.querySelector('.toggle-password i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function toggleScheduleField() {
            var enableSchedule = $('#enableSchedule').is(':checked');
            if (enableSchedule) {
                $('#scheduleField').show();
            } else {
                $('#scheduleField').hide();
            }
        }
    </script>
@endpush
