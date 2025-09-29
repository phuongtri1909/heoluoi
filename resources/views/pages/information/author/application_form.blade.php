<div class="application-form">
    <form action="{{ route('user.author.submit') }}" method="POST">
        @csrf
        
        <div class="alert alert-info mb-4">
            <i class="fa-solid fa-info-circle me-2"></i> Để trở thành tác giả, bạn cần cung cấp thông tin liên hệ và giới thiệu bản thân. Đơn đăng ký của bạn sẽ được xem xét và phản hồi trong vòng 24-48 giờ.
        </div>

        <div class="form-group">
            <label for="facebook_link" class="form-label">Link Facebook <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-brands fa-facebook"></i></span>
                <input type="url" class="form-control validate-url @error('facebook_link') is-invalid @enderror" 
                       id="facebook_link" name="facebook_link" 
                       placeholder="https://facebook.com/profile" 
                       value="{{ old('facebook_link') }}" required>
                <div class="invalid-feedback">
                    @error('facebook_link')
                        {{ $message }}
                    @else
                        Vui lòng nhập link Facebook hợp lệ
                    @enderror
                </div>
            </div>
            <small class="text-muted">Link Facebook cá nhân của bạn để liên hệ</small>
        </div>

        <div class="form-group">
            <label for="telegram_link" class="form-label">Link Telegram (không bắt buộc)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-brands fa-telegram"></i></span>
                <input type="url" class="form-control validate-url @error('telegram_link') is-invalid @enderror" 
                       id="telegram_link" name="telegram_link" 
                       placeholder="https://t.me/username" 
                       value="{{ old('telegram_link') }}">
                <div class="invalid-feedback">
                    @error('telegram_link')
                        {{ $message }}
                    @else
                        Vui lòng nhập link Telegram hợp lệ
                    @enderror
                </div>
            </div>
            <small class="text-muted">Link Telegram của bạn (nếu có)</small>
        </div>

        <div class="form-group">
            <label for="other_platform" class="form-label">Nền tảng khác (không bắt buộc)</label>
            <input type="text" class="form-control @error('other_platform') is-invalid @enderror" 
                   id="other_platform" name="other_platform" 
                   placeholder="Facebook, Wattpad, Truyenfull, v.v." 
                   value="{{ old('other_platform') }}">
            <div class="invalid-feedback">
                @error('other_platform')
                    {{ $message }}
                @enderror
            </div>
            <small class="text-muted">Nền tảng khác mà bạn đã từng đăng truyện</small>
        </div>

        <div class="form-group">
            <label for="other_platform_link" class="form-label">Link nền tảng khác (không bắt buộc)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-link"></i></span>
                <input type="url" class="form-control validate-url @error('other_platform_link') is-invalid @enderror" 
                       id="other_platform_link" name="other_platform_link" 
                       placeholder="https://example.com/profile" 
                       value="{{ old('other_platform_link') }}">
                <div class="invalid-feedback">
                    @error('other_platform_link')
                        {{ $message }}
                    @else
                        Vui lòng nhập link hợp lệ
                    @enderror
                </div>
            </div>
            <small class="text-muted">Link tới trang cá nhân của bạn trên nền tảng khác</small>
        </div>

        <div class="form-group">
            <label for="introduction" class="form-label">Giới thiệu bản thân (không bắt buộc)</label>
            <textarea class="form-control @error('introduction') is-invalid @enderror" 
                      id="introduction" name="introduction" rows="5" 
                      placeholder="Giới thiệu về bạn, kinh nghiệm viết truyện, thể loại sở trường..." 
                      minlength="50" maxlength="1000">{{ old('introduction') }}</textarea>
            <div class="invalid-feedback">
                @error('introduction')
                    {{ $message }}
                @else
                    Vui lòng nhập ít nhất 50 ký tự.
                @enderror
            </div>
            <div class="char-counter" id="charCounter">0/1000</div>
            <small class="text-muted">Giới thiệu về bản thân, kinh nghiệm viết truyện, thể loại sở trường, v.v. (ít nhất 50 ký tự)</small>
        </div>

        <div class="form-group text-center mt-4 mb-2">
            <button type="submit" class="btn author-submit-btn btn-lg text-white px-5">
                <i class="fa-solid fa-paper-plane me-2"></i> Gửi đơn đăng ký
            </button>
        </div>
    </form>
</div> 