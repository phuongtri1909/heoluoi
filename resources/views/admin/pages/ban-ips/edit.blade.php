@extends('admin.layouts.app')

@section('content-auth')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0 mx-0 mx-md-4 mb-md-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <h5 class="mb-0">Sửa thông tin IP cấm</h5>
                        <a href="{{ route('admin.ban-ips.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ban-ips.update', $banIp) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ip_address" class="form-label">Địa chỉ IP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                                           id="ip_address" name="ip_address" value="{{ old('ip_address', $banIp->ip_address) }}" 
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
                                            <option value="{{ $user->id }}" {{ old('user_id', $banIp->user_id) == $user->id ? 'selected' : '' }}>
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
                                      placeholder="Nhập lý do cấm IP...">{{ old('reason', $banIp->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Thông tin bổ sung</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Người cấm:</small>
                                            <p class="mb-0">{{ $banIp->bannedBy->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Ngày cấm:</small>
                                            <p class="mb-0">{{ $banIp->banned_at ? $banIp->banned_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.ban-ips.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn bg-gradient-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
