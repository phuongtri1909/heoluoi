@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0 px-3">
                <h5 class="mb-0">Thêm chủ đề mới</h5>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="{{ route('admin.tags.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Tên chủ đề <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn bg-gradient-primary">Lưu</button>
                        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Trở về</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
