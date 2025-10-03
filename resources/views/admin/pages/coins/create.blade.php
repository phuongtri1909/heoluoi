@extends('admin.layouts.app')

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Cộng/Trừ cám cho người dùng</h6>
                        <a href="{{ route('admin.coins.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/defaults/avatar_default.jpg') }}" 
                                     class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ $user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="bg-gradient-primary text-white p-3 rounded">
                                <h6 class="mb-0">Số cám hiện tại</h6>
                                <h3 class="mb-0">{{ number_format($user->coins) }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    
                    <form action="{{ route('admin.coins.store', $user->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount" class="form-control-label">Số cám</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" min="1" value="{{ old('amount', 1) }}" required>
                                    @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-control-label">Loại giao dịch</label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="add" {{ old('type') === 'add' ? 'selected' : '' }}>Cộng cám</option>
                                        <option value="subtract" {{ old('type') === 'subtract' ? 'selected' : '' }}>Trừ cám</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note" class="form-control-label">Ghi chú (không bắt buộc)</label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" 
                                              id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                    @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn bg-gradient-primary">
                                <i class="fas fa-save me-2"></i> Lưu giao dịch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection