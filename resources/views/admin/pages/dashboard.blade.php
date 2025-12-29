@extends('admin.layouts.app')

@push('styles-admin')
<style>
    .stats-card {
        transition: transform 0.2s ease-in-out;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    
    /* Alternating background colors for stats cards */
    .row .col-xl-3:nth-child(1) .stats-card,
    .row .col-xl-3:nth-child(3) .stats-card,
    .row .col-xl-3:nth-child(5) .stats-card,
    .row .col-xl-3:nth-child(7) .stats-card {
        background: white !important;
        color: #333 !important;
    }
    
    .row .col-xl-3:nth-child(2) .stats-card,
    .row .col-xl-3:nth-child(4) .stats-card,
    .row .col-xl-3:nth-child(6) .stats-card,
    .row .col-xl-3:nth-child(8) .stats-card {
        background: #f8f9fa !important;
        color: #333 !important;
    }
    
    /* Override gradient backgrounds */
    .stats-card.bg-gradient-primary,
    .stats-card.bg-gradient-success,
    .stats-card.bg-gradient-info,
    .stats-card.bg-gradient-warning,
    .stats-card.bg-gradient-secondary,
    .stats-card.bg-gradient-danger,
    .stats-card.bg-gradient-dark {
        background: inherit !important;
        color: inherit !important;
    }
    
    /* Icon colors for alternating backgrounds */
    .row .col-xl-3:nth-child(1) .stats-card .icon-shape,
    .row .col-xl-3:nth-child(3) .stats-card .icon-shape,
    .row .col-xl-3:nth-child(5) .stats-card .icon-shape,
    .row .col-xl-3:nth-child(7) .stats-card .icon-shape {
        background: #e3f2fd !important;
    }
    
    .row .col-xl-3:nth-child(2) .stats-card .icon-shape,
    .row .col-xl-3:nth-child(4) .stats-card .icon-shape,
    .row .col-xl-3:nth-child(6) .stats-card .icon-shape,
    .row .col-xl-3:nth-child(8) .stats-card .icon-shape {
        background: #f3e5f5 !important;
    }
    
    .row .col-xl-3:nth-child(1) .stats-card .icon i,
    .row .col-xl-3:nth-child(3) .stats-card .icon i,
    .row .col-xl-3:nth-child(5) .stats-card .icon i,
    .row .col-xl-3:nth-child(7) .stats-card .icon i {
        color: #1976d2 !important;
    }
    
    .row .col-xl-3:nth-child(2) .stats-card .icon i,
    .row .col-xl-3:nth-child(4) .stats-card .icon i,
    .row .col-xl-3:nth-child(6) .stats-card .icon i,
    .row .col-xl-3:nth-child(8) .stats-card .icon i {
        color: #7b1fa2 !important;
    }
    
    .chart-container {
        height: 300px;
    }
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@endpush

@section('content-auth')
<div class="d-flex flex-column mb-4">
    <h2 class="fw-bold mb-0">Dashboard Thống Kê</h2>
    
    <!-- Date Filter -->
    <div class="d-flex gap-2 mt-2">
        <select id="yearSelect" class="form-select form-select-sm" style="width: 100px;">
            @for($i = date('Y'); $i >= 2020; $i--)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        
        <select id="monthSelect" class="form-select form-select-sm" style="width: 120px;">
            <option value="" {{ $month == null ? 'selected' : '' }}>Cả năm</option>
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                    Tháng {{ $i }}
                </option>
            @endfor
        </select>
        
        <button id="refreshBtn" class="btn bg-gradient-primary btn-sm mb-0 px-3">
            <i class="fas fa-sync-alt"></i> Làm mới
        </button>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Basic Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card stats-card bg-gradient-primary text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Người dùng mới</p>
                            <h5 class="font-weight-bolder mb-0" id="newUsers">{{ number_format($basicStats['new_users']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card stats-card bg-gradient-success text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Truyện mới</p>
                            <h5 class="font-weight-bolder mb-0" id="newStories">{{ number_format($basicStats['new_stories']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-book text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card stats-card bg-gradient-info text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Chương mới</p>
                            <h5 class="font-weight-bolder mb-0" id="newChapters">{{ number_format($basicStats['new_chapters']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-file-alt text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-sm-6">
        <div class="card stats-card bg-gradient-warning text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Bình luận mới</p>
                            <h5 class="font-weight-bolder mb-0" id="newComments">{{ number_format($basicStats['new_comments']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-comments text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Đã đóng - Bỏ mục Tổng Cám người dùng, Cám đã nạp và Cám nhiệm vụ --}}
{{-- @if($isAdminMain)
<!-- Coin Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card stats-card bg-gradient-dark text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Tổng cám người dùng</p>
                            <h5 class="font-weight-bolder mb-0" id="totalUserCoins">{{ number_format($coinStats['total_user_coins']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-coins text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card stats-card bg-gradient-success text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Cám đã nạp</p>
                            <h5 class="font-weight-bolder mb-0" id="totalDeposited">{{ number_format($coinStats['total_deposited']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-credit-card text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="col-xl-3 col-sm-6">
        <div class="card stats-card bg-gradient-info text-white">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Cám nhiệm vụ</p>
                            <h5 class="font-weight-bolder mb-0" id="totalDailyTaskCoins">{{ number_format($coinStats['total_daily_task_coins']) }}</h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-white text-center rounded-circle shadow">
                            <i class="fas fa-trophy text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif --}}

<!-- Detailed Statistics Tables -->
<div class="row">
    <!-- Story Views -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Top Truyện Theo Lượt Xem</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Truyện</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tổng Views</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Chương</th>
                            </tr>
                        </thead>
                        <tbody id="storyViewsTable">
                            @foreach($storyViews as $story)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ Str::limit($story->title, 30) }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $story->author_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($story->total_views) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ $story->chapter_count }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @if($isAdminMain)
    <!-- Revenue Statistics -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Top Doanh Thu</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Truyện</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loại</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Doanh Thu</th>
                            </tr>
                        </thead>
                        <tbody id="revenueTable">
                            @foreach($revenueStats as $revenue)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ Str::limit($revenue->title, 25) }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $revenue->author_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="badge badge-sm bg-{{ $revenue->type == 'story' ? 'success' : 'info' }}">
                                        {{ $revenue->type == 'story' ? 'Truyện' : 'Chương' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($revenue->total_revenue) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Đã đóng - Thống Kê Nạp Cám --}}
{{-- @if($isAdminMain)
<!-- Deposit Statistics -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Thống Kê Nạp Cám</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Loại</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Số Lượng</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tổng Cám</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Trung Bình</th>
                            </tr>
                        </thead>
                        <tbody id="depositTable">
                            @foreach($depositStats as $deposit)
                            <tr>
                                <td>
                                    <span class="badge badge-sm bg-{{ $deposit->type == 'bank' ? 'primary' : ($deposit->type == 'paypal' ? 'success' : 'info') }}">
                                        {{ ucfirst($deposit->type) }}
                                    </span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($deposit->count) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($deposit->total_amount) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($deposit->avg_amount) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif --}}

{{-- Đã đóng - Nhiệm Vụ Hàng Ngày --}}
{{-- <!-- Daily Tasks -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Nhiệm Vụ Hàng Ngày</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nhiệm Vụ</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Hoàn Thành</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tổng Cám</th>
                            </tr>
                        </thead>
                        <tbody id="dailyTaskTable">
                            @foreach($dailyTaskStats as $task)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $task->name }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ number_format($task->avg_coins_per_task) }} cám</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($task->completion_count) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($task->total_coins_distributed) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    @if($isAdminMain)
    <!-- Manual Coin Transactions -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Giao Dịch Cám Thủ Công</h6>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Loại</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Số Giao Dịch</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tổng Cám</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Trung Bình</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                            </tr>
                        </thead>
                        <tbody id="manualCoinTable">
                            @foreach($manualCoinStats as $transaction)
                            <tr>
                                <td>
                                    <span class="badge badge-sm bg-{{ $transaction->type == 'add' ? 'success' : 'danger' }}">
                                        {{ $transaction->type == 'add' ? 'Cộng' : 'Trừ' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($transaction->transaction_count) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($transaction->total_amount) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ number_format($transaction->avg_amount) }}</span>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-xs font-weight-bold">{{ $transaction->admin_name ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> --}}
@endsection

@push('scripts-admin')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('yearSelect');
    const monthSelect = document.getElementById('monthSelect');
    const refreshBtn = document.getElementById('refreshBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Function to show loading
    function showLoading() {
        loadingOverlay.classList.remove('d-none');
        document.body.classList.add('loading');
    }
    
    // Function to hide loading
    function hideLoading() {
        loadingOverlay.classList.add('d-none');
        document.body.classList.remove('loading');
    }
    
    // Function to update URL parameters
    function updateURL() {
        const year = yearSelect.value;
        const month = monthSelect.value;
        const url = new URL(window.location);
        
        url.searchParams.set('year', year);
        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }
        
        window.history.pushState({}, '', url);
    }
    
    // Function to load dashboard data via AJAX
    function loadDashboardData() {
        showLoading();
        
        const year = yearSelect.value;
        const month = monthSelect.value;
        
        const params = new URLSearchParams({
            year: year
        });
        
        if (month) {
            params.append('month', month);
        }
        
        fetch(`/admin/dashboard/data?${params}`)
            .then(response => response.json())
            .then(data => {
                updateDashboardData(data);
                hideLoading();
            })
            .catch(error => {
                console.error('Error loading dashboard data:', error);
                hideLoading();
                showToast('Có lỗi xảy ra khi tải dữ liệu', 'error');
            });
    }
    
    // Function to update dashboard data
    function updateDashboardData(data) {
        // Update basic stats
        document.getElementById('newUsers').textContent = formatNumber(data.basicStats.new_users);
        document.getElementById('newStories').textContent = formatNumber(data.basicStats.new_stories);
        document.getElementById('newChapters').textContent = formatNumber(data.basicStats.new_chapters);
        document.getElementById('newComments').textContent = formatNumber(data.basicStats.new_comments);
        
        // Đã đóng - Bỏ mục Tổng Cám người dùng, Cám đã nạp và Cám nhiệm vụ
        // Update coin stats (only if admin_main)
        // if (data.coinStats) {
        //     document.getElementById('totalUserCoins').textContent = formatNumber(data.coinStats.total_user_coins);
        //     document.getElementById('totalDeposited').textContent = formatNumber(data.coinStats.total_deposited);
        //     document.getElementById('totalDailyTaskCoins').textContent = formatNumber(data.coinStats.total_daily_task_coins);
        // }
        
        // Update tables
        updateTable('storyViewsTable', data.storyViews, 'story');
        // Đã đóng - Nhiệm Vụ Hàng Ngày
        // updateTable('dailyTaskTable', data.dailyTaskStats, 'task');
        
        // Update revenue tables (only if admin_main)
        if (data.revenueStats) {
            updateTable('revenueTable', data.revenueStats, 'revenue');
        }
        // Đã đóng - Thống Kê Nạp Cám
        // if (data.depositStats) {
        //     updateTable('depositTable', data.depositStats, 'deposit');
        // }
        // Đã đóng - Giao Dịch Cám Thủ Công
        // if (data.manualCoinStats) {
        //     updateTable('manualCoinTable', data.manualCoinStats, 'manual');
        // }
    }
    
    // Function to update table data
    function updateTable(tableId, data, type) {
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        data.forEach(item => {
            const row = createTableRow(item, type);
            tbody.appendChild(row);
        });
    }
    
    // Function to create table row
    function createTableRow(item, type) {
        const row = document.createElement('tr');
        
        switch(type) {
            case 'story':
                row.innerHTML = `
                    <td>
                        <div class="d-flex px-2 py-1">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm">${truncateText(item.title, 30)}</h6>
                                <p class="text-xs text-secondary mb-0">${item.author_name}</p>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.total_views)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${item.chapter_count}</span>
                    </td>
                `;
                break;
                
            case 'revenue':
                row.innerHTML = `
                    <td>
                        <div class="d-flex px-2 py-1">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm">${truncateText(item.title, 25)}</h6>
                                <p class="text-xs text-secondary mb-0">${item.author_name}</p>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-${item.type == 'story' ? 'success' : 'info'}">
                            ${item.type == 'story' ? 'Truyện' : 'Chương'}
                        </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.total_revenue)}</span>
                    </td>
                `;
                break;
                
            case 'deposit':
                row.innerHTML = `
                    <td>
                        <span class="badge badge-sm bg-${item.type == 'bank' ? 'primary' : (item.type == 'paypal' ? 'success' : 'info')}">
                            ${capitalizeFirst(item.type)}
                        </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.count)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.total_amount)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.avg_amount)}</span>
                    </td>
                `;
                break;
            case 'task':
                row.innerHTML = `
                    <td>
                        <div class="d-flex px-2 py-1">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm">${item.name}</h6>
                                <p class="text-xs text-secondary mb-0">${formatNumber(item.avg_coins_per_task)} cám</p>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.completion_count)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.total_coins_distributed)}</span>
                    </td>
                `;
                break;
                
            case 'manual':
                row.innerHTML = `
                    <td>
                        <span class="badge badge-sm bg-${item.type == 'add' ? 'success' : 'danger'}">
                            ${item.type == 'add' ? 'Cộng' : 'Trừ'}
                        </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.transaction_count)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.total_amount)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${formatNumber(item.avg_amount)}</span>
                    </td>
                    <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">${item.admin_name || 'N/A'}</span>
                    </td>
                `;
                break;
        }
        
        return row;
    }
    
    // Utility functions
    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }
    
    function truncateText(text, length) {
        return text.length > length ? text.substring(0, length) + '...' : text;
    }
    
    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    function showToast(message, type = 'success') {
        let alertClass = 'alert-success';
        let icon = '<i class="fas fa-check-circle me-2"></i>';

        if (type === 'error') {
            alertClass = 'alert-danger';
            icon = '<i class="fas fa-exclamation-circle me-2"></i>';
        }

        const toast = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
            <div class="toast show align-items-center ${alertClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${icon} ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', toast);

        setTimeout(() => {
            const toastElement = document.querySelector('.toast.show');
            if (toastElement) {
                toastElement.remove();
            }
        }, 3000);
    }
    
    // Event listeners
    yearSelect.addEventListener('change', function() {
        updateURL();
        window.location.reload();
    });
    
    monthSelect.addEventListener('change', function() {
        updateURL();
        window.location.reload();
    });
    
    refreshBtn.addEventListener('click', function() {
        loadDashboardData();
    });
});
</script>
@endpush