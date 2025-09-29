@extends('layouts.information')

@section('info_title', 'Thêm nhiều chương')
@section('info_description', 'Thêm nhiều chương cho truyện ' . $story->title)
@section('info_keyword', 'thêm chương, đăng truyện, tác giả, ' . request()->getHost())
@section('info_section_title', 'Thêm nhiều chương')
@section('info_section_desc', 'Truyện: ' . $story->title)

@push('styles')
    <style>
        .batch-upload-area {
            display: block;
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

        .batch-format-example {
            background-color: #eee;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-line;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .preview-chapters {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }

        .schedule-input {
            margin-top: 10px;
        }

        #guideContent {
            transition: all 0.3s ease;
        }

        #toggleGuideBtn {
            transition: all 0.2s ease;
        }

        #toggleGuideBtn:hover {
            transform: translateY(-1px);
        }
    </style>

    <style>
        #guideContent {
            transition: all 0.3s ease;
        }

        #toggleGuideBtn {
            transition: all 0.2s ease;
        }

        #toggleGuideBtn:hover {
            transform: translateY(-1px);
        }

        #previewContent {
            transition: all 0.3s ease;
        }

        #togglePreviewBtn {
            transition: all 0.2s ease;
        }

        #togglePreviewBtn:hover {
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('info_content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('user.author.stories.chapters', $story->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>

        <a href="{{ route('user.author.stories.chapters.create', $story->id) }}" class="btn btn-sm btn-outline-dark">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <div class="mb-4">
        <div>
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i> {!! session('error') !!}
                </div>
            @endif
            <div class="alert bg-primary-bg-2 mb-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Hướng dẫn thêm nhiều chương</h5>
                    <button type="button" class="btn btn-sm btn-outline-dark" id="toggleGuideBtn" onclick="toggleGuide()">
                        <i class="fas fa-chevron-down" id="guideIcon"></i>
                    </button>
                </div>

                <div id="guideContent" class="mt-3" style="display: none;">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2">Copy & paste nội dung với định dạng sau:</p>
                            <div class="batch-format-example">Chương 1: Hai con heo
Nội dung chương 1 ở đây...

Chương 2: Ba con chó
Nội dung chương 2 ở đây...
                            </div>
                        </div>
                        <div class="col-6">
                            <p class="mb-2">Hoặc định dạng không có tiêu đề:</p>
                            <div class="batch-format-example">Chương 1
                                Nội dung chương 1 ở đây...

                                Chương 2
                                Nội dung chương 2 ở đây...
                            </div>
                        </div>
                    </div>


                    <ul class="mt-3 mb-0">
                        <li>Hệ thống sẽ <strong>giữ nguyên số chương</strong> từ tiêu đề (ví dụ: "Chương 5" sẽ được
                            lưu là chương số 5)</li>
                        <li>Nếu chương không có tiêu đề, hệ thống sẽ tự động đặt tiêu đề là "Chương X"</li>
                        <li>Có thể có <strong>khoảng trắng trước "Chương"</strong> (ví dụ: "  Chương 1")</li>
                        <li>Các chương đã tồn tại (trùng số) sẽ được thông báo</li>
                        <li><strong>Tiêu đề chương có thể trùng nhau</strong> - hệ thống sẽ tự động tạo slug unique</li>
                        <li>Cài đặt về giá, mật khẩu và trạng thái sẽ áp dụng cho tất cả các chương mới</li>
                    </ul>
                </div>
            </div>

            <form action="{{ route('user.author.stories.chapters.batch.store', $story->id) }}" method="POST"
                id="batchChapterForm">
                @csrf

                <div class="mb-3">
                    <label for="batch_content" class="form-label">Nội dung các chương <span
                            class="text-danger">*</span></label>
                    <div class="ckeditor-container">
                        <textarea class="form-control rounded-4 @error('batch_content') is-invalid @enderror" id="batch_content"
                            name="batch_content" rows="15">{{ old('batch_content') }}</textarea>
                    </div>
                    @error('batch_content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="text-center">
                    <button type="button" id="btnPreviewChapters" class="btn btn-outline-dark mb-3 ">
                        Xem chương
                    </button>
                </div>


                <!-- Preview chapters section -->
                <div id="previewChapters"
                    class="preview-chapters bg-primary-bg-2 mb-4 rounded-4 {{ old('chapters_count') ? '' : 'd-none' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Xem trước các chương</h5>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="togglePreviewBtn"
                            onclick="togglePreview()">
                            <i class="fas fa-chevron-up" id="previewIcon"></i>
                        </button>
                    </div>

                    <div id="previewContent">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> Vui lòng kiểm tra các chương đã tách
                            và
                            thiết lập lịch đăng (nếu cần)
                        </div>
                        <div id="chaptersPreviewContainer">
                            @if (old('chapters_count'))
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Vui lòng nhấn "Xem trước các chương" để
                                    hiển thị lại các chương đã tách.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Phần tùy chọn chương hàng loạt -->
                <div class="chapter-options rounded-4">
                    <h5 class="mb-3">Tùy chọn</h5>

                    <div class="row">
                        <div class="col-12 col-md-4 row">
                            <div class="col-12">
                                <!-- Hình thức chương -->
                                <div class="mb-3">
                                    <label class="form-label">Hình thức chương <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_free" id="is_free_yes"
                                            value="1" {{ old('is_free', '1') == '1' ? 'checked' : '' }}
                                            onchange="togglePricingOptions()">
                                        <label class="form-check-label" for="is_free_yes">
                                            <i class="fas fa-unlock text-success me-1"></i> Miễn phí
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_free" id="is_free_no"
                                            value="0" {{ old('is_free') == '0' ? 'checked' : '' }}
                                            onchange="togglePricingOptions()">
                                        <label class="form-check-label" for="is_free_no">
                                            <i class="fas fa-coins text-warning me-1"></i> Có phí
                                        </label>
                                    </div>
                                    @error('is_free')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <!-- Giá chương hàng loạt -->
                                <div class="pricing-options" id="pricingOptions">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Giá chương (Xu) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                            id="price" name="price" value="{{ old('price', 5) }}" min="1">
                                        <div class="form-text text-muted">Áp dụng cho tất cả chương.
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tùy chọn xuất bản hàng loạt -->
                        <div class="col-12 col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Xuất bản chương <span class="text-danger">*</span></label>

                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="status" id="status_published"
                                        value="published" {{ old('status','published') == 'published' ? 'checked' : '' }}
                                        onchange="toggleScheduleOptions()">
                                    <label class="form-check-label" for="status_published">
                                        <i class="fas fa-check-circle text-success me-1"></i> Xuất bản ngay
                                    </label>

                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_draft"
                                        value="draft" {{ old('status') == 'draft' ? 'checked' : '' }}
                                        onchange="toggleScheduleOptions()">
                                    <label class="form-check-label" for="status_draft">
                                        <i class="fas fa-edit text-secondary me-1"></i> Lưu nháp
                                    </label>

                                </div>

                            </div>

                            <div class="mb-3" id="scheduleOptionsContainer"
                                style="{{ old('status') == 'published' ? 'display: none;' : '' }}">
                                <label class="form-label d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input me-2" id="enableSchedule"
                                        onchange="toggleScheduleField()"
                                        {{ old('published_at') ? 'checked' : '' }}>
                                    Hẹn giờ xuất bản
                                </label>
                                <div id="scheduleField"
                                    style="{{ old('published_at') ? '' : 'display: none;' }}">
                                    <input type="datetime-local"
                                        class="form-control @error('published_at') is-invalid @enderror"
                                        id="published_at" name="published_at"
                                        value="{{ old('published_at') }}">
                                    <div class="form-text text-muted">Tất cả chương sẽ tự động xuất bản vào thời gian
                                        đã chọn (trừ khi có lịch riêng).</div>

                                    <!-- Thêm input cho khoảng cách giờ -->
                                    <div class="mt-2">
                                        <label for="hours_interval" class="form-label">
                                            Khoảng cách giờ giữa các chương
                                            <small class="text-muted">(tùy chọn)</small>
                                        </label>
                                        <input type="number"
                                            class="form-control @error('hours_interval') is-invalid @enderror"
                                            id="hours_interval" name="hours_interval"
                                            value="{{ old('hours_interval') }}" min="0" step="0.5"
                                            placeholder="Ví dụ: 2">

                                        @error('hours_interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @error('published_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mật khẩu hàng loạt -->
                        <div class="col-12 col-md-4" id="passwordOptions">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Các chương có mật khẩu không?</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="has_password"
                                                id="has_password_no" value="0"
                                                {{ old('has_password', '0') == '0' ? 'checked' : '' }}
                                                onchange="togglePasswordField()">
                                            <label class="form-check-label" for="has_password_no">
                                                <i class="fas fa-lock-open text-success me-1"></i> Không có mật khẩu
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="has_password"
                                                id="has_password_yes" value="1"
                                                {{ old('has_password') == '1' ? 'checked' : '' }}
                                                onchange="togglePasswordField()">
                                            <label class="form-check-label" for="has_password_yes">
                                                <i class="fas fa-lock text-warning me-1"></i> Có mật khẩu
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3" id="passwordField"
                                        style="{{ old('has_password') == '1' ? '' : 'display: none;' }}">
                                        <label for="password" class="form-label">Mật khẩu chương <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password" value="{{ old('password') }}">
                                            <span class="input-group-text toggle-password"
                                                onclick="togglePasswordVisibility()">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="form-text text-muted">Áp dụng cho tất cả các chương.
                                        </div>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>


                <div class="d-flex justify-content-end">
                    <a href="{{ route('user.author.stories.chapters', $story->id) }}"
                        class="btn btn-outline-danger me-2">Hủy</a>
                    <button type="submit" class="btn btn-outline-dark">
                        Lưu
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        function togglePreview() {
            const previewContent = $('#previewContent');
            const icon = $('#previewIcon');

            if (previewContent.is(':visible')) {
                previewContent.slideUp(200);
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                previewContent.slideDown(200);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        }

        function toggleGuide() {
            const guideContent = document.getElementById('guideContent');
            const guideIcon = document.getElementById('guideIcon');
            const toggleBtn = document.getElementById('toggleGuideBtn');

            if (guideContent.style.display === 'none') {
                guideContent.style.display = 'block';
                guideIcon.classList.remove('fa-chevron-down');
                guideIcon.classList.add('fa-chevron-up');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-up me-1" id="guideIcon"></i> Ẩn hướng dẫn';
            } else {
                guideContent.style.display = 'none';
                guideIcon.classList.remove('fa-chevron-up');
                guideIcon.classList.add('fa-chevron-down');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-down me-1" id="guideIcon"></i> Xem hướng dẫn';
            }
        }

        // Khởi tạo CKEditor
        let editor;
        
        $(document).ready(function() {
            // Khởi tạo CKEditor với cấu hình tùy chỉnh
            editor = CKEDITOR.replace('batch_content', {
                toolbar: [
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                    { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize'] }
                ],
                height: 400,
                removePlugins: 'elementspath,resize',
                resize_enabled: false,
                allowedContent: true,
                extraAllowedContent: 'h1,h2,h3,h4,h5,h6,span[style]',
                format_tags: 'p;h1;h2;h3;h4;h5;h6',
                font_names: 'Arial/Arial, Helvetica, sans-serif;Comic Sans MS/Comic Sans MS, cursive;Courier New/Courier New, courier;Georgia/Georgia, serif;Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;Tahoma/Tahoma, Geneva, sans-serif;Times New Roman/Times New Roman, Times, serif;Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;Verdana/Verdana, Geneva, sans-serif',
                fontSize_sizes: '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px',
                colorButton_colors: '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF',
                colorButton_foreStyle: {
                    element: 'span',
                    styles: { 'color': '#(color)' }
                },
                colorButton_backStyle: {
                    element: 'span',
                    styles: { 'background-color': '#(color)' }
                }
            });

            // Lắng nghe sự kiện thay đổi nội dung
            editor.on('change', function() {
                // Cập nhật textarea ẩn với nội dung text thuần túy
                updatePlainTextContent();
            });

            togglePricingOptions();
            togglePasswordField();
            toggleScheduleOptions();

            if ($('#has_password_yes').is(':checked')) {
                $('#passwordField').show();
            }

            @if (old('chapters_count'))
                setTimeout(function() {
                    $('#btnPreviewChapters').click();
                }, 500);

                @foreach (old('chapter_schedules', []) as $chapterNum => $schedule)
                    @if (!empty($schedule))
                        setTimeout(function() {
                            $('#enableSchedule_{{ $chapterNum }}').prop('checked', true);
                            $('#scheduleField_{{ $chapterNum }}').removeClass('d-none');
                            $('#schedule_{{ $chapterNum }}').val('{{ $schedule }}');
                        }, 1000);
                    @endif
                @endforeach
            @endif

            // Function để cập nhật nội dung text thuần túy
            function updatePlainTextContent() {
                if (editor) {
                    const htmlContent = editor.getData();
                    const plainText = stripHtmlTags(htmlContent);
                    const decodedText = decodeHtmlEntities(plainText);
                    $('#batch_content').val(decodedText);
                }
            }

            // Function để loại bỏ HTML tags và giữ lại text thuần túy
            function stripHtmlTags(html) {
                if (!html) return '';
                
                // Tạo element tạm để parse HTML
                const temp = document.createElement('div');
                temp.innerHTML = html;
                
                // Lấy text content và chuẩn hóa
                let text = temp.textContent || temp.innerText || '';
                
                // Chuẩn hóa line breaks và spaces
                text = text.replace(/\r\n/g, '\n');
                text = text.replace(/\r/g, '\n');
                text = text.replace(/\n\s*\n\s*\n/g, '\n\n'); // Loại bỏ nhiều line breaks liên tiếp
                text = text.replace(/[ \t]+/g, ' '); // Chuẩn hóa spaces
                text = text.trim();
                
                return text;
            }

            // Function để decode HTML entities
            function decodeHtmlEntities(text) {
                const textarea = document.createElement('textarea');
                textarea.innerHTML = text;
                return textarea.value;
            }

            // Xử lý xem trước các chương
            $('#btnPreviewChapters').click(function() {
                // Lấy nội dung từ CKEditor và chuyển thành text thuần túy
                updatePlainTextContent();
                const batchContent = $('#batch_content').val();

                if (!batchContent.trim()) {
                    showToast('Vui lòng nhập nội dung các chương trước khi xem trước.', 'warning');
                    return;
                }

                // Hiển thị loading
                $('#btnPreviewChapters').html('<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...');
                $('#btnPreviewChapters').prop('disabled', true);

                // Phân tích nội dung chương
                const chapters = parseChaptersFromContent(batchContent);

                if (chapters.length === 0) {
                    showToast(
                        'Không tìm thấy chương nào trong nội dung đã nhập. Vui lòng kiểm tra định dạng.',
                        'warning');
                    $('#btnPreviewChapters').html('Xem chương');
                    $('#btnPreviewChapters').prop('disabled', false);
                    return;
                }

                checkDuplicateChapters(chapters).then(function(result) {
                    displayParsedChapters(chapters, result.duplicates, result);

                    $('#btnPreviewChapters').html('Cập nhật xem trước');
                    $('#btnPreviewChapters').prop('disabled', false);
                }).catch(function(error) {
                    console.error('Error checking duplicates:', error);
                    displayParsedChapters(chapters, [], {});

                    $('#btnPreviewChapters').html('Cập nhật xem trước');
                    $('#btnPreviewChapters').prop('disabled', false);
                });
            });

            // Function to parse chapters from content
            function parseChaptersFromContent(content) {
                const chapters = [];

                const normalizedContent = content.replace(/\r\n/g, '\n');

                const chapterBlocks = normalizedContent.split(/\n(?=\s*Chương\s+\d+)/);

                chapterBlocks.forEach(block => {
                    if (!block.trim()) return;

                    const lines = block.trim().split('\n');
                    const headingLine = lines[0];

                    const headingMatch = headingLine.match(/^\s*Chương\s+(\d+)(?:\s*:\s*(.*))?$/);

                    if (headingMatch) {
                        const chapterNumber = parseInt(headingMatch[1]);
                        const title = headingMatch[2] ? headingMatch[2].trim() : `Chương ${chapterNumber}`;

                        const content = lines.slice(1).join('\n').trim();

                        chapters.push({
                            number: chapterNumber,
                            title: title,
                            content: content
                        });
                    }
                });

                return chapters;
            }

            // Function to create slug (mimic Laravel Str::slug)
            function createSlug(text) {
                return text
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[đĐ]/g, 'd')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            }

            // Function to count words in Vietnamese text
            function countWords(text) {
                if (!text || typeof text !== 'string') return 0;
                const normalizedText = text.replace(/\s+/g, ' ').trim();
                if (normalizedText === '') return 0;
                const words = normalizedText.split(' ').filter(word => word.length > 0);

                return words.length;
            }

            // Function to find internal duplicates trong chính input
            function findInternalDuplicates(chapters) {
                const duplicateNumbers = [];
                const seenNumbers = new Set();

                chapters.forEach(chapter => {
                    if (seenNumbers.has(chapter.number)) {
                        if (!duplicateNumbers.includes(chapter.number)) {
                            duplicateNumbers.push(chapter.number);
                        }
                    } else {
                        seenNumbers.add(chapter.number);
                    }
                });

                return {
                    numbers: duplicateNumbers,
                    titles: []
                };
            }

            // Function to check duplicate chapters via AJAX
            function checkDuplicateChapters(chapters) {
                return new Promise(function(resolve, reject) {
                    const internalDuplicates = findInternalDuplicates(chapters);
                    const chapterNumbers = chapters.map(ch => ch.number);

                    $.ajax({
                        url: '{{ route('user.author.stories.chapters.check-duplicates', $story->id) }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            chapter_numbers: chapterNumbers,
                            chapter_titles: []
                        },
                        success: function(response) {
                            const allDuplicateNumbers = [
                                ...internalDuplicates.numbers,
                                ...(response.duplicate_numbers || [])
                            ];

                            const uniqueDuplicateNumbers = [...new Set(allDuplicateNumbers)];

                            resolve({
                                duplicates: uniqueDuplicateNumbers,
                                duplicateTitles: [],
                                internalDuplicates: internalDuplicates,
                                databaseDuplicates: {
                                    numbers: response.duplicate_numbers || [],
                                    titles: []
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            resolve({
                                duplicates: internalDuplicates.numbers,
                                duplicateTitles: [],
                                internalDuplicates: internalDuplicates,
                                databaseDuplicates: {
                                    numbers: [],
                                    titles: []
                                }
                            });
                        }
                    });
                });
            }

            // Function to display parsed chapters với kiểm tra trùng lặp và đếm từ
            function displayParsedChapters(chapters, duplicateNumbers = [], duplicateInfo = {}) {
                const container = $('#chaptersPreviewContainer');
                container.empty();

                let html = '';

                if (duplicateNumbers.length > 0) {
                    html += '<div class="alert alert-danger mb-3">';
                    html +=
                        '<h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Phát hiện trùng lặp:</h6>';

                    if (duplicateInfo.internalDuplicates &&
                        duplicateInfo.internalDuplicates.numbers.length > 0) {
                        html += '<div class="mb-2">';
                        html +=
                            '<strong><i class="fas fa-file-alt text-danger me-1"></i>Trùng lặp trong nội dung nhập:</strong>';

                        html += '<br><span class="ms-3">• Số chương: ' + duplicateInfo.internalDuplicates
                            .numbers.join(', ') + '</span>';
                        html += '</div>';
                    }

                    // Show database duplicates
                    if (duplicateInfo.databaseDuplicates &&
                        duplicateInfo.databaseDuplicates.numbers.length > 0) {
                        html += '<div class="mb-2">';
                        html +=
                            '<strong><i class="fas fa-database text-warning me-1"></i>Chương đã tồn tại:</strong>';

                        html += '<br><span class="ms-3">• Số chương: ' + duplicateInfo.databaseDuplicates
                            .numbers.join(', ') + '</span>';
                        html += '</div>';
                    }

                    // General note
                    html += '<div class="mt-2">';
                    html += '<small class="text-muted">';
                    html += '<strong>Lưu ý:</strong> ';

                    if (duplicateInfo.internalDuplicates &&
                        duplicateInfo.internalDuplicates.numbers.length > 0) {
                        html += 'Các chương trùng lặp trong nội dung nhập cần được chỉnh sửa. ';
                    }

                    if (duplicateInfo.databaseDuplicates &&
                        duplicateInfo.databaseDuplicates.numbers.length > 0) {
                        html += 'Các chương trùng với database sẽ không được tạo mới. ';
                    }

                    html += 'Vui lòng chỉnh sửa trước khi lưu.';
                    html += '</small>';
                    html += '</div>';

                    html += '</div>';
                }

                html += '<div class="table-responsive"><table class="table table-bordered table-hover">';
                html += '<thead class="table-light">';
                html += '<tr>';
                html += '<th>STT</th>';
                html += '<th>Số chương</th>';
                html += '<th>Tiêu đề</th>';
                html += '<th>Slug</th>';
                html += '<th>Số từ</th>';
                html += '<th>Số ký tự</th>';
                html += '<th>Trạng thái</th>';
                html += '<th>Hẹn giờ đăng riêng</th>';
                html += '</tr>';
                html += '</thead><tbody>';

                // Add info alert about scheduling when "Xuất bản ngay" is selected
                const isPublished = $('#status_published').is(':checked');
                if (isPublished) {
                    html = `
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i> <strong>Lưu ý:</strong> Bạn đã chọn "Xuất bản ngay" cho tất cả chương.
                            Nếu bạn đặt lịch riêng cho một chương, chương đó sẽ được lưu ở trạng thái nháp và sẽ được xuất bản theo lịch.
                        </div>
                    ` + html;
                }

                const duplicateNumbersSet = new Set();

                chapters.forEach(chapter => {
                    const chapterNumberCount = chapters.filter(ch => ch.number === chapter.number).length;

                    // Check internal duplicates
                    if (chapterNumberCount > 1) {
                        duplicateNumbersSet.add(chapter.number);
                    }

                    // Check database duplicates
                    if (duplicateNumbers.includes(chapter.number)) {
                        duplicateNumbersSet.add(chapter.number);
                    }
                });

                chapters.forEach((chapter, index) => {
                    const wordCount = countWords(chapter.content);
                    const charCount = chapter.content.length;

                    // Check if this chapter is duplicate
                    const isDuplicateNumber = duplicateNumbersSet.has(chapter.number);
                    const isDuplicate = isDuplicateNumber;

                    // Determine duplicate type for better labeling
                    let duplicateType = '';
                    const isInternalDuplicateNumber = duplicateInfo.internalDuplicates &&
                        duplicateInfo.internalDuplicates.numbers.includes(chapter.number);
                    const isDatabaseDuplicateNumber = duplicateInfo.databaseDuplicates &&
                        duplicateInfo.databaseDuplicates.numbers.includes(chapter.number);

                    if (isInternalDuplicateNumber) {
                        if (isDatabaseDuplicateNumber) {
                            duplicateType = 'Trùng input & Đã tồn tại';
                        } else {
                            duplicateType = 'Trùng input';
                        }
                    } else if (isDatabaseDuplicateNumber) {
                        duplicateType = 'Đã tồn tại';
                    }

                    // Row class for duplicate indication
                    const rowClass = isDuplicate ? 'table-danger' : '';

                    // Status column
                    let statusHtml = '';
                    if (isDuplicate) {
                        statusHtml =
                            `<span class="badge bg-danger"><i class="fas fa-times me-1"></i>${duplicateType}</span>`;
                    } else {
                        if (wordCount === 0) {
                            statusHtml =
                                '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle me-1"></i>Không có nội dung</span>';
                        } else if (wordCount < 100) {
                            statusHtml =
                                '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle me-1"></i>Nội dung ngắn</span>';
                        } else {
                            statusHtml =
                                '<span class="badge bg-success"><i class="fas fa-check me-1"></i>OK</span>';
                        }
                    }

                    // Content statistics
                    const wordDisplay = wordCount > 0 ?
                        `<strong>${wordCount.toLocaleString()}</strong> từ` :
                        '<span class="text-danger">0 từ</span>';

                    const charDisplay = charCount > 0 ?
                        `${charCount.toLocaleString()} ký tự` :
                        '<span class="text-danger">0 ký tự</span>';

                    let duplicateWarning = '';
                    if (isDuplicateNumber) {
                        let warningParts = [];
                        if (isInternalDuplicateNumber) {
                            warningParts.push('input');
                        }
                        if (isDatabaseDuplicateNumber) {
                            warningParts.push('Đã tồn tại');
                        }
                        duplicateWarning =
                            `<br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Số chương trùng (${warningParts.join(', ')})</small>`;
                    }

                    // Hiển thị tiêu đề với style khác nếu là default title
                    const isDefaultTitle = chapter.title === `Chương ${chapter.number}`;
                    const titleDisplay = isDefaultTitle ?
                        `<span class="text-muted">${chapter.title}</span> <small class="text-muted">(mặc định)</small>` :
                        chapter.title;

                    html += `
                        <tr class="${rowClass}">
                            <td>${index + 1}</td>
                            <td>
                                <strong>Chương ${chapter.number}</strong>
                                ${isDuplicateNumber ? duplicateWarning : ''}
                            </td>
                            <td>
                                ${titleDisplay}
                            </td>
                            <td>
                                <small class="text-muted">chuong-${chapter.number}-${createSlug(chapter.title)}</small>
                            </td>
                            <td>${wordDisplay}</td>
                            <td>${charDisplay}</td>
                            <td>${statusHtml}</td>
                            <td>
                                ${isDuplicate ?
                                    '<span class="text-muted">N/A</span>' :
                                    `<div class="form-check mb-2">
                                                                            <input class="form-check-input chapter-schedule-toggle"
                                                                                type="checkbox"
                                                                                id="enableSchedule_${chapter.number}"
                                                                                data-chapter="${chapter.number}"
                                                                                onchange="toggleChapterSchedule(${chapter.number})">
                                                                            <label class="form-check-label" for="enableSchedule_${chapter.number}">
                                                                                Hẹn giờ riêng ${isPublished ? '<small class="text-info">(sẽ lưu nháp)</small>' : ''}
                                                                            </label>
                                                                        </div>
                                                                        <div class="schedule-input d-none" id="scheduleField_${chapter.number}">
                                                                            <input type="datetime-local"
                                                                                class="form-control form-control-sm chapter-schedule-date"
                                                                                id="schedule_${chapter.number}"
                                                                                name="chapter_schedules[${chapter.number}]">
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            <small class="text-muted calculated-time" id="calculatedTime_${chapter.number}">
                                                                                <!-- Thời gian tính toán sẽ hiển thị ở đây -->
                                                                            </small>
                                                                        </div>`
                                }
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';

                // Add summary statistics
                const totalWords = chapters.reduce((sum, ch) => sum + countWords(ch.content), 0);
                const totalChars = chapters.reduce((sum, ch) => sum + ch.content.length, 0);
                const validChapters = chapters.filter((ch, index) =>
                    !duplicateNumbersSet.has(ch.number)
                ).length;

                html += '<div class="row mt-3">';
                html += '<div class="col-md-3">';
                html += '<div class="card text-center border-primary">';
                html += '<div class="card-body">';
                html += '<h5 class="card-title text-primary">' + chapters.length + '</h5>';
                html += '<p class="card-text small">Tổng chương phát hiện</p>';
                html += '</div></div></div>';

                html += '<div class="col-md-3">';
                html += '<div class="card text-center border-success">';
                html += '<div class="card-body">';
                html += '<h5 class="card-title text-success">' + validChapters + '</h5>';
                html += '<p class="card-text small">Chương hợp lệ</p>';
                html += '</div></div></div>';

                html += '<div class="col-md-3">';
                html += '<div class="card text-center border-info">';
                html += '<div class="card-body">';
                html += '<h5 class="card-title text-info">' + totalWords.toLocaleString() + '</h5>';
                html += '<p class="card-text small">Tổng số từ</p>';
                html += '</div></div></div>';

                html += '<div class="col-md-3">';
                html += '<div class="card text-center border-secondary">';
                html += '<div class="card-body">';
                html += '<h5 class="card-title text-secondary">' + totalChars.toLocaleString() + '</h5>';
                html += '<p class="card-text small">Tổng ký tự</p>';
                html += '</div></div></div>';
                html += '</div>';

                html += '<input type="hidden" id="chapters_count" name="chapters_count" value="' + chapters.length +
                    '">';

                container.html(html);
                $('#previewChapters').removeClass('d-none');

                const previewContent = $('#previewContent');
                const previewIcon = $('#previewIcon');
                if (!previewContent.is(':visible')) {
                    previewContent.slideDown(200);
                    previewIcon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }

                setTimeout(() => {
                    calculateScheduleTimes();
                }, 100);
            }
        });

        function calculateScheduleTimes() {
            const baseTime = $('#published_at').val();
            const hoursInterval = parseFloat($('#hours_interval').val()) || 0;

            if (!baseTime || hoursInterval <= 0) {
                $('.calculated-time').text('');
                return;
            }

            const chapters = [];
            // Lấy danh sách các chương từ preview table
            $('#chaptersPreviewContainer tbody tr').each(function() {
                const chapterNumber = $(this).find('td:eq(1) strong').text().replace('Chương ', '');
                const hasCustomSchedule = $(`#enableSchedule_${chapterNumber}`).is(':checked') &&
                    $(`#schedule_${chapterNumber}`).val();

                if (!hasCustomSchedule) {
                    chapters.push(parseInt(chapterNumber));
                }
            });

            // Sắp xếp theo số chương
            chapters.sort((a, b) => a - b);

            // Tính toán thời gian cho từng chương
            const baseDate = new Date(baseTime);
            chapters.forEach((chapterNumber, index) => {
                const scheduleTime = new Date(baseDate.getTime() + (index * hoursInterval * 60 * 60 * 1000));
                const formattedTime = scheduleTime.toLocaleString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                $(`#calculatedTime_${chapterNumber}`).html(
                    `<i class="fas fa-clock text-info"></i> Dự kiến: ${formattedTime}`
                );
            });
        }

        $('#published_at, #hours_interval').on('change input', function() {
            calculateScheduleTimes();
        });

        $(document).on('change', '.chapter-schedule-toggle', function() {
            calculateScheduleTimes();
        });

        // Function to toggle individual chapter schedule
        function toggleChapterSchedule(chapterNumber) {
            const checkbox = $(`#enableSchedule_${chapterNumber}`);
            const scheduleField = $(`#scheduleField_${chapterNumber}`);

            if (checkbox.is(':checked')) {
                scheduleField.removeClass('d-none');
                $(`#calculatedTime_${chapterNumber}`).text('');
            } else {
                scheduleField.addClass('d-none');
                $(`#schedule_${chapterNumber}`).val('');
                calculateScheduleTimes();
            }
        }

        // Xử lý các hiển thị cho form
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

        function toggleScheduleOptions() {
            var isDraft = $('#status_draft').is(':checked');
            if (isDraft) {
                $('#scheduleOptionsContainer').show();
            } else {
                $('#scheduleOptionsContainer').hide();
                $('#enableSchedule').prop('checked', false);
                $('#scheduleField').hide();
                $('#published_at').val('');
            }

            if (!$('#previewChapters').hasClass('d-none') && $('#chaptersPreviewContainer').children().length > 0) {
                $('#btnPreviewChapters').click();
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

        function togglePasswordVisibility() {
            var passwordField = document.getElementById('password');
            var icon = document.querySelector('#passwordField .toggle-password i');

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

        $('#batchChapterForm').on('submit', function(e) {
            // Cập nhật nội dung text thuần túy trước khi submit
            if (editor) {
                const htmlContent = editor.getData();
                const plainText = stripHtmlTags(htmlContent);
                const decodedText = decodeHtmlEntities(plainText);
                $('#batch_content').val(decodedText);
                
                // Kiểm tra nếu decodedText rỗng nhưng có HTML content
                if (!decodedText.trim() && htmlContent.trim()) {
                    // Fallback: sử dụng regex để loại bỏ HTML tags và decode entities
                    const fallbackText = htmlContent.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                    const fallbackDecoded = decodeHtmlEntities(fallbackText);
                    $('#batch_content').val(fallbackDecoded);
                }
            }
            
            const batchContent = $('#batch_content').val();
            if (!batchContent.trim()) {
                e.preventDefault();
                showToast('Vui lòng nhập nội dung các chương.', 'warning');
                $('#batch_content').focus();
                return false;
            }

            const duplicateRows = $('#chaptersPreviewContainer .table-danger');
            if (duplicateRows.length > 0) {
                e.preventDefault();
                showToast('Có chương trùng lặp! Vui lòng chỉnh sửa trước khi lưu.', 'error');
                return false;
            }

            if ($('#has_password_yes').is(':checked') && $('#password').val() === '') {
                e.preventDefault();
                showToast('Vui lòng nhập mật khẩu cho các chương.', 'warning');
                $('#password').focus();
                return false;
            }

            // Check if chapters were previewed
            // if ($('#chaptersPreviewContainer').children().length === 0) {
            //     e.preventDefault();
            //     showToast('Vui lòng xem trước các chương trước khi lưu.', 'warning');
            //     $('#btnPreviewChapters').focus();
            //     return false;
            // }

            return true;
        });

        // Toggle guide visibility
        function toggleGuide() {
            const guideContent = $('#guideContent');
            const icon = $('#guideIcon');

            if (guideContent.is(':visible')) {
                guideContent.slideUp(200);
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                guideContent.slideDown(200);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        }
    </script>
@endpush
