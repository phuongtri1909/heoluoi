@extends('admin.layouts.app')

@push('styles-admin')
<style>
    .icon-preview {
        font-size: 1.5rem;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 50%;
        margin: 0 auto;
    }
    
    .social-card {
        transition: transform 0.3s;
    }
    
    .social-card:hover {
        transform: translateY(-5px);
    }
    
    .icon-select-wrapper {
        position: relative;
    }
    
    .icon-dropdown {
        max-height: 200px;
        overflow-y: auto;
    }
    
    .icon-option {
        display: flex;
        align-items: center;
    }
    
    .icon-option i {
        width: 30px;
        text-align: center;
        margin-right: 10px;
    }
    
    /* Custom icon styles */
    .custom-zalo {
        display: inline-block;
        width: 1em;
        height: 1em;
        background-image: url("https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Icon_of_Zalo.svg/50px-Icon_of_Zalo.svg.png");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }
</style>
@endpush

@section('content-auth')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Quản lý liên kết mạng xã hội</h6>
                    <p class="text-sm text-muted">Quản lý các liên kết mạng xã hội hiển thị ở footer của website.</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <!-- Add New Social Link Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Thêm liên kết mạng xã hội mới</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.socials.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Tên</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="url">URL</label>
                                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                                   id="url" name="url" value="{{ old('url') }}" required>
                                            @error('url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="icon">Icon</label>
                                            <select class="form-control @error('icon') is-invalid @enderror" 
                                                    id="icon" name="icon" required>
                                                <option value="">Chọn icon</option>
                                                @foreach($fontAwesomeIcons as $iconClass => $iconName)
                                                    <option value="{{ $iconClass }}" data-icon="{{ $iconClass }}">
                                                        {{ $iconName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="sort_order">Thứ tự</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   name="is_active" checked>
                                            <label class="form-check-label" for="is_active">
                                                Hiển thị
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="icon-preview-container">
                                            <div class="icon-preview" id="iconPreview"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn btn-primary">Thêm mới</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Existing Social Links -->
                    <h6 class="mb-3">Danh sách liên kết mạng xã hội</h6>
                    <div class="row">
                        @forelse($socials as $social)
                            <div class="col-md-4 mb-4">
                                <div class="card social-card">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="icon-preview">
                                                <i class="{{ $social->icon }}"></i>
                                            </div>
                                            <h5 class="mt-2">{{ $social->name }}</h5>
                                            <a href="{{ $social->url }}" target="_blank" class="text-primary">
                                                {{ $social->url }}
                                            </a>
                                        </div>
                                        
                                        <form action="{{ route('admin.socials.update', $social) }}" method="POST" class="mt-3">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="name_{{ $social->id }}">Tên</label>
                                                        <input type="text" class="form-control" 
                                                               id="name_{{ $social->id }}" name="name" 
                                                               value="{{ $social->name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="sort_order_{{ $social->id }}">Thứ tự</label>
                                                        <input type="number" class="form-control" 
                                                               id="sort_order_{{ $social->id }}" name="sort_order" 
                                                               value="{{ $social->sort_order }}" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group mb-3">
                                                <label for="url_{{ $social->id }}">URL</label>
                                                <input type="url" class="form-control" 
                                                       id="url_{{ $social->id }}" name="url" 
                                                       value="{{ $social->url }}" required>
                                            </div>
                                            
                                            <div class="form-group mb-3">
                                                <label for="icon_{{ $social->id }}">Icon</label>
                                                <select class="form-control icon-select" 
                                                        id="icon_{{ $social->id }}" name="icon" required
                                                        data-preview="iconPreview_{{ $social->id }}">
                                                    @foreach($fontAwesomeIcons as $iconClass => $iconName)
                                                        <option value="{{ $iconClass }}" 
                                                                {{ $social->icon === $iconClass ? 'selected' : '' }}
                                                                data-icon="{{ $iconClass }}">
                                                            {{ $iconName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="is_active_{{ $social->id }}" name="is_active" 
                                                       {{ $social->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active_{{ $social->id }}">
                                                    Hiển thị
                                                </label>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fas fa-save me-1"></i> Lưu
                                                </button>
                                                
                                                <form action="{{ route('admin.socials.destroy', $social) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa liên kết này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Chưa có liên kết mạng xã hội nào. Hãy thêm liên kết mới.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts-admin')
<script>
    $(document).ready(function() {
        // Preview icon when selecting from dropdown
        $('#icon').on('change', function() {
            const iconClass = $(this).find(':selected').data('icon');
            if (iconClass) {
                if (iconClass.startsWith('custom-')) {
                    // Handle custom icons like Zalo
                    $('#iconPreview').html(`<span class="${iconClass}"></span>`);
                } else {
                    // Handle FontAwesome icons
                    $('#iconPreview').html(`<i class="${iconClass}"></i>`);
                }
            } else {
                $('#iconPreview').html('');
            }
        });
        
        // Preview icons for existing social links
        $('.icon-select').each(function() {
            const iconClass = $(this).find(':selected').data('icon');
            const previewId = $(this).data('preview');
            if (iconClass && previewId) {
                if (iconClass.startsWith('custom-')) {
                    // Handle custom icons like Zalo
                    $(`#${previewId}`).html(`<span class="${iconClass}"></span>`);
                } else {
                    // Handle FontAwesome icons
                    $(`#${previewId}`).html(`<i class="${iconClass}"></i>`);
                }
            }
        });
        
        // Handle icon select change for existing social links
        $('.icon-select').on('change', function() {
            const iconClass = $(this).find(':selected').data('icon');
            const previewId = $(this).data('preview');
            if (iconClass && previewId) {
                if (iconClass.startsWith('custom-')) {
                    // Handle custom icons like Zalo
                    $(`#${previewId}`).html(`<span class="${iconClass}"></span>`);
                } else {
                    // Handle FontAwesome icons
                    $(`#${previewId}`).html(`<i class="${iconClass}"></i>`);
                }
            }
        });
    });
</script>
@endpush 