@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <h5 class="mb-0">Thông tin Donate</h5>
            </div>
            <div class="card-body">
                

                <form action="{{ route('donate.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Title and Description -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3">
                                    <h6 class="mb-0">Nội dung</h6>
                                </div>
                                <div class="card-body p-3">
                                    <!-- Title Field -->
                                    <div class="form-group mb-3">
                                        <label for="title" class="form-control-label">Tiêu đề</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               name="title" id="title" value="{{ old('title', $donate->title ?? '') }}">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Description Field -->
                                    <div class="form-group">
                                        <label for="description" class="form-control-label">Mô tả</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  name="description" id="description" rows="6">{{ old('description', $donate->description ?? '') }}</textarea>
                                        <small class="form-text text-muted">
                                            Bạn có thể sử dụng định dạng HTML cơ bản.
                                        </small>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="about_us" class="form-control-label">Nội dung footer</label>
                                        <textarea class="form-control @error('about_us') is-invalid @enderror" 
                                                  name="about_us" id="about_us" rows="6">{{ old('about_us', $donate->about_us ?? '') }}</textarea>
                                        <small class="form-text text-muted">
                                            Bạn có thể sử dụng định dạng HTML cơ bản.
                                        </small>
                                        @error('about_us')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Image Section -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header p-3">
                                    <h6 class="mb-0">Mã QR</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="form-group">
                                        <label for="image_qr" class="form-control-label">Hình ảnh QR Code</label>
                                        <input type="file" class="form-control @error('image_qr') is-invalid @enderror" 
                                               name="image_qr" id="image_qr" accept="image/*">
                                        <small class="form-text text-muted">
                                            Hình ảnh QR code sẽ được tối ưu để dễ quét.
                                        </small>
                                        @error('image_qr')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    @if(isset($donate) && $donate->image_qr)
                                        <div class="mt-3 mb-4 text-center p-3 bg-light rounded">
                                            <p>QR Code hiện tại:</p>
                                            <img src="{{ Storage::url($donate->image_qr) }}" alt="QR Code" class="img-fluid" style="max-height: 200px;">
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
@endsection

@push('scripts-admin')
<script>
    // Initialize the editor if needed
    document.addEventListener('DOMContentLoaded', function() {
        // You can add a rich text editor here if desired
    });
</script>
@endpush