@extends('layouts.app')

@section('title', 'Tài khoản bị khóa')

@push('styles')
<style>
    .ban-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .ban-container::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: moveBackground 20s linear infinite;
    }
    
    @keyframes moveBackground {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }
    
    .ban-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        padding: 3rem;
        max-width: 500px;
        width: 100%;
        position: relative;
        z-index: 1;
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .ban-icon-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }
    
    .ban-icon {
        font-size: 5rem;
        color: #dc3545;
        animation: shake 0.5s ease-in-out infinite alternate, pulse 2s ease-in-out infinite;
        display: inline-block;
    }
    
    @keyframes shake {
        0% { transform: rotate(-5deg); }
        100% { transform: rotate(5deg); }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }
    }
    
    .ban-icon-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.1);
        animation: ripple 2s ease-out infinite;
    }
    
    @keyframes ripple {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(1.5);
            opacity: 0;
        }
    }
    
    .ban-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        animation: fadeIn 0.8s ease-out 0.2s both;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .ban-message {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        border-left: 4px solid #ffc107;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        animation: fadeIn 0.8s ease-out 0.4s both;
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
    }
    
    .ban-message p {
        margin-bottom: 1rem;
        color: #856404;
        font-size: 1rem;
        line-height: 1.6;
    }
    
    .ban-message p:last-child {
        margin-bottom: 0;
    }
    
    .ban-link {
        color: #0d6efd;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .ban-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #0d6efd;
        transition: width 0.3s ease;
    }
    
    .ban-link:hover {
        color: #0a58ca;
    }
    
    .ban-link:hover::after {
        width: 100%;
    }
    
    .ban-button {
        background: var(--primary-color-2);
        border: none;
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        animation: fadeIn 0.8s ease-out 0.6s both;
    }
    
    .ban-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        color: white;
    }
    
    .ban-button:active {
        transform: translateY(0);
    }
    
    .ban-button i {
        font-size: 1.2rem;
    }
    
    @media (max-width: 768px) {
        .ban-card {
            padding: 2rem;
            margin: 1rem;
        }
        
        .ban-title {
            font-size: 1.5rem;
        }
        
        .ban-icon {
            font-size: 4rem;
        }
        
        .ban-icon-circle {
            width: 100px;
            height: 100px;
        }
    }
</style>
@endpush

@section('content')
<div class="ban-container">
    <div class="ban-card">
        <div class="text-center">
            <div class="ban-icon-wrapper">
                <div class="ban-icon-circle"></div>
                <i class="fas fa-ban ban-icon"></i>
            </div>
            
            <h1 class="ban-title">Tài khoản của bạn đã bị khóa</h1>
            
            <div class="ban-message">
                <p class="mb-2 fw-semibold">{{ $message }}</p>
                <p class="mb-0">
                    Nếu đây là sự cố ngoài ý muốn, vui lòng liên hệ 
                    <a href="{{ \App\Models\Config::getConfig('facebook_page_url', 'https://www.facebook.com/profile.php?id=61572454674711') }}" target="_blank" rel="noopener noreferrer" class="ban-link">
                        fan page
                    </a> 
                    để được hỗ trợ sớm nhất.
                </p>
            </div>
            
            <a href="{{ route('home') }}" class="ban-button">
                <i class="fas fa-home"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>
@endsection
