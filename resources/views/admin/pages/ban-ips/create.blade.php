@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <h5 class="mb-0">Thêm IP cấm</h5>
                        <a href="{{ route('admin.ban-ips.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ban-ips.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ip_address" class="form-label">Địa chỉ IP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                                           id="ip_address" name="ip_address" value="{{ old('ip_address') }}" 
                                           placeholder="192.168.1.1" required>
                                    @error('ip_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Người dùng</label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                        <option value="">Chọn người dùng (tùy chọn)</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('user_id') ?? request('user_id')) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Lý do cấm</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="3" 
                                      placeholder="Nhập lý do cấm IP...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.ban-ips.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn bg-gradient-primary">Thêm IP cấm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
