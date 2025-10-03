@extends('admin.layouts.app')

@push('styles-admin')
    <style>
        .selected-items {
            background: white;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-height: 50px;
            align-items: center;
        }

        .selected-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
            transition: all 0.2s ease;
            border: 2px solid transparent;
            white-space: nowrap;
            width: auto;
        }

        .selected-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .selected-item .remove-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            padding: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            margin-left: 5px;
        }

        .selected-item .remove-btn:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }

         /* Search results styling */
         .search-results {
             background: white;
             border: 1px solid #ddd;
             border-top: none;
             border-radius: 0 0 4px 4px;
             max-height: 120px;
             overflow-y: auto;
             box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
             display: none;
             margin-top: 2px;
         }

        .search-result-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item.selected {
            background-color: #e3f2fd;
        }

        .checkbox-indicator {
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
        }

        .search-result-item.selected .checkbox-indicator {
            background-color: #007bff;
            border-color: #007bff;
        }

         .form-group {
             margin-bottom: 1.5rem;
         }

         .form-group:first-of-type {
             margin-bottom: 2rem;
         }
    </style>
@endpush

@section('content-auth')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Chuyển cám cho người dùng</h6>
                            <a href="{{ route('admin.coin-transfers.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Current Admin cám Info -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card bg-gradient-info">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('images/defaults/avatar_default.jpg') }}"
                                                    class="rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-0">{{ Auth::user()->name }}</h6>
                                                <small class="text-white opacity-8">{{ Auth::user()->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-gradient-success">
                                    <div class="card-body p-3">
                                        <div class="text-white">
                                            <h6 class="mb-0">Cám hiện tại</h6>
                                            <h4 class="mb-0">{{ number_format(Auth::user()->coins) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-gradient-warning">
                                    <div class="card-body p-3">
                                        <div class="text-white">
                                            <h6 class="mb-0">Giới hạn/lần</h6>
                                            <h4 class="mb-0">50,000</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Form -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header pb-0">
                                        <h6>Thông tin chuyển khoản</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <form action="{{ route('admin.coin-transfers.store') }}" method="POST"
                                                    id="transferForm">
                                                    @csrf
                                                    <div class="form-group mb-3">
                                                        <label class="form-control-label">Chọn người nhận <span
                                                                class="text-danger">*</span></label>

                                                        <!-- Selected Users Display -->
                                                        <div id="selected_users" class="selected-items mb-2"></div>

                                                        <!-- Search Input -->
                                                        <input type="text"
                                                            class="form-control @error('user_ids') is-invalid @enderror"
                                                            id="user_search_input"
                                                            placeholder="Tìm kiếm theo tên hoặc email..."
                                                            autocomplete="off">
                                                        <div id="user_search_results" class="search-results"></div>
                                                        @error('user_ids')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        <!-- Hidden Inputs for Form Submission -->
                                                        <div id="user_hidden_inputs"></div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="amount" class="form-control-label">Số Cám <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number"
                                                            class="form-control @error('amount') is-invalid @enderror"
                                                            id="amount" name="amount" min="1" max="50000"
                                                            value="{{ old('amount', 1) }}" placeholder="Nhập số Cám..."
                                                            required>
                                                        @error('amount')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="note" class="form-control-label">Ghi chú</label>
                                                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3"
                                                            placeholder="Lý do chuyển Cám...">{{ old('note') }}</textarea>
                                                        @error('note')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="d-flex gap-2">
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm flex-grow-1"
                                                            id="previewBtn">
                                                            <i class="fas fa-eye me-1"></i> Xem trước
                                                        </button>
                                                        <button type="submit"
                                                            class="btn bg-gradient-primary btn-sm flex-grow-1"
                                                            id="submitBtn">
                                                            <i class="fas fa-exchange-alt me-1"></i> Chuyển Cám
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="col-md-6">
                                                <!-- Preview Section -->
                                                <div id="previewSection" style="display: none;">
                                                    <div class="card">
                                                        <div class="card-header pb-0">
                                                            <h6><i class="fas fa-info-circle me-2"></i>Xem trước chuyển
                                                                khoản</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group mb-3">
                                                                <label class="text-sm mb-1"><strong>Người
                                                                        nhận:</strong></label>
                                                                <div id="recipientInfo" class="bg-light p-2 rounded">-
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-3">
                                                                <label class="text-sm mb-1"><strong>Số
                                                                        Cám:</strong></label>
                                                                <div id="amountInfo" class="bg-light p-2 rounded">-</div>
                                                            </div>

                                                            <div class="form-group mb-3">
                                                                <label class="text-sm mb-1"><strong>Cám còn lại sau
                                                                        chuyển:</strong></label>
                                                                <div id="remainingInfo" class="bg-light p-2 rounded">-
                                                                </div>
                                                            </div>

                                                            <div class="alert alert-warning mb-0">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                <strong>Chú ý:</strong> Giao dịch này sẽ trừ Cám từ tài
                                                                khoản của bạn và cộng vào người nhận ngay lập tức!
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- EmptyState -->
                                                <div id="emptyState" class="text-center py-5">
                                                    <i class="fas fa-arrow-left text-muted" style="font-size: 3rem;"></i>
                                                    <h5 class="text-muted mt-3">Điền thông tin bên trái để xem trước</h5>
                                                    <p class="text-muted">Chọn người nhận và số Cám để xem thông tin chi
                                                        tiết</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('transferForm');
            const previewSection = document.getElementById('previewSection');
            const previewBtn = document.getElementById('previewBtn');
            const submitBtn = document.getElementById('submitBtn');

            // Multi-select functionality
            let selectedUsers = [];
            let users = {!! json_encode(\App\Models\User::where('role', 'user')->orderBy('email')->get()) !!};

            const userSearchInput = document.getElementById('user_search_input');
            const userSearchResults = document.getElementById('user_search_results');

            let searchTimeout;
            userSearchInput.addEventListener('input', function() {
                const query = this.value.trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    userSearchResults.style.display = 'none';
                    updatePreview();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    const filtered = users.filter(user => {
                        const display = user.email + (user.name ? ' (' + user.name + ')' :
                            '');
                        return display.toLowerCase().includes(query.toLowerCase());
                    });

                    displayUserResults(userSearchResults, filtered, 'user');
                }, 300);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userSearchInput.contains(e.target) && !userSearchResults.contains(e.target)) {
                    userSearchResults.style.display = 'none';
                }
            });

            // Hide dropdown when pressing Tab to move to next input
            userSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' || e.key === 'Escape') {
                    userSearchResults.style.display = 'none';
                }
            });

            // Load initial users on page load
            setTimeout(() => {
                displayUserResults(userSearchResults, users.slice(0, 10), 'user');
            }, 500);

            function displayUserResults(container, items, type) {
                container.innerHTML = '';

                if (items.length === 0) {
                    container.innerHTML = '<div class="search-result-item">Không tìm thấy người dùng</div>';
                } else {
                    items.forEach(function(item) {
                        const isSelected = selectedUsers.some(selected => selected.id === item.id);

                        const div = document.createElement('div');
                        div.className = 'search-result-item';
                        if (isSelected) div.classList.add('selected');

                        const display = item.email + (item.name ? ' (' + item.name + ')' : '');
                        div.innerHTML = '<span class="checkbox-indicator">' + (isSelected ? '✓' : '') +
                            '</span><span>' + display + '</span>';

                        div.addEventListener('click', function() {
                            toggleUserSelection(item, display);
                        });

                        container.appendChild(div);
                    });
                }

                container.style.display = 'block';
            }

             function toggleUserSelection(item, display) {
                const isSelected = selectedUsers.some(s => s.id === item.id);

                if (isSelected) {
                    selectedUsers = selectedUsers.filter(s => s.id !== item.id);
                } else {
                    selectedUsers.push({
                        id: item.id,
                        display: display
                    });
                }

                updateSelectedDisplay();
                updateHiddenInputs();
                updatePreview();
                
                // Keep dropdown open for multi-select but allow typing
                userSearchInput.focus();
            }

            function updateSelectedDisplay() {
                const container = document.getElementById('selected_users');

                if (selectedUsers.length === 0) {
                    container.style.display = 'none';
                    return;
                }

                container.innerHTML = '';
                selectedUsers.forEach(function(item) {
                    const tag = document.createElement('div');
                    tag.className = 'selected-item';
                    tag.innerHTML = '<span>' + item.display +
                        '</span><button type="button" class="remove-btn" onclick="removeUserSelection(' +
                        item.id + ')">×</button>';
                    container.appendChild(tag);
                });

                container.style.display = 'flex';
            }

            function updateHiddenInputs() {
                const container = document.getElementById('user_hidden_inputs');

                container.innerHTML = '';
                selectedUsers.forEach(function(item) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = item.id;
                    container.appendChild(input);
                });
            }

            window.removeUserSelection = function(id) {
                selectedUsers = selectedUsers.filter(s => s.id !== id);
                updateSelectedDisplay();
                updateHiddenInputs();
                updatePreview();
            };

            function updatePreview() {
                const emptyState = document.getElementById('emptyState');

                if (selectedUsers.length > 0) {
                    const userList = selectedUsers.map(user => user.display).join('<br>');
                    document.getElementById('recipientInfo').innerHTML = userList +
                        '<br><small class="text-muted">Tổng ' + selectedUsers.length + ' người</small>';
                } else {
                    document.getElementById('recipientInfo').textContent = '-';
                }

                const amount = document.getElementById('amount').value;
                if (amount && selectedUsers.length > 0) {
                    const currentCoins = {{ Auth::user()->coins }};
                    const transferAmount = parseInt(amount);
                    const totalNeeded = transferAmount * selectedUsers.length;
                    const remainingCoins = currentCoins - totalNeeded;

                    document.getElementById('amountInfo').textContent = transferAmount.toLocaleString() +
                        ' Cám/người';
                    document.getElementById('remainingInfo').innerHTML =
                        `<span class="${remainingCoins >= 0 ? 'text-success' : 'text-danger'}">${remainingCoins.toLocaleString()} Cám</span>`;

                    // Show preview section và hide empty state
                    previewSection.style.display = 'block';
                    emptyState.style.display = 'none';

                    // Enable/disable submit button
                    if (transferAmount > 0 && remainingCoins >= 0) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('btn-secondary');
                        submitBtn.classList.add('btn-primary');
                    } else {
                        submitBtn.disabled = true;
                        submitBtn.classList.remove('btn-primary');
                        submitBtn.classList.add('btn-secondary');
                    }
                } else {
                    previewSection.style.display = 'none';
                    emptyState.style.display = 'block';
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-secondary');
                }
            }

            // Update amount info on amount input change
            document.getElementById('amount').addEventListener('input', updatePreview);

            // Preview button click
            previewBtn.addEventListener('click', function() {
                updatePreview();
                if (previewSection.style.display === 'none') {
                    previewSection.style.display = 'block';
                }
            });

            // Form submission with confirmation
            form.addEventListener('submit', function(e) {
                if (selectedUsers.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất 1 người nhận!');
                    return;
                }

                const amount = parseInt(document.getElementById('amount').value);
                const totalNeeded = amount * selectedUsers.length;
                const currentCoins = {{ Auth::user()->coins }};

                if (totalNeeded > currentCoins) {
                    e.preventDefault();
                    alert('Số Cám của bạn không đủ để chuyển!');
                    return;
                }

                const confirmMessage = selectedUsers.length === 1 ?
                    `Xác nhận chuyển ${amount.toLocaleString()} Cám cho ${selectedUsers[0].display}?` :
                    `Xác nhận chuyển ${amount.toLocaleString()} Cám cho ${selectedUsers.length} người (Tổng: ${totalNeeded.toLocaleString()} Cám)?`;

                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
@endpush
