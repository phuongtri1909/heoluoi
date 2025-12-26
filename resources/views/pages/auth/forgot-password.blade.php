@extends('layouts.main')
@section('title', 'Quên mật khẩu')
@section('description', 'Quên mật khẩu')
@section('keywords', 'Quên mật khẩu')

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

        .cursor-pointer {
            cursor: pointer;
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
                            <h1 class="auth-title">Bạn quên mật khẩu rồi à?</h1>
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

                        <form id="forgotForm">
                            <div class="form-email mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="name@example.com" required>
                                    <label for="email">Nhập email của bạn</label>
                                </div>
                            </div>

                            <div id="otpContainer" class="overflow-hidden text-center">
                                <!-- OTP inputs will be inserted here via JavaScript -->
                            </div>

                            <div id="passwordContainer"></div>

                            <div class="box-button">
                                <button type="submit" class="auth-btn btn w-100 border" id="btn-send">
                                    Tiếp Tục
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <span>Bạn đã nhớ mật khẩu? </span>
                                <a href="{{ route('login') }}" class="auth-link text-decoration-none color-3">Đăng nhập</a>
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
        $(document).ready(function() {
            $('#forgotForm').on('submit', function(e) {
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
                    url: '{{ route('forgot.password') }}',
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

                            $('#otpContainer').html(`
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
                            `);

                            $('.box-button').html(`
                                <button class="auth-btn btn w-100 mb-4 border" type="button" id="submitOtp">Tiếp tục</button>
                            `);

                            $('#submitOtp').on('click', function() {
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });

                                input_otp.find('.invalid-otp').remove();

                                removeInvalidFeedback(emailInput);

                                $.ajax({
                                    url: '{{ route('forgot.password') }}',
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    data: JSON.stringify({
                                        email: email,
                                        otp: otp,
                                    }),
                                    success: function(response) {

                                        if (response.status === 'success') {
                                            showToast(response.message,
                                                'success');
                                            $('#submitOtp').remove();
                                            $('#otpContainer').remove();

                                            $('#passwordContainer').html(`
                                                <div class="mb-4">
                                                    <span class="text-center d-block mb-3">${response.message}</span>
                                                    <div class="form-floating mb-3 position-relative">
                                                        <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password" required>
                                                        <label for="password" class="form-label">Mật khẩu mới</label>
                                                        <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword"></i>
                                                    </div>
                                                </div>
                                            `);

                                            $('.box-button').html(`
                                                <button class="auth-btn btn w-100 mb-4 border" type="button" id="submitPassword">Xác nhận</button>
                                            `);

                                            // Add toggle password functionality
                                            $('#togglePassword').on('click',
                                                function() {
                                                    const
                                                        passwordInput =
                                                        $('#password');
                                                    const type =
                                                        passwordInput
                                                        .attr(
                                                        'type') ===
                                                        'password' ?
                                                        'text' :
                                                        'password';
                                                    passwordInput.attr(
                                                        'type', type
                                                        );
                                                    $(this).toggleClass(
                                                        'fa-eye fa-eye-slash'
                                                        );
                                                });

                                            $('#submitPassword').on('click',
                                                function() {
                                                    const
                                                        passwordInput =
                                                        $('#password');
                                                    const password =
                                                        passwordInput
                                                        .val();

                                                    removeInvalidFeedback
                                                        (passwordInput);

                                                    $.ajax({
                                                        url: '{{ route('forgot.password') }}',
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        data: JSON
                                                            .stringify({
                                                                email: email,
                                                                otp: otp,
                                                                password: password
                                                            }),
                                                        success: function(
                                                            response
                                                            ) {
                                                            if (response
                                                                .status ===
                                                                'success'
                                                                ) {
                                                                showToast
                                                                    (response
                                                                        .message,
                                                                        'success'
                                                                        );
                                                                saveToast
                                                                    (response
                                                                        .message,
                                                                        response
                                                                        .status
                                                                        );
                                                                window
                                                                    .location
                                                                    .href =
                                                                    response
                                                                    .url;
                                                            } else {
                                                                showToast
                                                                    (response
                                                                        .message,
                                                                        'error'
                                                                        );
                                                            }
                                                        },
                                                        error: function(
                                                            xhr
                                                            ) {
                                                            const
                                                                response =
                                                                xhr
                                                                .responseJSON;

                                                            if (response &&
                                                                response
                                                                .status ===
                                                                'error'
                                                                ) {
                                                                if (response
                                                                    .message
                                                                    .password
                                                                    ) {
                                                                    response
                                                                        .message
                                                                        .password
                                                                        .forEach(
                                                                            error => {
                                                                                const
                                                                                    invalidFeedback =
                                                                                    $(
                                                                                        '<div class="invalid-feedback"></div>')
                                                                                    .text(
                                                                                        error
                                                                                        );
                                                                                passwordInput
                                                                                    .addClass(
                                                                                        'is-invalid'
                                                                                        )
                                                                                    .parent()
                                                                                    .append(
                                                                                        invalidFeedback
                                                                                        );
                                                                            }
                                                                            );
                                                                }
                                                            } else {
                                                                showToast
                                                                    ('Đã xảy ra lỗi, vui lòng thử lại.',
                                                                        'error'
                                                                        );
                                                            }
                                                        }
                                                    });
                                                });
                                        } else {
                                            showToast(response.message,
                                                'error');
                                        }
                                    },
                                    error: function(xhr) {
                                        const response = xhr.responseJSON;

                                        if (response && response.status ===
                                            'error') {
                                            if (typeof response.message === 'string') {
                                                showToast(response.message, 'error');
                                            } else if (response.message.email) {
                                                response.message.email
                                                    .forEach(error => {
                                                        const
                                                            invalidFeedback =
                                                            $(
                                                                '<div class="invalid-feedback"></div>')
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
                                        } else {
                                            showToast(
                                                'Đã xảy ra lỗi, vui lòng thử lại.',
                                                'error');
                                        }
                                    }
                                });
                            });
                        } else {
                            showToast(response.message, 'error');
                            submitButton.prop('disabled', false);
                            submitButton.html('Tiếp tục');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        if (response && response.message) {
                            if (typeof response.message === 'string') {
                                showToast(response.message, 'error');
                            } 
                            else if (response.message.email) {
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
                        } else {
                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                        }
                        submitButton.prop('disabled', false);
                        submitButton.html('Tiếp tục');
                    }
                });
            });

            // Helper function to remove invalid feedback
            function removeInvalidFeedback(input) {
                const oldInvalidFeedback = input.parent().find('.invalid-feedback');
                input.removeClass('is-invalid');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }
            }
        });
    </script>
@endpush
