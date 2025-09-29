{{-- filepath: resources/views/pages/information/deposit/check_card.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Thẻ Cào - Test Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .check-card-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .check-card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .btn-check {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-check:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .response-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }
        
        .json-response {
            background: #2d3748;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-1 { background: #d4edda; color: #155724; }
        .status-2 { background: #fff3cd; color: #856404; }
        .status-3 { background: #f8d7da; color: #721c24; }
        .status-99 { background: #d1ecf1; color: #0c5460; }
        .status-unknown { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="container">
        <div class="check-card-container">
            <!-- Header -->
            <div class="check-card-header">
                <h2><i class="fas fa-search me-2"></i>Check Thẻ Cào Tool</h2>
                <p class="mb-0">Kiểm tra thông tin thẻ cào qua API</p>
            </div>
            
            <!-- Form -->
            <div class="p-4">
                <form id="checkCardForm">
                    @csrf
                    
                    <!-- API Settings -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-server me-2"></i>Domain
                            </label>
                            <input type="text" class="form-control" name="domain" 
                                   value="thesieure88.com" required>
                            <small class="text-muted">Không cần http://</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-id-card me-2"></i>Partner ID
                            </label>
                            <input type="text" class="form-control" name="partner_id" 
                                   value="3681148751" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-key me-2"></i>Partner Key (optional)
                        </label>
                        <input type="text" class="form-control" name="partner_key" 
                               placeholder="Để trống sẽ dùng key mặc định">
                        <small class="text-muted">Dùng để tính signature</small>
                    </div>
                    
                    <!-- Card Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-sim-card me-2"></i>Loại thẻ
                            </label>
                            <select class="form-select" name="telco" required>
                                <option value="VIETTEL" selected>Viettel</option>
                                <option value="MOBIFONE">Mobifone</option>
                                <option value="VINAPHONE">Vinaphone</option>
                                <option value="GARENA">Garena</option>
                                <option value="ZING">Zing</option>
                                <option value="VNMOBI">Vietnamobile</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill me-2"></i>Mệnh giá
                            </label>
                            <select class="form-select" name="amount" required>
                                <option value="10000">10.000đ</option>
                                <option value="20000">20.000đ</option>
                                <option value="50000" selected>50.000đ</option>
                                <option value="100000">100.000đ</option>
                                <option value="200000">200.000đ</option>
                                <option value="500000">500.000đ</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-barcode me-2"></i>Serial
                            </label>
                            <input type="text" class="form-control" name="serial" 
                                   placeholder="Nhập serial thẻ" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i>Mã thẻ
                            </label>
                            <input type="text" class="form-control" name="code" 
                                   placeholder="Nhập mã thẻ" required>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-check" id="submitBtn">
                            <i class="fas fa-search me-2"></i>Check Thẻ
                        </button>
                    </div>
                </form>
                
                <!-- Response Section -->
                <div class="response-section" id="responseSection">
                    <h5><i class="fas fa-chart-line me-2"></i>Kết quả</h5>
                    
                    <!-- Status Summary -->
                    <div class="row mb-3" id="statusSummary">
                        <div class="col-md-6">
                            <strong>Trạng thái:</strong>
                            <span class="status-badge ms-2" id="statusBadge">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>HTTP Code:</strong>
                            <span class="badge bg-info ms-2" id="httpCode">-</span>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="messageSummary">
                        <strong>Message:</strong>
                        <span id="responseMessage">-</span>
                    </div>
                    
                    <!-- Raw Response -->
                    <div class="mb-3">
                        <strong>Raw Response:</strong>
                        <div class="json-response" id="rawResponse">
                            <pre id="rawResponseContent">Chưa có dữ liệu</pre>
                        </div>
                    </div>
                    
                    <!-- Request Data -->
                    <div class="mb-3">
                        <strong>Request Data:</strong>
                        <div class="json-response" id="requestData">
                            <pre id="requestDataContent">Chưa có dữ liệu</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('checkCardForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const responseSection = document.getElementById('responseSection');
            
            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang check...';
            
            // Hide previous response
            responseSection.style.display = 'none';
            
            // Collect form data
            const formData = new FormData(this);
            
            // Send request
            fetch('{{ route("check.card") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResponse(data);
                } else {
                    displayError(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi gửi request'
                });
            })
            .finally(() => {
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-search me-2"></i>Check Thẻ';
            });
        });
        
        function displayResponse(data) {
            const responseSection = document.getElementById('responseSection');
            const statusBadge = document.getElementById('statusBadge');
            const httpCode = document.getElementById('httpCode');
            const responseMessage = document.getElementById('responseMessage');
            const rawResponseContent = document.getElementById('rawResponseContent');
            const requestDataContent = document.getElementById('requestDataContent');
            
            // Update status
            const status = data.response_data.status || 'unknown';
            statusBadge.textContent = status;
            statusBadge.className = `status-badge ms-2 status-${status}`;
            
            // Update HTTP code
            httpCode.textContent = data.response_data.http_code;
            
            // Update message
            responseMessage.textContent = data.response_data.message || 'Không có message';
            
            // Update raw response
            rawResponseContent.textContent = JSON.stringify(data.response_data, null, 2);
            
            // Update request data
            requestDataContent.textContent = JSON.stringify(data.request_data, null, 2);
            
            // Show response section
            responseSection.style.display = 'block';
            
            // Show success notification
            Swal.fire({
                icon: 'success',
                title: 'Check thành công!',
                text: `Status: ${status} - ${data.response_data.message}`,
                timer: 3000,
                timerProgressBar: true
            });
        }
        
        function displayError(data) {
            const responseSection = document.getElementById('responseSection');
            const statusBadge = document.getElementById('statusBadge');
            const httpCode = document.getElementById('httpCode');
            const responseMessage = document.getElementById('responseMessage');
            const rawResponseContent = document.getElementById('rawResponseContent');
            const requestDataContent = document.getElementById('requestDataContent');
            
            // Update error info
            statusBadge.textContent = 'ERROR';
            statusBadge.className = 'status-badge ms-2 bg-danger text-white';
            
            httpCode.textContent = data.status_code || 'N/A';
            responseMessage.textContent = data.message || 'Unknown error';
            
            // Update responses
            rawResponseContent.textContent = JSON.stringify(data, null, 2);
            requestDataContent.textContent = 'Error occurred';
            
            // Show response section
            responseSection.style.display = 'block';
            
            // Show error notification
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: data.message || 'Có lỗi xảy ra'
            });
        }
        
        // Auto-fill example data
        document.addEventListener('DOMContentLoaded', function() {
            // You can set example values here for testing
            // document.querySelector('input[name="serial"]').value = '10004783347874';
            // document.querySelector('input[name="code"]').value = '312821445892982';
        });
    </script>
</body>
</html>