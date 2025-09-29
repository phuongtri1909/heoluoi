<!-- filepath: d:\truyen\cakhonho\resources\views\admin\pages\logo-site\edit.blade.php -->
@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0">
                <h5 class="mb-0">Cấu hình Logo và Favicon</h5>
            </div>
            <div class="card-body">
                

                <form action="{{ route('admin.logo-site.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Logo Section -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3">
                                    <h6 class="mb-0">Logo Trang Web</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="form-group">
                                        <label for="logo" class="form-control-label">Tệp Logo</label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                               name="logo" id="logo" accept="image/*">
                                        <small class="form-text text-muted">
                                            Chiều cao sẽ được điều chỉnh thành 50px. 
                                            Hình ảnh sẽ được chuyển đổi thành định dạng WebP để tối ưu.
                                        </small>
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    @if(isset($logoSite) && $logoSite->logo)
                                        <div class="mt-3 mb-4 text-center p-3 bg-light rounded">
                                            <p>Logo hiện tại:</p>
                                            <img src="{{ Storage::url($logoSite->logo) }}" alt="Site Logo" class="img-fluid">
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="deleteLogo()">
                                                    <i class="fas fa-trash"></i> Xóa Logo
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Favicon Section -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3">
                                    <h6 class="mb-0">Favicon</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="form-group">
                                        <label for="favicon" class="form-control-label">Tệp Favicon</label>
                                        <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                                               name="favicon" id="favicon" accept="image/*,image/x-icon">
                                        <small class="form-text text-muted">
                                            Favicon sẽ được điều chỉnh về kích thước 32x32px và chuyển đổi thành định dạng WebP.
                                            Đề xuất dùng hình ảnh vuông.
                                        </small>
                                        @error('favicon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    @if(isset($logoSite) && $logoSite->favicon)
                                        <div class="mt-3 mb-4 text-center p-3 bg-light rounded">
                                            <p>Favicon hiện tại:</p>
                                            <div style="width: 32px; height: 32px; display: inline-block; border: 1px solid #ddd;">
                                                <img src="{{ Storage::url($logoSite->favicon) }}" alt="Favicon" class="img-fluid">
                                            </div>
                                            <div class="mt-2">
                                                <small>Hiển thị kích thước thực</small>
                                            </div>
                                            <div class="mt-3">
                                                <img src="{{ Storage::url($logoSite->favicon) }}" alt="Favicon" style="width: 64px; height: 64px;">
                                                <div>
                                                    <small>Phóng to 2x</small>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="deleteFavicon()">
                                                    <i class="fas fa-trash"></i> Xóa Favicon
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn bg-gradient-primary">Lưu cấu hình</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteLogo() {
    if (confirm('Bạn có chắc chắn muốn xóa logo này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.logo-site.delete-logo") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteFavicon() {
    if (confirm('Bạn có chắc chắn muốn xóa favicon này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.logo-site.delete-favicon") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection