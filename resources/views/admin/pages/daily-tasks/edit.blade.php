@extends('admin.layouts.app')

@section('content-auth')
<div class="row">
    <div class="col-12">
        <div class="card mb-0 mb-md-4">
            <div class="card-header pb-0">
                <h5 class="mb-0">Chỉnh sửa nhiệm vụ</h5>
            </div>
            <div class="card-body">
                

                <form action="{{ route('admin.daily-tasks.update', $dailyTask) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-control-label">Tên nhiệm vụ</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $dailyTask->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-control-label">Loại nhiệm vụ</label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="">Chọn loại nhiệm vụ</option>
                                    <option value="login" {{ old('type', $dailyTask->type) === 'login' ? 'selected' : '' }}>Đăng nhập</option>
                                    <option value="comment" {{ old('type', $dailyTask->type) === 'comment' ? 'selected' : '' }}>Bình luận</option>
                                    <option value="bookmark" {{ old('type', $dailyTask->type) === 'bookmark' ? 'selected' : '' }}>Theo dõi</option>
                                    <option value="share" {{ old('type', $dailyTask->type) === 'share' ? 'selected' : '' }}>Chia sẻ</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description" class="form-control-label">Mô tả</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $dailyTask->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="coin_reward" class="form-control-label">Thưởng cám</label>
                                <input type="number" name="coin_reward" id="coin_reward" class="form-control @error('coin_reward') is-invalid @enderror"
                                    value="{{ old('coin_reward', $dailyTask->coin_reward) }}" min="0" required>
                                @error('coin_reward')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_per_day" class="form-control-label">Số lần tối đa/ngày</label>
                                <input type="number" name="max_per_day" id="max_per_day" class="form-control @error('max_per_day') is-invalid @enderror"
                                    value="{{ old('max_per_day', $dailyTask->max_per_day) }}" min="1" required>
                                @error('max_per_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order" class="form-control-label">Thứ tự hiển thị</label>
                                <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror"
                                    value="{{ old('order', $dailyTask->order) }}" min="0">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="active" id="active" {{ old('active', $dailyTask->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">Kích hoạt nhiệm vụ</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
                            <a href="{{ route('admin.daily-tasks.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection