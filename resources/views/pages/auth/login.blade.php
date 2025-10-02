@extends('layouts.main')
@section('title', 'Đăng nhập')

@push('styles-main')
    
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
                        </div>

                        <a href="{{ route('login.google') }}" class="btn w-100 mb-3 border auth-btn text-dark">
                            <img src="{{ asset('images/svg/google_2025.svg') }}" alt="Google" class="me-2"
                                height="30">
                            Đăng nhập với Google
                        </a>

                        <div class="d-flex align-items-center text-center my-4">
                            <hr class="flex-grow-1 border-top border-secondary">
                            <span class="px-2 text-dark">hoặc</span>
                            <hr class="flex-grow-1 border-top border-secondary">
                        </div>

                        <form action="{{ route('login.post') }}" method="post">
                            @csrf
                            <div class="mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" id="email" placeholder="name@example.com"
                                        value="{{ old('email') }}" required>
                                    <label for="email">Email của bạn</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" id="password" placeholder="Password" required>
                                    <label for="password">Mật khẩu</label>
                                    <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                        id="togglePassword"></i>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 text-end">
                                <a href="{{ route('forgot-password') }}" class="color-3 text-decoration-none">Quên mật
                                    khẩu?</a>
                            </div>

                            <button type="submit" class="auth-btn btn w-100 border">Đăng Nhập</button>

                            <div class="text-center mt-4">
                                <span>Chưa có tài khoản? </span>
                                <a href="{{ route('register') }}" class="auth-link text-decoration-none color-3">Đăng ký ngay</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
