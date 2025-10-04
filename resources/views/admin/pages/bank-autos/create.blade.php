@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0 px-3">
                <h5 class="mb-0">Thêm ngân hàng tự động mới</h5>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="{{ route('admin.bank-autos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Tên ngân hàng</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Mã ngân hàng</label>
                                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code') }}" required>
                                <small class="form-text text-muted">Ví dụ: VCB, BIDV, TPB, MB, ...</small>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_number">Số tài khoản Casso</label>
                                <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror"
                                    value="{{ old('account_number') }}" required>
                                <small class="form-text text-muted">Số tài khoản ngân hàng được kết nối với Casso</small>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_name">Chủ tài khoản Casso</label>
                                <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror"
                                    value="{{ old('account_name') }}" required>
                                <small class="form-text text-muted">Tên chủ tài khoản ngân hàng Casso</small>
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror"
                                    accept="image/*">
                                <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="mt-2 d-none" id="logo-preview-container">
                                    <img src="" alt="Logo Preview" id="logo-preview" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qr_code">Mã QR</label>
                                <input type="file" name="qr_code" id="qr_code" class="form-control @error('qr_code') is-invalid @enderror"
                                    accept="image/*">
                                <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF</small>
                                @error('qr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="mt-2 d-none" id="qr-preview-container">
                                    <img src="" alt="QR Code Preview" id="qr-preview" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
                                <label class="form-check-label" for="status">Hoạt động</label>
                                <div class="form-text">Ngân hàng không hoạt động sẽ không hiển thị cho người dùng</div>
                            </div>
                        </div>

                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                            <a href="{{ route('admin.bank-autos.index') }}" class="btn btn-secondary">Trở về</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts-admin')
<script>
    $(document).ready(function() {
        // Preview for logo
        $('#logo').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#logo-preview').attr('src', e.target.result);
                    $('#logo-preview-container').removeClass('d-none');
                }
                reader.readAsDataURL(file);
                
                // Update file label
                $(this).next('.custom-file-label').html(file.name);
            }
        });
        
        // Preview for QR code
        $('#qr_code').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#qr-preview').attr('src', e.target.result);
                    $('#qr-preview-container').removeClass('d-none');
                }
                reader.readAsDataURL(file);
                
                // Update file label
                $(this).next('.custom-file-label').html(file.name);
            }
        });
    });
</script>
@endpush
