@extends('layouts.information')

@section('info_title', 'Đăng ký làm tác giả')
@section('info_description', 'Đăng ký làm tác giả để đăng truyện trên ' . config('app.name'))
@section('info_keyword', 'đăng ký tác giả, tác giả ' . config('app.name') . ', sáng tác truyện')

@section('info_section_title', 'Đăng ký làm tác giả')
@section('info_section_desc', 'Hãy điền đầy đủ thông tin để đăng ký trở thành tác giả trên ' . config('app.name'))

@push('styles')
<style>
    /* Styles được di chuyển vào information.css */
</style>
@endpush

@section('info_content')
    @if (isset($application))
        <div class="author-application-status status-{{ $application->status }} animate__animated animate__fadeIn">
            <h4 class="mb-3">
                @if ($application->isPending())
                    <i class="fa-solid fa-clock me-2"></i> Đơn đăng ký của bạn đang được xem xét
                @elseif ($application->isApproved())
                    <i class="fa-solid fa-check-circle me-2"></i> Đơn đăng ký của bạn đã được chấp nhận
                @elseif ($application->isRejected())
                    <i class="fa-solid fa-times-circle me-2"></i> Đơn đăng ký của bạn đã bị từ chối
                @endif
            </h4>
            
            <p>Ngày gửi đơn: {{ $application->submitted_at->format('d/m/Y H:i') }}</p>
            
            @if ($application->isApproved() || $application->isRejected())
                <p>Ngày xét duyệt: {{ $application->published_at->format('d/m/Y H:i') }}</p>
                
                @if ($application->admin_note)
                    <div class="author-admin-note animate__animated animate__fadeIn">
                        <h5 class="mb-2">Phản hồi từ quản trị viên:</h5>
                        <p class="mb-0">{{ $application->admin_note }}</p>
                    </div>
                @endif
            @endif
            
            @if ($application->isApproved())
                <div class="mt-4">
                    <a href="{{ route('user.author.index') }}" class="btn btn-success">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Đi đến khu vực tác giả
                    </a>
                </div>
            @elseif ($application->isRejected())
                <div class="mt-4">
                    <p>Bạn có thể gửi lại đơn đăng ký sau khi đã khắc phục các vấn đề.</p>
                    <button class="btn btn-primary" id="showNewApplicationForm">
                        <i class="fa-solid fa-paper-plane me-2"></i> Gửi đơn đăng ký mới
                    </button>
                </div>
                
                <div class="mt-4 d-none" id="newApplicationForm">
                    @include('pages.information.author.application_form')
                </div>
            @else
                <div class="alert alert-info mt-3">
                    <i class="fa-solid fa-info-circle me-2"></i> Đơn đăng ký của bạn đang được xem xét. Vui lòng đợi phản hồi từ quản trị viên.
                </div>
            @endif
        </div>
    @else
        @include('pages.information.author.application_form')
    @endif
@endsection

@push('info_scripts')
<script>
    $(document).ready(function() {
        // Character counter for introduction
        $('#introduction').on('input', function() {
            const maxLength = 1000;
            const minLength = 50;
            const currentLength = $(this).val().length;
            const $counter = $('#charCounter');
            
            $counter.text(`${currentLength}/${maxLength}`);
            
            if (currentLength < minLength) {
                $counter.removeClass('author-char-warning author-char-danger').addClass('author-char-danger');
            } else if (currentLength > maxLength * 0.8) {
                $counter.removeClass('author-char-warning author-char-danger').addClass('author-char-warning');
            } else {
                $counter.removeClass('author-char-warning author-char-danger');
            }
        });
        
        // Show new application form button
        $('#showNewApplicationForm').on('click', function() {
            $('#newApplicationForm').removeClass('d-none').addClass('animate__animated animate__fadeIn');
            $(this).addClass('d-none');
        });
        
        // Form validation for URLs
        function isValidUrl(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        }
        
        $('.validate-url').on('blur', function() {
            const url = $(this).val().trim();
            if (url && !isValidUrl(url)) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('URL không hợp lệ. Vui lòng nhập đúng định dạng URL (https://example.com)');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>
@endpush 