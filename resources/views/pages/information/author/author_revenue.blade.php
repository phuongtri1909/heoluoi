@extends('layouts.information')

@section('info_title', 'Doanh thu tác giả')
@section('info_description', 'Theo dõi doanh thu của bạn trên ' . request()->getHost())
@section('info_keyword', 'doanh thu, tác giả, xu, truyện, ' . request()->getHost())
@section('info_section_title', 'Doanh thu tác giả')
@section('info_section_desc', 'Theo dõi doanh thu tháng ' . date('m/Y') . ' và lịch sử giao dịch')

@push('info_styles')
<style>
    .revenue-card {
        border-radius: 12px;
        padding: 25px;
        height: 170px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 6px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background-color: white;
    }
    
    .revenue-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    
    .revenue-card::before {
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
    
    .revenue-card:hover::before {
        transform: scale(6);
        opacity: 0.15;
    }
    
    .revenue-card-title {
        color: #333;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 0.5px;
        position: relative;
        z-index: 1;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    
    .revenue-card-value {
        color: #333;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
        margin: 12px 0;
    }
    
    .revenue-card-value i {
        margin-right: 15px;
        font-size: 24px;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    
    .card-total {
        background: linear-gradient(135deg, #fff, #fff);
        border: 1px solid #20b2aa;
        box-shadow: 0 6px 15px rgba(32, 178, 170, 0.1);
    }
    
    .card-chapter {
        background: linear-gradient(135deg, #fff, #fff);
        border: 1px solid #7bc5ae;
        box-shadow: 0 6px 15px rgba(123, 197, 174, 0.1);
    }
    
    .card-story {
        background: linear-gradient(135deg, #fff, #fff);
        border: 1px solid #e9b384;
        box-shadow: 0 6px 15px rgba(233, 179, 132, 0.1);
    }
    
    .card-comparison {
        background: linear-gradient(135deg, #fff, #fff);
        border: 1px solid #64b5f6;
        box-shadow: 0 6px 15px rgba(100, 181, 246, 0.1);
    }
    
    .card-total .revenue-card-value i {
        background-color: rgba(32, 178, 170, 0.1);
        color: #20b2aa;
    }
    
    .card-chapter .revenue-card-value i {
        background-color: rgba(123, 197, 174, 0.1);
        color: #7bc5ae;
    }
    
    .card-story .revenue-card-value i {
        background-color: rgba(233, 179, 132, 0.1);
        color: #e9b384;
    }
    
    .card-comparison .revenue-card-value i {
        background-color: rgba(100, 181, 246, 0.1);
        color: #64b5f6;
    }
    
    .revenue-card-footer {
        font-size: 13px;
        color: #777;
        font-weight: 500;
        position: relative;
        z-index: 1;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        padding-top: 10px;
        margin-top: 5px;
    }
    
    .revenue-filter {
        background-color: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        margin-bottom: 35px;
        transition: all 0.3s ease;
    }
    
    .revenue-filter:hover {
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
    }
    
    .chart-container {
        background-color: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        margin-top: 25px;
        position: relative;
        height: 450px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .chart-container:hover {
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
    }
    
    .chart-loading {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.85);
        z-index: 10;
        border-radius: 16px;
    }
    
    .transaction-history {
        background-color: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        margin-top: 35px;
        transition: all 0.3s ease;
    }
    
    .transaction-history:hover {
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
    }
    
    .transaction-item {
        padding: 15px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .transaction-item:hover {
        background-color: rgba(0,0,0,0.02);
        border-radius: 8px;
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .transaction-item:last-child {
        border-bottom: none;
    }
    
    .transaction-date {
        font-size: 12px;
        color: #777;
    }
    
    .transaction-amount {
        font-weight: 700;
        color: #20b2aa;
    }
    
    .transaction-title {
        font-weight: 600;
    }
    
    .transaction-type {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .type-chapter {
        background-color: rgba(123, 197, 174, 0.2);
        color: #7bc5ae;
    }
    
    .type-story {
        background-color: rgba(233, 179, 132, 0.2);
        color: #e9b384;
    }
    
    .filter-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        border-color: rgba(0,0,0,0.1);
        box-shadow: none;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        border-color: #57c5b6;
        box-shadow: 0 0 0 0.25rem rgba(32, 178, 170, 0.25);
    }
    
    .text-success, .text-success i {
        color: #2ecc71 !important;
    }
    
    .text-danger, .text-danger i {
        color: #e74c3c !important;
    }
    
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 18px 25px;
    }
    
    .card-header h5 {
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 600;
        color: #555;
        border-bottom-width: 1px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table a {
        color: #20b2aa;
        transition: all 0.3s ease;
    }
    
    .table a:hover {
        color: #57c5b6;
    }
    
    @media (max-width: 767px) {
        .revenue-card {
            height: auto;
            min-height: 150px;
        }
    }
    
    .card-comparison .revenue-card-footer .text-success {
        background-color: rgba(46, 204, 113, 0.1);
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    
    .card-comparison .revenue-card-footer .text-danger {
        background-color: rgba(231, 76, 60, 0.1);
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    
    /* New styles for transaction history */
    .badge-chapter {
        background-color: rgba(123, 197, 174, 0.15);
        color: #7bc5ae;
        border: 1px solid rgba(123, 197, 174, 0.3);
        font-weight: 500;
    }
    
    .badge-story {
        background-color: rgba(233, 179, 132, 0.15);
        color: #e9b384;
        border: 1px solid rgba(233, 179, 132, 0.3);
        font-weight: 500;
    }
    
    .transaction-row {
        transition: all 0.3s ease;
    }
    
    .transaction-row:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .transaction-amount {
        font-weight: 700;
        color: #20b2aa;
        text-align: right;
    }
    
    .transaction-user {
        display: flex;
        align-items: center;
    }
    
    .transaction-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        color: #666;
        font-weight: 600;
        font-size: 14px;
    }
    
    .transaction-date {
        display: flex;
        flex-direction: column;
    }
    
    .transaction-date-day {
        font-weight: 600;
        color: #333;
    }
    
    .transaction-date-time {
        font-size: 12px;
        color: #888;
    }
    
    .load-more-btn {
        border-radius: 50px;
        padding: 8px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('info_content')
<div class="container">
    <div class="row">
        <!-- Tổng doanh thu -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="revenue-card card-total">
                <div class="revenue-card-title">
                    Tổng doanh thu
                </div>
                <div class="revenue-card-value">
                    <i class="fas fa-chart-line"></i>
                    <span class="fw-bold text-warning" id="total-revenue">{{ number_format($grandTotal) }} xu</span>
                </div>
                <div class="revenue-card-footer">
                    Tổng doanh thu từ trước đến nay
                </div>
            </div>
        </div>
        
        <!-- Doanh thu tháng trước -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="revenue-card card-comparison">
                <div class="revenue-card-title">
                    So sánh với tháng trước
                </div>
                <div class="revenue-card-value">
                    <i class="fas fa-balance-scale"></i>
                    <span class="fw-bold text-warning">{{ number_format($lastMonthRevenue ?? 0) }} xu</span>
                </div>
                <div class="revenue-card-footer">
                    @if(isset($revenueChangePercent) && $revenueChangePercent != 0)
                        @if($revenueIncreased)
                            <span class="text-success">
                                <i class="fas fa-arrow-up"></i> Tăng {{ $revenueChangePercent }}%
                            </span>
                        @else
                            <span class="text-danger">
                                <i class="fas fa-arrow-down"></i> Giảm {{ abs($revenueChangePercent) }}%
                            </span>
                        @endif
                    @else
                        Không thay đổi
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Doanh thu từ chương -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="revenue-card card-chapter">
                <div class="revenue-card-title">
                    Doanh thu từ chương
                </div>
                <div class="revenue-card-value">
                    <i class="fas fa-book-open"></i>
                    <span class="fw-bold text-warning" id="chapter-revenue">Đang tải...</span>
                </div>
                <div class="revenue-card-footer">
                    Doanh thu từ việc bán chương trong kỳ
                </div>
            </div>
        </div>
        
        <!-- Doanh thu từ trọn bộ -->
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="revenue-card card-story">
                <div class="revenue-card-title">
                    Doanh thu từ trọn bộ
                </div>
                <div class="revenue-card-value">
                    <i class="fas fa-book"></i>
                    <span class="fw-bold text-warning" id="story-revenue">Đang tải...</span>
                </div>
                <div class="revenue-card-footer">
                    Doanh thu từ việc bán trọn bộ trong kỳ
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bộ lọc -->
    <div class="revenue-filter">
        <div class="row">
            <div class="col-md-4">
                <div class="filter-label">Chọn năm</div>
                <select id="year-filter" class="form-select">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="filter-label">Chọn tháng</div>
                <select id="month-filter" class="form-select">
                    <option value="">Tất cả các tháng</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>Tháng {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <div class="filter-label">Thống kê theo</div>
                <select id="chart-type" class="form-select">
                    <option value="bar">Biểu đồ cột</option>
                    <option value="line">Biểu đồ đường</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Biểu đồ -->
    <div class="chart-container">
        <div class="chart-loading" id="chart-loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
        </div>
        <canvas id="revenue-chart"></canvas>
    </div>
    
    <!-- Lịch sử giao dịch -->
    <div class="card mt-4 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lịch sử giao dịch gần đây</h5>
            <span class="badge bg-light text-dark rounded-pill">
                <i class="fas fa-calendar-alt me-1"></i> 
                <span id="transaction-period">{{ date('m/Y') }}</span>
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 18%"><i class="far fa-clock me-1 text-muted"></i> Thời gian</th>
                            <th style="width: 20%"><i class="far fa-user me-1 text-muted"></i> Người mua</th>
                            <th><i class="far fa-file-alt me-1 text-muted"></i> Nội dung</th>
                            <th style="width: 15%" class="text-end"><i class="fas fa-coins me-1 text-muted"></i> Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-list">
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="mt-2 mb-0">Đang tải dữ liệu giao dịch...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="transaction-pagination" class="d-flex justify-content-center mt-3" style="display: none !important;">
                <button id="load-more-transactions" class="btn btn-outline-primary load-more-btn">
                    <i class="fas fa-sync-alt me-2"></i> Xem thêm giao dịch
                </button>
            </div>
        </div>
    </div>
    
    <!-- Bảng thống kê tổng doanh thu -->
    <div class="card mt-4 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Thống kê tổng doanh thu theo truyện</h5>
            <span class="badge bg-light text-dark rounded-pill">
                <i class="fas fa-chart-bar me-1"></i> 
                Tất cả thời gian
            </span>
        </div>
        <div class="card-body">
            @if(count($storyRevenueStats) > 0)
                <div class="table-responsive">
                    <table id="story-stats-table" class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 35%">Tên truyện</th>
                                <th style="width: 20%" class="text-end">Doanh thu chương</th>
                                <th style="width: 20%" class="text-end">Doanh thu combo</th>
                                <th style="width: 20%" class="text-end">Tổng doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($storyRevenueStats as $index => $story)
                                <tr class="transaction-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none fw-semibold color-3">
                                            {{ $story->title }}
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-primary">{{ number_format($story->chapter_revenue) }} xu</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-warning">{{ number_format($story->story_revenue) }} xu</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success">{{ number_format($story->total_revenue) }} xu</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($storyRevenueStats->hasPages() && $storyRevenueStats->hasMorePages())
                    <div id="story-stats-pagination" class="d-flex justify-content-center mt-3">
                        <button id="load-more-story-stats" class="btn btn-outline-primary load-more-btn">
                            <i class="fas fa-sync-alt me-2"></i> Xem thêm truyện
                        </button>
                    </div>
                @else
                    <div id="story-stats-pagination" class="d-flex justify-content-center mt-3" style="display: none !important;">
                        <button id="load-more-story-stats" class="btn btn-outline-primary load-more-btn">
                            <i class="fas fa-sync-alt me-2"></i> Xem thêm truyện
                        </button>
                    </div>
                @endif
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <p>Chưa có dữ liệu doanh thu</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Nội dung bán chạy -->
    <div class="row mt-4">
        <!-- Truyện bán chạy -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Truyện bán chạy nhất</h5>
                </div>
                <div class="card-body">
                    @if(count($topStories) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên truyện</th>
                                        <th>Lượt mua</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody id="top-stories-list">
                                    @foreach($topStories as $index => $story)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none color-3">
                                                    {{ $story->title }}
                                                </a>
                                            </td>
                                            <td>{{ $story->purchase_count }}</td>
                                            <td>{{ number_format($story->total_revenue) }} xu</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="top-stories-pagination" class="d-flex justify-content-center mt-3" style="display: none !important;">
                            <button id="load-more-stories" class="btn btn-outline-primary btn-sm load-more-btn">
                                <i class="fas fa-sync-alt me-2"></i> Xem thêm truyện
                            </button>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <p>Chưa có dữ liệu bán truyện trọn bộ</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Chương bán chạy -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chương bán chạy nhất</h5>
                </div>
                <div class="card-body">
                    @if(count($topChapters) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên chương</th>
                                        <th>Lượt mua</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody id="top-chapters-list">
                                    @foreach($topChapters as $index => $chapter)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('chapter', [$chapter->story_slug, $chapter->slug]) }}" class="text-decoration-none color-3">
                                                    {{ $chapter->title }}
                                                </a>
                                                <div class="small text-muted">
                                                    {{ $chapter->story_title }}
                                                </div>
                                            </td>
                                            <td>{{ $chapter->purchase_count }}</td>
                                            <td>{{ number_format($chapter->total_revenue) }} xu</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="top-chapters-pagination" class="d-flex justify-content-center mt-3" style="display: none !important;">
                            <button id="load-more-chapters" class="btn btn-outline-primary btn-sm load-more-btn">
                                <i class="fas fa-sync-alt me-2"></i> Xem thêm chương
                            </button>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-book-open fa-2x mb-2"></i>
                            <p>Chưa có dữ liệu bán chương</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('info_scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let revenueChart = null;
    let currentTransactionPage = 1;
    let currentStoriesPage = 1;
    let currentChaptersPage = 1;
    let currentStoryStatsPage = 1;
    let hasMoreTransactions = false;
    let hasMoreStories = false;
    let hasMoreChapters = false;
    let hasMoreStoryStats = false;
    
    // Hiệu ứng đếm số
    function animateNumber(element, targetValue) {
        // Đảm bảo targetValue là số nguyên
        targetValue = parseInt(targetValue) || 0;
        
        // Reset lại giá trị trước khi bắt đầu animation
        element.textContent = '0 xu';
        
        const duration = 1500;
        const frameDuration = 1000 / 60;
        const totalFrames = Math.round(duration / frameDuration);
        let frame = 0;
        const initialValue = 0;
        const valueIncrement = (targetValue - initialValue) / totalFrames;
        
        const counter = setInterval(() => {
            frame++;
            const newValue = Math.floor(initialValue + (valueIncrement * frame));
            element.textContent = newValue.toLocaleString() + ' xu';
            
            if (frame === totalFrames) {
                clearInterval(counter);
                element.textContent = targetValue.toLocaleString() + ' xu';
            }
        }, frameDuration);
    }
    
    // Hiệu ứng hover cho card
    function initCardHoverEffects() {
        const cards = document.querySelectorAll('.revenue-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                // Thêm hiệu ứng khi hover
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                // Loại bỏ hiệu ứng khi rời đi
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    // Hàm tải dữ liệu doanh thu
    function loadRevenueData() {
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        const chartType = document.getElementById('chart-type').value;
        
        // Hiển thị loading
        const chartContainer = document.querySelector('.chart-container');
        chartContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải dữ liệu doanh thu...</p>
            </div>
        `;
        
        // Gọi API để lấy dữ liệu
        fetch(`{{ route('user.author.revenue.data') }}?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                // Cập nhật biểu đồ
                createOrUpdateChart(data, chartType);
                
                // Cập nhật thông tin tổng hợp
                updateRevenueSummary(data);
                
                // Tải lại các dữ liệu khác
                loadTransactionHistory(true);
                loadTopStories(true);
                loadTopChapters(true);
            })
            .catch(error => {
                console.error('Error fetching revenue data:', error);
                chartContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <p>Có lỗi xảy ra khi tải dữ liệu doanh thu</p>
                    </div>
                `;
            });
    }
    
    // Hàm tạo hoặc cập nhật biểu đồ
    function createOrUpdateChart(data, chartType) {
        const chartContainer = document.querySelector('.chart-container');
        chartContainer.innerHTML = '<canvas id="revenue-chart"></canvas>';
        
        const ctx = document.getElementById('revenue-chart').getContext('2d');
        
        // Kiểm tra xem có dữ liệu không
        if (!data.datasets || !data.labels || data.datasets.length === 0) {
            chartContainer.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-bar fa-3x text-muted mb-3"></i><p>Không có dữ liệu doanh thu trong khoảng thời gian này</p></div>';
            return;
        }
        
        // Tạo biểu đồ mới
        const revenueChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                },
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
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });
    }
    
    // Hàm cập nhật thông tin tổng hợp doanh thu
    function updateRevenueSummary(data) {
        // Sử dụng dữ liệu tổng hợp từ API thay vì tính toán từ datasets
        if (data.summary) {
            const totalChapterRevenue = data.summary.totalChapterRevenue;
            const totalStoryRevenue = data.summary.totalStoryRevenue;
            
            // Cập nhật UI với hiệu ứng đếm số
            const chapterRevenueElement = document.getElementById('chapter-revenue');
            const storyRevenueElement = document.getElementById('story-revenue');
            
            // Hiệu ứng đếm số với giá trị đúng
            animateNumber(chapterRevenueElement, totalChapterRevenue);
            animateNumber(storyRevenueElement, totalStoryRevenue);
        } else {
            // Fallback nếu API không trả về dữ liệu summary
            let totalChapterRevenue = 0;
            let totalStoryRevenue = 0;
            
            // Tính tổng doanh thu từ chương
            if (data.datasets && data.datasets.length > 0) {
                totalChapterRevenue = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                if (data.datasets.length > 1) {
                    totalStoryRevenue = data.datasets[1].data.reduce((sum, value) => sum + value, 0);
                }
            }
            
            // Cập nhật UI với hiệu ứng đếm số
            const chapterRevenueElement = document.getElementById('chapter-revenue');
            const storyRevenueElement = document.getElementById('story-revenue');
            
            // Hiệu ứng đếm số với giá trị đúng
            animateNumber(chapterRevenueElement, totalChapterRevenue);
            animateNumber(storyRevenueElement, totalStoryRevenue);
        }
    }
    
    // Hàm tải lịch sử giao dịch
    function loadTransactionHistory(reset = false) {
        if (reset) {
            currentTransactionPage = 1;
            document.getElementById('transaction-list').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="mt-2 mb-0">Đang tải dữ liệu giao dịch...</p>
                    </td>
                </tr>
            `;
            document.getElementById('transaction-pagination').style.display = 'none';
        }
        
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        
        // Cập nhật hiển thị kỳ giao dịch
        document.getElementById('transaction-period').textContent = month ? `${month}/${year}` : `Năm ${year}`;
        
        fetch(`{{ route('user.author.revenue.transactions') }}?year=${year}&month=${month}&page=${currentTransactionPage}`)
            .then(response => response.json())
            .then(data => {
                hasMoreTransactions = data.current_page < data.last_page;
                
                document.getElementById('transaction-pagination').style.display = hasMoreTransactions ? 'flex' : 'none';
                
                if (currentTransactionPage === 1) {
                    document.getElementById('transaction-list').innerHTML = '';
                }
                
                if (data.data.length === 0 && currentTransactionPage === 1) {
                    document.getElementById('transaction-list').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-receipt fa-3x mb-3 text-muted"></i>
                                <p class="mb-0">Không có giao dịch nào trong khoảng thời gian này</p>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                const transactionList = document.getElementById('transaction-list');
                
                data.data.forEach((transaction, index) => {
                    const row = document.createElement('tr');
                    row.className = 'transaction-row';
                    
                    // Format date
                    const date = new Date(transaction.created_at);
                    const day = date.toLocaleDateString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    const time = date.toLocaleTimeString('vi-VN', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Get user initials for avatar
                    const userInitials = transaction.user_name
                        .split(' ')
                        .map(name => name[0])
                        .join('')
                        .substring(0, 2)
                        .toUpperCase();
                    
                    // Determine content based on transaction type
                    let contentHtml;
                    let badgeClass = transaction.type === 'chapter' ? 'badge-chapter' : 'badge-story';
                    let badgeText = transaction.type === 'chapter' ? 'Chương' : 'Trọn bộ';
                    let badgeIcon = transaction.type === 'chapter' ? 'fa-book-open' : 'fa-book';
                    
                    if (transaction.type === 'chapter') {
                        contentHtml = `
                            <span class="badge ${badgeClass} mb-2 text-dark">
                                <i class="fas ${badgeIcon} me-1"></i> ${badgeText}
                            </span>
                            <div>
                                <a href="{{ url('/story') }}/${transaction.story_slug}/${transaction.chapter_slug}" class="text-decoration-none fw-semibold color-3">
                                    Chương ${transaction.chapter_number}: ${transaction.chapter_title}
                                </a>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-bookmark me-1"></i> ${transaction.story_title}
                                </div>
                            </div>
                        `;
                    } else {
                        contentHtml = `
                            <span class="badge ${badgeClass} mb-2 text-dark">
                                <i class="fas ${badgeIcon} me-1"></i> ${badgeText}
                            </span>
                            <div>
                                <a href="{{ url('/story') }}/${transaction.story_slug}" class="text-decoration-none fw-semibold color-3">
                                    ${transaction.story_title}
                                </a>
                            </div>
                        `;
                    }
                    
                    row.innerHTML = `
                        <td>
                            <div class="transaction-date">
                                <span class="transaction-date-day">${day}</span>
                                <span class="transaction-date-time">${time}</span>
                            </div>
                        </td>
                        <td>
                            <div class="transaction-user">
                                <div class="transaction-user-avatar">${userInitials}</div>
                                <span>${transaction.user_name}</span>
                            </div>
                        </td>
                        <td>${contentHtml}</td>
                        <td class="transaction-amount text-shadow-custom">${transaction.amount_received.toLocaleString()} xu</td>
                    `;
                    
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(10px)';
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    
                    transactionList.appendChild(row);
                    
                    setTimeout(() => {
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                    }, 50 * index);
                });
            })
            .catch(error => {
                console.error('Error fetching transaction history:', error);
                document.getElementById('transaction-list').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-exclamation-circle fa-3x mb-3 text-danger"></i>
                            <p class="mb-0">Có lỗi xảy ra khi tải lịch sử giao dịch</p>
                            <button class="btn btn-sm btn-outline-danger mt-3" onclick="loadTransactionHistory(true)">
                                <i class="fas fa-redo me-1"></i> Thử lại
                            </button>
                        </td>
                    </tr>
                `;
            });
    }
    
    // Hàm tải danh sách truyện bán chạy nhất
    function loadTopStories(reset = false) {
        if (reset) {
            currentStoriesPage = 1;
            document.getElementById('top-stories-list').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </td>
                </tr>
            `;
            document.getElementById('top-stories-pagination').style.display = 'none';
        }
        
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        

        fetch(`{{ route('user.author.revenue.top-stories') }}?year=${year}&month=${month}&page=${currentStoriesPage}`)
            .then(response => response.json())
            .then(data => {
                hasMoreStories = data.current_page < data.last_page;
                
                document.getElementById('top-stories-pagination').style.display = hasMoreStories ? 'flex' : 'none';
                
                if (currentStoriesPage === 1) {
                    document.getElementById('top-stories-list').innerHTML = '';
                }
                
                if (data.data.length === 0 && currentStoriesPage === 1) {
                    document.getElementById('top-stories-list').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-book fa-2x mb-3"></i>
                                <p>Không có dữ liệu bán truyện trong khoảng thời gian này</p>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                const storiesList = document.getElementById('top-stories-list');
                const startIndex = (currentStoriesPage - 1) * data.per_page;
                
                data.data.forEach((story, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${startIndex + index + 1}</td>
                        <td>
                            <a href="{{ url('/story') }}/${story.slug}" class="text-decoration-none color-3">
                                ${story.title}
                            </a>
                        </td>
                        <td>${story.purchase_count}</td>
                        <td>${story.total_revenue.toLocaleString()} xu</td>
                    `;
                    
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s ease';
                    
                    storiesList.appendChild(row);
                    
                    setTimeout(() => {
                        row.style.opacity = '1';
                    }, 50 * index);
                });
            })
            .catch(error => {
                console.error('Error fetching top stories:', error);
                document.getElementById('top-stories-list').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p>Có lỗi xảy ra khi tải dữ liệu truyện bán chạy</p>
                        </td>
                    </tr>
                `;
            });
    }
    
    // Hàm tải danh sách chương bán chạy nhất
    function loadTopChapters(reset = false) {
        if (reset) {
            currentChaptersPage = 1;
            document.getElementById('top-chapters-list').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </td>
                </tr>
            `;
            document.getElementById('top-chapters-pagination').style.display = 'none';
        }
        
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        
        fetch(`{{ route('user.author.revenue.top-chapters') }}?year=${year}&month=${month}&page=${currentChaptersPage}`)
            .then(response => response.json())
            .then(data => {
                hasMoreChapters = data.current_page < data.last_page;
                
                document.getElementById('top-chapters-pagination').style.display = hasMoreChapters ? 'flex' : 'none';
                
                if (currentChaptersPage === 1) {
                    document.getElementById('top-chapters-list').innerHTML = '';
                }
                
                if (data.data.length === 0 && currentChaptersPage === 1) {
                    document.getElementById('top-chapters-list').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-book-open fa-2x mb-3"></i>
                                <p>Không có dữ liệu bán chương trong khoảng thời gian này</p>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                const chaptersList = document.getElementById('top-chapters-list');
                const startIndex = (currentChaptersPage - 1) * data.per_page;
                
                data.data.forEach((chapter, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${startIndex + index + 1}</td>
                        <td>
                            <a href="{{ url('/story') }}/${chapter.story_slug}/${chapter.slug}" class="text-decoration-none color-3">
                                ${chapter.title}
                            </a>
                            <div class="small text-muted">
                                ${chapter.story_title}
                            </div>
                        </td>
                        <td>${chapter.purchase_count}</td>
                        <td>${chapter.total_revenue.toLocaleString()} xu</td>
                    `;
                    
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s ease';
                    
                    chaptersList.appendChild(row);
                    
                    setTimeout(() => {
                        row.style.opacity = '1';
                    }, 50 * index);
                });
            })
            .catch(error => {
                console.error('Error fetching top chapters:', error);
                document.getElementById('top-chapters-list').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p>Có lỗi xảy ra khi tải dữ liệu chương bán chạy</p>
                        </td>
                    </tr>
                `;
            });
    }
    
    // Hàm tải thêm thống kê doanh thu theo truyện
    function loadMoreStoryStats() {
        currentStoryStatsPage++;
        
        fetch(`{{ route('user.author.revenue.story-stats') }}?page=${currentStoryStatsPage}`)
            .then(response => response.json())
            .then(data => {
                hasMoreStoryStats = data.current_page < data.last_page;
                
                document.getElementById('story-stats-pagination').style.display = hasMoreStoryStats ? 'flex' : 'none';
                
                if (data.data.length === 0) {
                    return;
                }
                
                const tbody = document.querySelector('#story-stats-table tbody');
                const startIndex = (currentStoryStatsPage - 1) * data.per_page;
                
                data.data.forEach((story, index) => {
                    const row = document.createElement('tr');
                    row.className = 'transaction-row';
                    row.innerHTML = `
                        <td>${startIndex + index + 1}</td>
                        <td>
                            <a href="{{ url('/story') }}/${story.slug}" class="text-decoration-none fw-semibold color-3">
                                ${story.title}
                            </a>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-primary">${parseInt(story.chapter_revenue).toLocaleString()} xu</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-warning">${parseInt(story.story_revenue).toLocaleString()} xu</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success">${parseInt(story.total_revenue).toLocaleString()} xu</span>
                        </td>
                    `;
                    
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s ease';
                    
                    tbody.appendChild(row);
                    
                    setTimeout(() => {
                        row.style.opacity = '1';
                    }, 50 * index);
                });
            })
            .catch(error => {
                console.error('Error fetching story stats:', error);
                currentStoryStatsPage--; // Rollback page on error
            });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Khởi tạo hiệu ứng hover cho các thẻ
        initCardHoverEffects();
        
        // Tải dữ liệu doanh thu ban đầu
        loadRevenueData();
        
        // Khởi tạo hiệu ứng đếm số cho tổng doanh thu
        const totalRevenueElement = document.getElementById('total-revenue');
        const totalRevenueText = totalRevenueElement.textContent.replace(/[^\d]/g, '');
        const totalRevenueValue = parseInt(totalRevenueText) || 0;
        
        animateNumber(totalRevenueElement, totalRevenueValue);
        
        // Thêm sự kiện cho các bộ lọc và nút tải thêm
        document.getElementById('year-filter').addEventListener('change', loadRevenueData);
        document.getElementById('month-filter').addEventListener('change', loadRevenueData);
        document.getElementById('chart-type').addEventListener('change', loadRevenueData);
        
        document.getElementById('load-more-transactions').addEventListener('click', function() {
            currentTransactionPage++;
            loadTransactionHistory();
        });
        
        document.getElementById('load-more-stories').addEventListener('click', function() {
            currentStoriesPage++;
            loadTopStories();
        });

        document.getElementById('load-more-chapters').addEventListener('click', function() {
            currentChaptersPage++;
            loadTopChapters();
        });

        document.getElementById('load-more-story-stats').addEventListener('click', function() {
            loadMoreStoryStats();
        });
    });
</script>
@endpush 