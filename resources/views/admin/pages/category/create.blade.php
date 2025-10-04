@extends('admin.layouts.app')

@push('styles-admin')
<style>
    @media (max-width: 768px) {
        .row .col-md-12 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .form-control,
        .form-select {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
        
        textarea.form-control {
            min-height: 100px;
        }
    }
</style>
@endpush

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
            <div class="card-header pb-0 px-3">
                <h5 class="mb-0">Thêm thể loại mới</h5>
            </div>
            <div class="card-body pt-4 p-3">
                

                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Tên thể loại</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Mô tả</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_main" id="is_main" {{ old('is_main') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_main">Đánh dấu là thể loại chính</label>
                                <div class="form-text">Thể loại chính sẽ được hiển thị nổi bật trong hệ thống</div>
                            </div>
                        </div>

                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Trở về</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection