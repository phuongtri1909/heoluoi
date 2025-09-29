@extends('layouts.information')

@section('info_title', 'Thông tin cá nhân')
@section('info_description', 'Thông tin cá nhân của bạn trên ' . request()->getHost())
@section('info_keyword', 'Thông tin cá nhân, thông tin tài khoản, ' . request()->getHost())
@section('info_section_title', 'Thông tin người dùng')
@section('info_section_desc', 'Quản lý thông tin cá nhân của bạn')

@section('info_content')
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="text-center">
                <div class="profile-avatar-edit" id="avatar">
                    @if (!empty($user->avatar))
                        <img id="avatarImage" class="profile-avatar" src="{{ Storage::url($user->avatar) }}" alt="Avatar">
                    @else
                        <div class="profile-avatar d-flex align-items-center justify-content-center bg-light">
                            <i class="fa-solid fa-user fa-2x" id="defaultIcon"></i>
                        </div>
                    @endif
                    <div class="avatar-edit-overlay">
                        <i class="fas fa-camera me-1"></i> Cập nhật
                    </div>
                </div>
                <input type="file" id="avatarInput" style="display: none;" accept="image/*">
                
                <div class="mt-3">
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <div class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i> Tham gia từ: {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-8 mt-3 mt-md-0">
            <div class="profile-info-card">
                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-fingerprint"></i> ID
                    </div>
                    <div class="profile-info-value">
                        {{ $user->id }}
                    </div>
                </div>
                
                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-user"></i> <span class="d-none d-sm-inline">Họ và tên</span>
                    </div>
                    <div class="profile-info-value d-flex align-items-center">
                        <span class="me-2">{{ $user->name ?: 'Chưa cập nhật' }}</span>
                        <button class="btn btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-type="name">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                
                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-envelope"></i> <span class="d-none d-sm-inline">Email</span>
                    </div>
                    <div class="profile-info-value">
                        {{ $user->email }}
                    </div>
                </div>
                
                <div class="profile-info-item">
                    <div class="profile-info-label">
                        <i class="fas fa-lock"></i> <span class="d-none d-sm-inline">Mật khẩu</span>
                    </div>
                    <div class="profile-info-value d-flex align-items-center">
                        <span class="me-2">••••••••</span>
                        <button class="btn btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#otpPWModal">
                            <i class="fas fa-key"></i>
                        </button>
                    </div>
                </div>
                
                @if($user->role === 'author' || $user->role === 'admin')
                    <div class="profile-info-item">
                        <div class="profile-info-label">
                            <i class="fab fa-facebook"></i> <span class="d-none d-sm-inline">Facebook</span>
                        </div>
                        <div class="profile-info-value d-flex align-items-center">
                            <a href="{{ $user->facebook_link }}" target="_blank" class="me-2 d-inline-block">{{ $user->facebook_link ?: 'Chưa cập nhật' }}</a>
                            <button class="btn btn-sm profile-edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-type="facebook_link">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('user.update.name.or.phone') }}" method="post">
                        @csrf
                        <div class="mb-3" id="formContent">
                            <!-- Nội dung sẽ được cập nhật dựa trên loại dữ liệu được chọn -->
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-outline-success click-scroll"
                                id="saveChanges">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="otpPWModal" tabindex="-1" aria-labelledby="otpPWModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpPWModalLabel">Xác thực OTP để đổi mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="otpPWForm">
                        @csrf
                        <div class="mb-3 d-flex flex-column align-items-center" id="formOTPPWContent">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="text-end box-button-update">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-outline-success" id="btn-send-otpPW">Tiếp tục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            // Click vào avatar để mở file input
            $('#avatar').on('click', function() {
                $('#avatarInput').click();
            });

            // Xử lý khi người dùng chọn ảnh
            $('#avatarInput').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        // Hiển thị ảnh đã chọn
                        if (!$('#avatarImage').length) {
                            // Nếu chưa có ảnh (chỉ có icon), tạo thẻ <img> mới
                            $('#avatar').html('<img id="avatarImage" class="profile-avatar" src="' + e.target.result +
                                '" alt="Avatar"><div class="avatar-edit-overlay"><i class="fas fa-camera me-1"></i> Cập nhật</div>');
                            $('#defaultIcon').hide();
                        } else {
                            // Nếu đã có ảnh, chỉ cần thay đổi src của ảnh
                            $('#avatarImage').attr('src', e.target.result).show();
                        }
                    };
                    reader.readAsDataURL(file);
                    var formData = new FormData();
                    formData.append('avatar', file);

                    $.ajax({
                        url: "{{ route('user.update.avatar') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast(response.message, 'success');
                            } else {
                                showToast(response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            const response = xhr.responseJSON;
                            console.log('Error:', response);
                            showToast('Có lỗi xảy ra khi cập nhật ảnh đại diện', 'error');
                        }
                    });
                }
            });
        });

        //update user info (name, phone)
        $(document).ready(function() {
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var type = button.data('type');
                var modal = $(this);

                var formContent = $('#formContent');
                formContent.empty();

                if (type == 'name') {
                    modal.find('.modal-title').text('Chỉnh sửa Họ và Tên');
                    formContent.append(`
                        <label for="editValue" class="form-label">Họ và Tên</label>
                        <input type="text" class="form-control" id="editValue" name="name" value="{{ $user->name }}" required>
                    `);
                } else if (type == 'phone') {
                    modal.find('.modal-title').text('Chỉnh sửa Số điện thoại');
                    formContent.append(`
                        <label for="editValue" class="form-label">Số điện thoại</label>
                        <input type="number" class="form-control" id="editValue" name="phone" value="{{ $user->phone ?? '' }}" required>
                    `);
                } else if (type == 'facebook_link') {
                    @if($user->role === 'author' || $user->role === 'admin')
                        modal.find('.modal-title').text('Chỉnh sửa Facebook');
                        formContent.append(`
                            <label for="editValue" class="form-label">Link Facebook (Fanpage/Nhóm)</label>
                            <input type="url" class="form-control" id="editValue" name="facebook_link" value="{{ $user->facebook_link ?? '' }}" placeholder="https://facebook.com/your-page">
                            <div class="form-text">Nhập link Facebook fanpage hoặc nhóm của bạn</div>
                        `);
                    @else
                        showToast('Chỉ tác giả mới có thể cập nhật Facebook link', 'error');
                        return;
                    @endif
                } else {
                    showToast('Thao tác sai, hãy thử lại', 'error');
                }
            });
        });

        //update user password
        $(document).ready(function() {
            $('#otpPWModal').on('show.bs.modal', function(event) {
                var modal = $(this);
                $('#btn-send-otpPW').text('Tiếp tục');

                var formOTPContent = $('#formOTPPWContent');
                formOTPContent.empty();
                formOTPContent.append(`
                    <p class="text-center mb-3">
                        Chúng tôi sẽ gửi mã xác nhận OTP đến email của bạn. 
                        Vui lòng nhập mã nhận được để tiếp tục.
                    </p>
                    <div class="spinner-border text-success mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="otp-input-container" id="input-otp-pw">
                        <input type="text" maxlength="1" class="otp-digit" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-digit" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-digit" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-digit" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-digit" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-digit" oninput="handleInput(this)" />
                    </div>
                `);

                // Rest of your existing code for OTP password update
                $.ajax({
                    url: "{{ route('user.update.password') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Hide spinner when response received
                        formOTPContent.find('.spinner-border').hide();
                    },
                    error: function(xhr, status, error) {
                        // Hide spinner and show error
                        formOTPContent.find('.spinner-border').hide();
                        showToast('Có lỗi xảy ra khi gửi mã OTP', 'error');
                    }
                });
            });
        });

        //response save
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                @if (is_array(session('error')))
                    @foreach (session('error') as $message)
                        @foreach ($message as $key => $value)
                            showToast('{{ $value }}', 'error');
                        @endforeach
                    @endforeach
                @else
                    showToast('{{ session('error') }}', 'error');
                @endif
            });
        @endif
        
        // Function to handle OTP input
        function handleInput(input) {
            let value = input.value;
            
            // Only allow numbers
            input.value = value.replace(/[^0-9]/g, '');
            
            // Auto-move to next input
            if (value.length === 1) {
                let nextInput = input.nextElementSibling;
                if (nextInput && nextInput.tagName === 'INPUT') {
                    nextInput.focus();
                }
            }
        }
    </script>
@endpush