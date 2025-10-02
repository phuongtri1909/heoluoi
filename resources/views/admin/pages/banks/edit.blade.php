@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0">
                <h5 class="mb-0">Chỉnh sửa ngân hàng</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-control-label">Tên ngân hàng</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" id="name" value="{{ old('name', $bank->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code" class="form-control-label">Mã ngân hàng</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" id="code" value="{{ old('code', $bank->code) }}" required>
                                <small class="form-text text-muted">Ví dụ: BIDV, VCB, TPB, MB, ...</small>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_number" class="form-control-label">Số tài khoản</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       name="account_number" id="account_number" value="{{ old('account_number', $bank->account_number) }}" required>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_name" class="form-control-label">Chủ tài khoản</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                       name="account_name" id="account_name" value="{{ old('account_name', $bank->account_name) }}" required>
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo" class="form-control-label">Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                       name="logo" id="logo" accept="image/*">
                                <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF. Để trống để giữ nguyên logo cũ.</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($bank->logo)
                                    <div class="mt-2">
                                        <p>Logo hiện tại:</p>
                                        <img src="{{ Storage::url($bank->logo) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qr_code" class="form-control-label">Mã QR</label>
                                <input type="file" class="form-control @error('qr_code') is-invalid @enderror" 
                                       name="qr_code" id="qr_code" accept="image/*">
                                <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng: JPG, PNG, GIF. Để trống để giữ nguyên mã QR cũ.</small>
                                @error('qr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($bank->qr_code)
                                    <div class="mt-2">
                                        <p>Mã QR hiện tại:</p>
                                        <img src="{{ Storage::url($bank->qr_code) }}" alt="Current QR Code" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="status" value="1" 
                                       {{ old('status', $bank->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Hoạt động</label>
                                <div class="form-text">Ngân hàng không hoạt động sẽ không hiển thị cho người dùng</div>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
                            <a href="{{ route('admin.banks.index') }}" class="btn btn-outline-secondary">Quay lại</a>
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