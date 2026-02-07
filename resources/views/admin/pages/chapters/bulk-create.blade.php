@extends('admin.layouts.app')

@section('content-auth')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-box">
                    <h4 class="page-title">Tạo nhiều chương - {{ $story->title }}</h4>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="bulkCreateForm" method="POST" novalidate
                            action="{{ route('admin.stories.chapters.bulk-store', $story) }}">
                            @csrf

                            <!-- Content Source -->
                            <div class="mb-4">
                                <label class="form-label">Nội dung từ file mẫu</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Nội dung sẽ được tự động phân tích và trích xuất theo định dạng:
                                    <strong>"Chương [số]: [tên chương]"</strong>
                                </div>
                                <textarea class="form-control" id="sampleContent" rows="30" placeholder="Paste nội dung từ file mẫu vào đây..."></textarea>
                            </div>

                            <!-- Global Settings -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5>Thiết lập chung cho tất cả chương:</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Giá mặc định</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="globalPrice" value="0"
                                            min="0" step="1" required>
                                        <span class="input-group-text">Cám</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Ngày công bố đầu tiên</label>
                                    <input type="datetime-local" class="form-control" id="globalPublishedAt">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Thời gian giữa các chương</label>
                                    <div class="row">
                                        <div class="col">
                                            <input type="number" class="form-control" id="globalIntervalValue"
                                                value="5" min="1">
                                        </div>
                                        <div class="col">
                                            <select class="form-control" id="globalIntervalUnit">
                                                <option value="hours">Giờ</option>
                                                <option value="days">Ngày</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Analysis Results and Preview -->
                            <div id="analysisResults" class="mb-4" style="display: none;">
                                <h5>Kết quả phân tích và xem trước các chương:</h5>
                                
                                <div id="previewList" class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Số chương</th>
                                                <th>Tên chương</th>
                                                <th>Giá</th>
                                                <th>Ngày công bố</th>
                                                <th>Public luôn</th>
                                                <th>Thiết lập riêng</th>
                                            </tr>
                                        </thead>
                                        <tbody id="previewTableBody">
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button type="button" class="btn bg-gradient-primary" id="detectChapters">
                                    <i class="fas fa-search"></i> Phân tích chương
                                </button>
                                <button type="submit" class="btn bg-gradient-primary" id="createChapters" style="display: none;">
                                    <i class="fas fa-plus"></i> Tạo tất cả chương
                                </button>
                                <a href="{{ route('admin.stories.chapters.index', $story) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Chapter Settings Modal -->
    <div class="modal fade" id="chapterSettingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thiết lập riêng cho Chương <span id="modalChapterNumber"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Giá riêng</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="individualPrice" min="0">
                            <span class="input-group-text">Cám</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <input type="checkbox" id="useIndividualPublishedAt">
                            Sử dụng ngày công bố riêng
                        </label>
                        <input type="datetime-local" class="form-control" id="individualPublishedAt" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên chương riêng</label>
                        <input type="text" class="form-control" id="individualTitle">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <input type="checkbox" id="individualPublishNow">
                            Public ngay lập tức
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn bg-gradient-primary" id="saveIndividualSettings">Lưu thiết lập</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles-main')
    <style>
        .chapter-settings-btn {
            font-size: 0.8rem;
        }

        #previewList {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
@endpush

@push('scripts-admin')
    <script>
        // Define missing functions that are referenced in dashboard.min.js
        function focused(e) {
            if (e.parentElement) {
                e.parentElement.classList.add('focused');
            }
        }
        
        function defocused(e) {
            if (e.parentElement) {
                e.parentElement.classList.remove('focused');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            let detectedChapters = [];
            let currentChapterIndex = null;

            // Set default published date to current time from server
            fetch('{{ route("admin.get-server-time") }}')
                .then(response => response.json())
                .then(data => {
                    // Server returns time in Vietnam timezone format
                    document.getElementById('globalPublishedAt').value = data.time;
                })
                .catch(error => {
                    // Fallback to client time if server request fails
                    const now = new Date();
                    document.getElementById('globalPublishedAt').value = now.toISOString().slice(0, 16);
                });

            // Chapter detection function
            function detectChapters() {
                const content = document.getElementById('sampleContent').value.trim();
                if (!content) {
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Vui lòng nhập nội dung mẫu',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                    return;
                }

                // More comprehensive chapter detection
                const chapters = [];
                const lines = content.split('\n');
                let currentChapter = null;
                let chapterContent = [];

                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();
                    
                    // Check if this line starts with "Chương"
                    const chapterMatch = line.match(/^Chương\s*(\d+)(?:\s*:\s*(.*))?$/);
                    
                    if (chapterMatch) {
                        // Save previous chapter if exists
                        if (currentChapter) {
                            chapters.push({
                                number: currentChapter.number,
                                title: currentChapter.title,
                                content: chapterContent.join('\n').trim()
                            });
                        }
                        
                        // Start new chapter
                        const chapterNumber = parseInt(chapterMatch[1]);
                        const title = chapterMatch[2] ? chapterMatch[2].trim() : `Chương ${chapterNumber}`;
                        
                        currentChapter = { number: chapterNumber, title: title };
                        chapterContent = [];
                    } else if (currentChapter) {
                        // Add content to current chapter
                        chapterContent.push(line);
                    }
                }
                
                // Don't forget the last chapter
                if (currentChapter) {
                    chapters.push({
                        number: currentChapter.number,
                        title: currentChapter.title,
                        content: chapterContent.join('\n').trim()
                    });
                }

                if (chapters.length === 0) {
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Không tìm thấy chương nào trong nội dung',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                    return;
                }

                // Check for existing chapters
                checkExistingChapters(chapters);
            }

            // Check existing chapters via AJAX
            function checkExistingChapters(chapters) {
                const existingNumbers = chapters.map(ch => ch.number);

                fetch(`{{ route('admin.stories.chapters.check-existing', $story) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            chapter_numbers: existingNumbers
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const existingNumbers = data.existing || [];

                        detectedChapters = chapters.map(chapter => ({
                            ...chapter,
                            existing: existingNumbers.includes(chapter.number),
                            settings: {
                                price: parseInt(document.getElementById('globalPrice').value) || 0,
                                published_at: document.getElementById('globalPublishedAt').value,
                                useCustom: false,
                                customPrice: null,
                                customPublishedAt: null,
                                customTitle: null,
                                publishNow: false
                            }
                        }));

                        displayDetectedChapters();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra khi kiểm tra chương',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                    });
            }

            // Display detected chapters and preview
            function displayDetectedChapters() {
                // Calculate published dates first
                calculatePublishedDates();
                
                const resultsDiv = document.getElementById('analysisResults');
                const tbody = document.getElementById('previewTableBody');

                tbody.innerHTML = '';

                detectedChapters.forEach((chapter, index) => {
                    // Show all chapters (both existing and new) in the table
                    const row = document.createElement('tr');
                    
                    // Add CSS class based on chapter status
                    if (chapter.existing) {
                        row.classList.add('table-warning');
                    } else {
                        row.classList.add('table-success');
                    }
                    
                    row.innerHTML = `
                    <td>
                        <strong>Chương ${chapter.number}</strong>
                        ${chapter.existing ? '<span class="badge bg-warning ms-2">Đã tồn tại</span>' : '<span class="badge bg-success ms-2">Mới</span>'}
                    </td>
                    <td>
                        <strong>${chapter.settings.customTitle || chapter.title}</strong>
                        <small class="d-block text-muted" style="max-height: 50px; overflow: hidden;">${chapter.content.substring(0, 100)}...</small>
                    </td>
                    <td>
                        ${chapter.settings.customPrice !== null ? 
                            `<span class="text-warning">${chapter.settings.customPrice.toLocaleString()} Cám</span>` : 
                            `<span class="text-info">${chapter.settings.price.toLocaleString()} Cám</span>`}
                    </td>
                    <td>
                        ${chapter.settings.publishNow ? 
                            '<span class="text-success">Ngay lập tức</span>' : 
                            (chapter.settings.customPublishedAt ? 
                                `<span class="text-warning">${formatDateTime(chapter.settings.customPublishedAt)}</span>` : 
                                `<span class="text-info">${formatDateTime(chapter.settings.published_at)}</span>`)}
                    </td>
                    <td>
                        ${chapter.existing ? 
                            '<span class="text-muted">-</span>' : 
                            `<div class="form-check">
                                <input class="form-check-input publish-now-checkbox" type="checkbox" 
                                    data-chapter-number="${chapter.number}" 
                                    ${chapter.settings.publishNow ? 'checked' : ''}>
                            </div>`}
                    </td>
                    <td>
                        ${chapter.existing ? 
                            '<span class="text-muted">-</span>' : 
                            `<button type="button" class="btn btn-sm btn-outline-primary" data-chapter-index="${chapter.number}" onclick="openChapterSettings(${chapter.number})">
                                <i class="fas fa-cog"></i>
                            </button>`}
                    </td>
                `;
                    tbody.appendChild(row);
                });

                resultsDiv.style.display = 'block';
                document.getElementById('createChapters').style.display = 'inline-block';
            }

            // Format datetime for display
            function formatDateTime(dateTimeStr) {
                // Handle both ISO format and YYYY-MM-DDTHH:mm format
                let date;
                if (dateTimeStr.includes('T') && !dateTimeStr.includes('Z') && !dateTimeStr.includes('+')) {
                    // Format: YYYY-MM-DDTHH:mm (local time)
                    date = new Date(dateTimeStr + '+07:00'); // Assume Vietnam timezone
                } else {
                    // ISO format or other formats
                    date = new Date(dateTimeStr);
                }
                
                return date.toLocaleString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: 'Asia/Ho_Chi_Minh'
                });
            }

            // Calculate published dates
            function calculatePublishedDates() {
                if (detectedChapters.length === 0) return;

                const baseDateStr = document.getElementById('globalPublishedAt').value;
                const intervalValue = parseInt(document.getElementById('globalIntervalValue').value);
                const intervalUnit = document.getElementById('globalIntervalUnit').value;

                detectedChapters.forEach((chapter, index) => {
                    // Only calculate for chapters that are not set to publish now and don't have custom settings
                    if (!chapter.settings.publishNow && (!chapter.settings.useCustom || !chapter.settings.customPublishedAt)) {
                        // Create date in Vietnam timezone
                        const baseDate = new Date(baseDateStr + '+07:00'); // Vietnam timezone offset
                        const calculatedDate = new Date(baseDate);
                        calculatedDate.setTime(baseDate.getTime() + (index * intervalValue * (
                            intervalUnit === 'hours' ? 3600000 : 86400000)));

                        // Format as YYYY-MM-DDTHH:mm format (without timezone info)
                        const year = calculatedDate.getFullYear();
                        const month = String(calculatedDate.getMonth() + 1).padStart(2, '0');
                        const day = String(calculatedDate.getDate()).padStart(2, '0');
                        const hours = String(calculatedDate.getHours()).padStart(2, '0');
                        const minutes = String(calculatedDate.getMinutes()).padStart(2, '0');
                        
                        chapter.settings.published_at = `${year}-${month}-${day}T${hours}:${minutes}`;
                    }
                });
            }


            // Open chapter settings modal
            window.openChapterSettings = function(chapterNumber) {
                const chapter = detectedChapters.find(ch => ch.number === chapterNumber);
                if (!chapter) return;

                currentChapterIndex = detectedChapters.indexOf(chapter);

                document.getElementById('modalChapterNumber').textContent = chapterNumber;
                document.getElementById('individualPrice').value = chapter.settings.customPrice || chapter
                    .settings.price;
                document.getElementById('individualPublishedAt').value = chapter.settings.customPublishedAt ||
                    chapter.settings.published_at;
                document.getElementById('individualTitle').value = chapter.settings.customTitle || chapter
                .title;
                document.getElementById('useIndividualPublishedAt').checked = chapter.settings.useCustom;
                document.getElementById('individualPublishNow').checked = chapter.settings.publishNow;

                toggleIndividualPublishedAt();
                toggleIndividualPublishNow();

                $('#chapterSettingsModal').modal('show');
            };

            // Toggle individual published date checkbox
            document.getElementById('useIndividualPublishedAt').addEventListener('change',
                toggleIndividualPublishedAt);
                
            // Toggle publish now checkbox
            document.getElementById('individualPublishNow').addEventListener('change',
                toggleIndividualPublishNow);

            function toggleIndividualPublishedAt() {
                const checkbox = document.getElementById('useIndividualPublishedAt');
                const input = document.getElementById('individualPublishedAt');
                const publishNowCheckbox = document.getElementById('individualPublishNow');
                
                if (publishNowCheckbox.checked) {
                    input.disabled = true;
                    checkbox.disabled = true;
                } else {
                    input.disabled = !checkbox.checked;
                    checkbox.disabled = false;
                }
            }
            
            function toggleIndividualPublishNow() {
                const publishNowCheckbox = document.getElementById('individualPublishNow');
                const publishedAtCheckbox = document.getElementById('useIndividualPublishedAt');
                const publishedAtInput = document.getElementById('individualPublishedAt');
                
                if (publishNowCheckbox.checked) {
                    publishedAtCheckbox.disabled = true;
                    publishedAtInput.disabled = true;
                } else {
                    publishedAtCheckbox.disabled = false;
                    publishedAtInput.disabled = !publishedAtCheckbox.checked;
                }
            }

            // Save individual settings
            document.getElementById('saveIndividualSettings').addEventListener('click', function() {
                if (currentChapterIndex === null) return;

                const chapter = detectedChapters[currentChapterIndex];
                const customPrice = parseInt(document.getElementById('individualPrice').value);
                const customTitle = document.getElementById('individualTitle').value.trim();
                const useCustomPublishedAt = document.getElementById('useIndividualPublishedAt').checked;
                const publishNow = document.getElementById('individualPublishNow').checked;

                chapter.settings.customPrice = customPrice;
                chapter.settings.customTitle = customTitle || null;
                chapter.settings.useCustom = useCustomPublishedAt;
                chapter.settings.publishNow = publishNow;

                if (useCustomPublishedAt && !publishNow) {
                    chapter.settings.customPublishedAt = document.getElementById('individualPublishedAt')
                        .value;
                }

                $('#chapterSettingsModal').modal('hide');
                displayDetectedChapters();
            });

            // Event listeners
            document.getElementById('detectChapters').addEventListener('click', detectChapters);
            
            // Handle global settings changes
            document.getElementById('globalPrice').addEventListener('input', function() {
                if (detectedChapters.length > 0) {
                    const newPrice = parseInt(this.value) || 0;
                    detectedChapters.forEach(chapter => {
                        // Only update price for chapters that don't have custom price settings
                        if (!chapter.settings.useCustom && chapter.settings.customPrice === null) {
                            chapter.settings.price = newPrice;
                        }
                    });
                    displayDetectedChapters();
                }
            });
            
            document.getElementById('globalPublishedAt').addEventListener('change', function() {
                if (detectedChapters.length > 0) {
                    displayDetectedChapters();
                }
            });
            
            document.getElementById('globalIntervalValue').addEventListener('change', function() {
                if (detectedChapters.length > 0) {
                    displayDetectedChapters();
                }
            });
            
            document.getElementById('globalIntervalUnit').addEventListener('change', function() {
                if (detectedChapters.length > 0) {
                    displayDetectedChapters();
                }
            });
            
            // Handle publish now checkbox changes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('publish-now-checkbox')) {
                    const chapterNumber = parseInt(e.target.dataset.chapterNumber);
                    const chapter = detectedChapters.find(ch => ch.number === chapterNumber);
                    if (chapter) {
                        chapter.settings.publishNow = e.target.checked;
                        // Update the display
                        displayDetectedChapters();
                    }
                }
            });

            // Handle chapter settings clicks
            document.addEventListener('click', function(e) {
                if (e.target.closest('.chapter-settings-btn')) {
                    const index = parseInt(e.target.closest('.chapter-settings-btn').dataset.index);
                    openChapterSettings(detectedChapters[index].number);
                }
            });

            // Form submission
            document.getElementById('bulkCreateForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const newChapters = detectedChapters.filter(ch => !ch.existing);
                if (newChapters.length === 0) {
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Không có chương nào có thể tạo',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                    return;
                }

                // Validate data before sending
                let hasError = false;
                let errorMessage = '';
                
                newChapters.forEach((chapter, index) => {
                    if (!chapter.number || chapter.number < 1) {
                        hasError = true;
                        errorMessage += `Chương thứ ${index + 1}: Số chương không hợp lệ\n`;
                    }
                    if (!chapter.title || chapter.title.trim() === '') {
                        hasError = true;
                        errorMessage += `Chương thứ ${index + 1}: Tên chương không được để trống\n`;
                    }
                    if (!chapter.content || chapter.content.trim() === '') {
                        hasError = true;
                        errorMessage += `Chương thứ ${index + 1}: Nội dung không được để trống\n`;
                    }
                    if (chapter.settings.price < 0) {
                        hasError = true;
                        errorMessage += `Chương thứ ${index + 1}: Giá không được âm\n`;
                    }
                });
                
                if (hasError) {
                    Swal.fire({
                        title: 'Lỗi validation',
                        html: 'Có lỗi trong dữ liệu:<br>' + errorMessage.replace(/\n/g, '<br>'),
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                    return;
                }

                 // Prepare data
                 const formData = new FormData();
                 formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                     'content'));

                 const chaptersData = newChapters.map(chapter => ({
                     number: chapter.number,
                     title: chapter.settings.customTitle || chapter.title,
                     content: chapter.content,
                     price: chapter.settings.customPrice || chapter.settings.price,
                     published_at: chapter.settings.publishNow ? null : (chapter.settings.customPublishedAt || chapter.settings.published_at),
                     publish_now: chapter.settings.publishNow
                 }));

                 console.log('Chapters data to send:', chaptersData);
                 formData.append('chapters', JSON.stringify(chaptersData));

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                     .then(data => {
                         console.log('Response data:', data);
                         if (data.success) {
                             Swal.fire({
                                 title: 'Thành công',
                                 text: `Đã tạo thành công ${data.created_count} chương`,
                                 icon: 'success',
                                 confirmButtonText: 'Đóng'
                             }).then(() => {
                                 window.location.href = '{{ route('admin.stories.chapters.index', $story) }}';
                             });
                         } else {
                             Swal.fire({
                                 title: 'Lỗi',
                                 text: data.message || 'Có lỗi xảy ra',
                                 icon: 'error',
                                 confirmButtonText: 'Đóng'
                             });
                         }
                     })
                     .catch(error => {
                         console.error('Error:', error);
                         Swal.fire({
                             title: 'Lỗi',
                             text: 'Có lỗi xảy ra khi tạo chương: ' + error.message,
                             icon: 'error',
                             confirmButtonText: 'Đóng'
                         });
                     });
            });
        });
    </script>
@endpush
