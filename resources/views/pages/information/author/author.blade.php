@extends('layouts.information')

@section('info_title', 'Khu vực tác giả')
@section('info_description', 'Khu vực tác giả của bạn trên ' . request()->getHost())
@section('info_keyword', 'quản lý truyện, đăng truyện, tác giả, ' . request()->getHost())
@section('info_section_title', 'Khu vực tác giả')
@section('info_section_desc', 'Quản lý truyện và theo dõi doanh thu')

@push('styles')
<style>
    .author-dashboard-card {
        border-radius: 20px;
        padding: 25px;
        height: 135px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        border: none;
    }
    
    .author-dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }
    
    .author-dashboard-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.1;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.5s ease;
    }
    
    .author-dashboard-card:hover::before {
        transform: scale(6);
        opacity: 0.15;
    }
    
    .card-revenue {
        background: linear-gradient(135deg, #20b2aa, #57c5b6);
    }
    
    .card-stories {
        background: linear-gradient(135deg, #7bc5ae, #9ed2be);
    }
    
    .card-pending {
        background: linear-gradient(135deg, #e9b384, #f3deba);
    }
    
    .card-support {
        background: linear-gradient(135deg, #64b5f6, #1e88e5);
    }
    
    .author-card-title {
        color: rgba(255, 255, 255, 0.85);
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 1;
    }
    
    .author-card-value {
        color: white;
        font-size: 25px;
        font-weight: 700;
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        margin: 8px 0;
    }
    
    .author-card-value i {
        margin-right: 15px;
        font-size: 26px;
        background-color: rgba(255, 255, 255, 0.15);
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .author-button {
        border-radius: 30px;
        padding: 12px 24px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: white;
        border: none;
        color: #333;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .author-button:hover {
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .author-button i {
        font-size: 18px;
    }
    
    .author-button.primary {
        background: linear-gradient(135deg, #20b2aa, #57c5b6);
        color: white;
    }
    
    .author-button.secondary {
        background: white;
        color: #333;
    }
    
    .revenue-chart {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-top: 30px;
    }
    
    .revenue-chart:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    
    .revenue-chart-title {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .revenue-chart-title i {
        font-size: 20px;
        margin-right: 10px;
        color: #20b2aa;
    }
    
    .loading-animation {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        color: #999;
    }
    
    .action-buttons {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulseGlow {
        0% { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        50% { box-shadow: 0 4px 20px rgba(32, 178, 170, 0.3); }
        100% { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    }

    .stats-widget {
        margin-bottom: 30px;
        animation: fadeIn 0.6s ease-in-out;
    }

    .recent-activities {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-top: 30px;
    }

    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
    }

    .bg-view {
        background: linear-gradient(135deg, #78c1f3, #9be8ff);
    }

    .bg-comment {
        background: linear-gradient(135deg, #e9b384, #f3deba);
    }

    .bg-update {
        background: linear-gradient(135deg, #7bc5ae, #9ed2be);
    }

    .revenue-change {
        color: rgba(255, 255, 255, 0.9);
        margin-top: 5px;
        font-weight: 500;
        font-size: 13px;
        position: relative;
        z-index: 1;
    }
    
    .revenue-change .text-success {
        color: #b3ffb3 !important;
    }
    
    .revenue-change .text-danger {
        color: #ffb3b3 !important;
    }
</style>
@endpush

@section('info_content')
    <div class="row g-4 mb-4">
        <!-- Doanh thu tháng -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ route('user.author.revenue') }}" class="text-decoration-none">
                <div class="author-dashboard-card card-revenue">
                    <div class="author-card-title">
                        Doanh thu tháng {{ date('m/Y') }}
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="author-card-value">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ number_format($totalRevenue ?? 0) }} xu</span>
                    </div>
                    @if(isset($revenueChangePercent) && $revenueChangePercent != 0)
                        <div class="revenue-change small">
                            @if($revenueIncreased)
                                <span class="text-success">
                                    <i class="fas fa-arrow-up"></i> +{{ $revenueChangePercent }}% so với tháng trước
                                </span>
                            @else
                                <span class="text-danger">
                                    <i class="fas fa-arrow-down"></i> {{ $revenueChangePercent }}% so với tháng trước
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </a>
        </div>
        
        <!-- Truyện của tôi -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ route('user.author.stories') }}" class="text-decoration-none">
                <div class="author-dashboard-card card-stories">
                    <div class="author-card-title">
                        Truyện của tôi
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="author-card-value">
                        <i class="fas fa-book"></i>
                        <span>{{ $stories->total() ?? 0 }}</span>
                    </div>
                </div>
            </a>
        </div>
        
      
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ route('user.author.stories.create') }}" class="text-decoration-none">
                <div class="author-dashboard-card card-pending">
                    <div class="author-card-title">
                        ĐĂNG TRUYỆN
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="author-card-value">
                        <i class="fas fa-book"></i>
                        <span>{{ $stories->total() ?? 0 }}</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Yêu cầu hỗ trợ -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="author-dashboard-card card-support">
                <div class="author-card-title">
                    Thống kê nhanh
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-white">Lượt xem:</span>
                        <span class="small text-white fw-bold">{{ number_format($totalViews ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-white">Chương:</span>
                        <span class="small text-white fw-bold">{{ number_format($totalChapters ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-white">Người theo dõi:</span>
                        <span class="small text-white fw-bold">{{ number_format($totalFollowers ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="revenue-chart">
                <div class="revenue-chart-title">
                    <i class="fas fa-chart-bar"></i>
                    <h5 class="mb-0">Biểu đồ hiệu suất doanh thu tháng</h5>
                </div>
                <div class="mb-4 mt-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="chart-year" class="form-label">Năm</label>
                            <select id="chart-year" class="form-select">
                                @php
                                    $currentYear = date('Y');
                                    $years = range($currentYear, $currentYear - 3);
                                @endphp
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="chart-month" class="form-label">Tháng</label>
                            <select id="chart-month" class="form-select">
                                <option value="">Tất cả các tháng</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>Tháng {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="load-chart-btn" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </div>
                <div class="loading-animation" id="chart-loading">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                        <p>Đang tải dữ liệu...</p>
                    </div>
                </div>
                <div id="chart-container" style="height: 300px; display: none;">
                    <canvas id="revenue-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Thêm hiệu ứng pulse cho các card
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.author-dashboard-card');
        
        cards.forEach((card, index) => {
            // Thêm delay cho mỗi card để tạo hiệu ứng lần lượt
            setTimeout(() => {
                card.style.animation = 'pulseGlow 2s infinite';
            }, index * 300);
        });
        
        // Thêm sự kiện hover cho các card
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.animation = 'none';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.animation = 'pulseGlow 2s infinite';
            });
        });
        
        // Khởi tạo biểu đồ doanh thu
        initRevenueChart();
    });
    
    let revenueChart = null;
    
    // Hàm khởi tạo biểu đồ doanh thu
    function initRevenueChart() {
        const loadButton = document.getElementById('load-chart-btn');
        
        if (loadButton) {
            loadButton.addEventListener('click', loadChartData);
            
            // Tải dữ liệu biểu đồ ban đầu
            loadChartData();
        }
    }
    
    // Hàm tải dữ liệu biểu đồ
    function loadChartData() {
        const year = document.getElementById('chart-year').value;
        const month = document.getElementById('chart-month').value;
        const chartLoading = document.getElementById('chart-loading');
        const chartContainer = document.getElementById('chart-container');
        
        // Hiển thị loading, ẩn biểu đồ
        chartLoading.style.display = 'flex';
        chartContainer.style.display = 'none';
        
        // Tạo URL cho API dựa trên tham số
        let url = `{{ route('user.author.revenue.data') }}?year=${year}`;
        if (month) {
            url += `&month=${month}`;
        }
        
        // Gọi API lấy dữ liệu
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Cập nhật biểu đồ
                updateChart(data);
                
                // Ẩn loading, hiển thị biểu đồ
                chartLoading.style.display = 'none';
                chartContainer.style.display = 'block';
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                chartLoading.style.display = 'none';
                
                // Hiển thị thông báo lỗi
                chartContainer.style.display = 'block';
                chartContainer.innerHTML = '<div class="text-center text-danger">Có lỗi xảy ra khi tải dữ liệu</div>';
            });
    }
    
    // Hàm cập nhật biểu đồ
    function updateChart(data) {
        const ctx = document.getElementById('revenue-chart').getContext('2d');
        
        // Nếu biểu đồ đã tồn tại, hủy nó
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        // Tạo biểu đồ mới
        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' xu';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString() + ' xu';
                            }
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>
@endpush