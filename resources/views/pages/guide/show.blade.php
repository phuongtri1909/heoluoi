@extends('layouts.app')

@section('title', $guide ? $guide->title : 'Hướng dẫn')
@section('description', $guide ? $guide->meta_description : 'Hướng dẫn sử dụng ' . config('app.name'))
@section('keywords', $guide ? $guide->meta_keywords : 'hướng dẫn, ' . config('app.name') . ', truyện')

@section('content')
<div class="container py-5 animate__animated animate__fadeIn">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-4 shadow-sm overflow-hidden">
                <div class="card-header bg-3 bg-gradient text-white p-4 border-4 animate__animated animate__fadeInDown">
                    <h1 class="h2 mb-0">{{ $guide->title ?? 'Hướng dẫn' }}</h1>
                </div>
                
                <div class="card-body p-lg-5 p-4 guide-content animate__animated animate__fadeInUp">
                    @if($guide)
                        {!! $guide->content !!}
                    @else
                        <div class="text-center py-5">
                            <i class="fa-solid fa-circle-info fa-3x mb-3 text-muted"></i>
                            <p class="lead">Thông tin hướng dẫn đang được cập nhật.</p>
                            <p>Vui lòng quay lại sau.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($guide)
                <div class="d-flex justify-content-between mt-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="{{ route('home') }}" class="btn bg-3 rounded-5 text-white">
                        <i class="fas fa-home me-2"></i> Trang chủ
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .guide-content {
        line-height: 1.8;
        color: #333;
    }
    
    .guide-content h2 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #2c3e50;
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 0.5rem;
    }
    
    .guide-content h3 {
        margin-top: 1.2rem;
        color: #3498db;
    }
    
    .guide-content p {
        margin-bottom: 1rem;
    }
    
    .guide-content ul, .guide-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    
    .guide-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin: 1.5rem 0;
    }
    
    .guide-content blockquote {
        background-color: #f8f9fa;
        border-left: 4px solid #3498db;
        padding: 1rem;
        margin: 1.5rem 0;
        border-radius: 0 8px 8px 0;
    }
    
    .guide-content a {
        color: #3498db;
        text-decoration: none;
        border-bottom: 1px dotted;
        transition: all 0.3s ease;
    }
    
    .guide-content a:hover {
        color: #2980b9;
        border-bottom: 1px solid;
    }
    
    .guide-content table {
        width: 100%;
        margin-bottom: 1.5rem;
        border-collapse: collapse;
    }
    
    .guide-content table th, .guide-content table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .guide-content table th {
        background-color: #f8f9fa;
    }
    
    .guide-content .table-responsive {
        overflow-x: auto;
        margin-bottom: 1.5rem;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #3498db;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #2980b9;
    }
    
    /* Animation classes */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    .fade-in-up {
        animation: fadeInUp 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize animations for elements that come into view
        const animateElements = document.querySelectorAll('.guide-content h2, .guide-content img, .guide-content blockquote');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        animateElements.forEach(element => {
            observer.observe(element);
        });
    
    });
</script>
@endsection 