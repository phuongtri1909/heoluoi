@extends('layouts.main')
@section('title', 'Tạo tài khoản')
@section('description', 'Tạo tài khoản con đường bá chủ')
@section('keywords', 'Tạo tài khoản con đường bá chủ')

@push('styles-main')
    <style>
        .logo_conduongbachu {
            height: 75px;
            object-fit: contain;
            transition: height 0.3s ease;
        }

        @media (max-width: 768px) {
            .logo_conduongbachu {
                height: 60px;
            }
        }

        @media (max-width: 576px) {
            .logo_conduongbachu {
                height: 50px;
            }
        }

        /*  */
        .cursor-pointer {
            cursor: pointer;
        }

        .avatar-preview:hover {
            border-color: #0d6efd !important;
            opacity: 0.8;
        }
    </style>
@endpush

@section('content-main')
    <div class="auth-container d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card p-4 p-md-5">
                        <div class="text-center mb-4">
                            <a href="{{ route('home') }}">
                                @php
                                    // Get the logo and favicon from LogoSite model
                                    $logoSite = \App\Models\LogoSite::first();
                                    $logoPath =
                                        $logoSite && $logoSite->logo
                                            ? Storage::url($logoSite->logo)
                                            : asset('images/logo/logo-site.png');
                                @endphp
                                <img class="auth-logo mb-4" src="{{ $logoPath }}" alt="logo">
                            </a>
                            <h1 class="auth-title  color-3 text-decoration-none">Tạo Tài Khoản Mới</h1>
                        </div>

                        <a href="{{ route('login.google') }}" class="btn w-100 mb-3 auth-btn border text-dark">
                            <img src="{{ asset('images/svg/google_2025.svg') }}" alt="Google" class="me-2"
                                height="30">
                            Đăng nhập với Google
                        </a>

                        <div class="d-flex align-items-center text-center my-4">
                            <hr class="flex-grow-1 border-top border-secondary">
                            <span class="px-2 text-dark">hoặc</span>
                            <hr class="flex-grow-1 border-top border-secondary">
                        </div>

                        <form id="registerForm">
                            <div class="form-email mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="name@example.com" required>
                                    <label for="email">Email của bạn</label>
                                </div>
                            </div>

                            <div id="otpPasswordContainer" class="overflow-hidden text-center">
                                <!-- OTP inputs will be inserted here via JavaScript -->
                            </div>

                            <div class="box-button">
                                <button type="submit" class="auth-btn btn w-100 border" id="btn-send">
                                    Tiếp Tục
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <span>Đã có tài khoản? </span>
                                <a href="{{ route('login') }}" class="auth-link color-3 text-decoration-none">Đăng nhập</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-main')
    <script>
        // Xử lý khi người dùng nhấn nút gửi mã OTP
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                const emailInput = $('#email');
                const email = emailInput.val();
                const submitButton = $('#btn-send');

                // Xóa thông báo lỗi cũ nếu tồn tại
                const oldInvalidFeedback = emailInput.parent().find('.invalid-feedback');
                emailInput.removeClass('is-invalid');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }

                // Thay đổi nút submit thành trạng thái loading
                submitButton.prop('disabled', true);
                submitButton.html('<span class="loading-spinner"></span> Đang xử lý...');

                $.ajax({
                    url: '{{ route('register') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        email: email
                    }),
                    success: function(response) {


                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            submitButton.remove();

                            $('.form-email').remove();

                            $('#otpPasswordContainer').html(`
                                <span class="text-center mb-1">${response.message}</span>
                                <div class="otp-container justify-content-center mb-3" id="input-otp">
                                    <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                                    <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                                    <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                                    <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                                    <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                                    <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                                    <br>
                                </div>
                                <div class="col-12">
                                    <div class="text-center mb-4">
                                        <div class="avatar-upload position-relative mx-auto" style="width: 150px;height:150px;">
                                            <input type="file" class="d-none" id="avatarInput" name="avatar" accept="image/*">
                                            <div id="avatarPreview" class="avatar-preview rounded cursor-pointer d-flex align-items-center justify-content-center" style="width: 100%; height: 100%; border: 2px dashed #ccc; overflow: hidden;">
                                                <i class="fas fa-camera fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="avatar-helper">
                                            <small class="text-muted mt-2">Click để chọn ảnh đại diện (không bắt buộc)</small>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-3 position-relative">
                                        <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password" required>
                                        <label for="password" class="form-label">Mật khẩu</label>
                                        <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword"></i>
                                    </div>
                                    
                                    <div class="form-floating mb-3 position-relative">
                                        <input type="text" class="form-control" name="name" id="name" value="" placeholder="Name" required>
                                        <label for="name" class="form-label">Họ và tên</label>
                                    </div>
                                </div>
                            `);

                            $('.box-button').html(`
                                <button class="w-100 btn btn-lg border auth-btn" type="button" id="submitOtpPassword">Xác nhận</button>
                            `);


                            // Đoạn js xử lý chọn ảnh đại diện
                            const avatarPreview = document.getElementById('avatarPreview');
                            const avatarInput = document.getElementById('avatarInput');

                            if (avatarPreview && avatarInput) {
                                avatarPreview.addEventListener('click', function() {
                                    avatarInput.click();
                                });

                                avatarInput.addEventListener('change', function(e) {
                                    if (e.target.files && e.target.files[0]) {
                                        const reader = new FileReader();
                                        $('.avatar-helper').find('.invalid-feedback')
                                            .remove();

                                        reader.onload = function(e) {
                                            avatarPreview.innerHTML =
                                                `<img src="${e.target.result}" class="w-100 h-100" style="object-fit: cover;">`;
                                            avatarPreview.style.border = 'none';
                                            $('.avatar-helper small').removeClass(
                                                'd-none');
                                        }

                                        reader.readAsDataURL(e.target.files[0]);
                                    }
                                });
                            }

                            $('#submitOtpPassword').on('click', function() {
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');
                                const passwordInput = $('#password');
                                const nameInput = $('#name');
                                const avatarInput = $('#avatarInput')[0];

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });
                                const formData = new FormData();
                                formData.append('email', email);
                                formData.append('otp', otp);
                                formData.append('password', passwordInput.val());
                                formData.append('name', nameInput.val());
                                if (avatarInput.files[0]) {
                                    formData.append('avatar', avatarInput.files[0]);
                                }


                                removeInvalidFeedback(passwordInput);
                                input_otp.find('.invalid-otp').remove();
                                removeInvalidFeedback(emailInput);
                                removeInvalidFeedback(nameInput);
                                $('.avatar-helper').find('.invalid-feedback').remove();


                                $.ajax({
                                    url: '{{ route('register') }}',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    success: function(response) {

                                        if (response.status === 'success') {
                                            showToast(response.message,
                                                'success');
                                            saveToast(response.message,
                                                response.status);
                                            window.location.href = response
                                                .url;
                                        } else {
                                            showToast(response.message,
                                                'error');
                                        }
                                    },
                                    error: function(xhr) {
                                        const response = xhr.responseJSON;

                                        if (response && response.status ===
                                            'error') {
                                            if (response.message.email) {
                                                response.message.email
                                                    .forEach(error => {
                                                        const
                                                            invalidFeedback =
                                                            $(
                                                                '<div class="invalid-feedback"></div>'
                                                            )
                                                            .text(
                                                                error);
                                                        emailInput
                                                            .addClass(
                                                                'is-invalid'
                                                            )
                                                            .parent()
                                                            .append(
                                                                invalidFeedback
                                                            );
                                                    });
                                            }
                                            if (response.message.otp) {
                                                input_otp.append(
                                                    `<div class="invalid-otp text-danger fs-7">${response.message.otp[0]}</div>`
                                                );
                                            }
                                            if (response.message.password) {
                                                response.message.password
                                                    .forEach(error => {
                                                        const
                                                            invalidFeedback =
                                                            $(
                                                                '<div class="invalid-feedback"></div>'
                                                            )
                                                            .text(
                                                                error);
                                                        passwordInput
                                                            .addClass(
                                                                'is-invalid'
                                                            )
                                                            .parent()
                                                            .append(
                                                                invalidFeedback
                                                            );
                                                    });
                                            }
                                            if (response.message.name) {
                                                response.message.name
                                                    .forEach(error => {
                                                        const
                                                            invalidFeedback =
                                                            $(
                                                                '<div class="invalid-feedback"></div>'
                                                            )
                                                            .text(
                                                                error);
                                                        nameInput
                                                            .addClass(
                                                                'is-invalid'
                                                            )
                                                            .parent()
                                                            .append(
                                                                invalidFeedback
                                                            );
                                                    });
                                            }

                                            if (response.message.avatar) {

                                                $('.avatar-helper small')
                                                    .addClass('d-none');
                                                response.message.avatar
                                                    .forEach(error => {
                                                        const
                                                            invalidFeedback =
                                                            $(
                                                                '<div class="invalid-feedback d-block text-center"></div>'
                                                            )
                                                            .text(
                                                                error);
                                                        $('.avatar-helper')
                                                            .append(
                                                                invalidFeedback
                                                            );
                                                    });
                                            } else {
                                                $('.avatar-helper small')
                                                    .removeClass('d-none');
                                            }

                                        } else {
                                            showToast(
                                                'Đã xảy ra lỗi, vui lòng thử lại.',
                                                'error');
                                        }
                                    }
                                });
                            });

                        } else {
                            console.log(response);

                            showToast(response.message, 'error');
                            submitButton.prop('disabled', false);
                            submitButton.html('Tiếp tục');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        console.log(response);


                        if (response && response.message && response.message.email) {
                            response.message.email.forEach(error => {
                                const invalidFeedback = $(
                                    '<div class="invalid-feedback"></div>').text(
                                    error);
                                emailInput.addClass('is-invalid').parent().append(
                                    invalidFeedback);
                            });
                        } else {
                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                        }
                        submitButton.prop('disabled', false);
                        submitButton.html('Tiếp tục');
                    }
                });
            });
        });


        function removeInvalidFeedback(input) {
            const oldInvalidFeedback = input.parent().find('.invalid-feedback');
            input.removeClass('is-invalid');
            if (oldInvalidFeedback.length) {
                oldInvalidFeedback.remove();
            }
        }
    </script>
@endpush
