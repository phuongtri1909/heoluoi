<div class="sidebar-widget recent-reads rounded-4 border-5 border border-color-3 shadow-sm mt-4 mt-md-0">
    <div class="widget-header border-bottom-0">
        <div class="text-center">
            <h2 class="fs-3 text-center m-0 text-dark fw-bold title-dark font-svn-apple">BẢNG XẾP HẠNG</h2>
        </div>
        <div class="d-flex justify-content-evenly mt-3 font-svn-apple" id="hotStoriesTabs" role="tablist">
            <button class="tab-button active fs-5" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily"
                type="button" role="tab">
                NGÀY
            </button>
            <button class="tab-button fs-5" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button"
                role="tab">
                TUẦN
            </button>
            <button class="tab-button fs-5" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button"
                role="tab">
                THÁNG
            </button>
        </div>
    </div>
    <div class="widget-content px-md-4 px-2">
        <!-- Tab Content -->
        <div class="tab-content" id="hotStoriesContent">
            <!-- Daily Hot Stories -->
            @include('components.hot_story_tab', [
                'tabId' => 'daily',
                'isActive' => true,
                'stories' => $dailyTopPurchased
            ])

            <!-- Weekly Hot Stories -->
            @include('components.hot_story_tab', [
                'tabId' => 'weekly',
                'isActive' => false,
                'stories' => $weeklyTopPurchased
            ])

            <!-- Monthly Hot Stories -->
            @include('components.hot_story_tab', [
                'tabId' => 'monthly',
                'isActive' => false,
                'stories' => $monthlyTopPurchased
            ])
        </div>
    </div>
</div>


@once
    @push('styles')
        <style>
            /* Hot Stories Styles */


            .hot-stories-list {
                transition: background-color 0.2s;
            }

            .hot-stories-list:hover {
                background-color: rgba(0, 0, 0, 0.03);
            }

            .story-rank {
                min-width: 40px;
                min-height: 40px;
                width: 30px;
                height: 30px;
                flex: 0 0 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                border-radius: 4px;
                margin-right: 10px;
                color: var(--primary-color-3);
                border: 1px solid var(--primary-color-4);
                background-color: transparent;
            }

            .rank-icon {
                width: 40px;
                height: 40px;
                object-fit: contain;
            }

            .hot-story-thumb {
                width: 90px;
                height: 120px;
                object-fit: cover;
            }

            .hot-story-title {
                font-size: 0.9rem;
                margin-bottom: 5px;
                overflow: hidden;
            }

            .latest-chapter a {
                color: #666;
                text-decoration: none;
            }

            .latest-chapter a:hover {
                color: #007bff;
            }

            .publish-date {
                font-size: 0.8rem;
                margin-top: 2px;
            }

            .tab-button {
                background: none;
                border: 2px solid transparent;
                padding: 5px 30px;
                margin: 0 4px;
                border-radius: 15px;
                font-size: 0.9rem;
                font-weight: 500;
                color: #666;
                cursor: pointer;
                transition: all 0.3s ease;
                flex: 1;
                min-width: 0;
            }

            /* Responsive tabs */
            @media (max-width: 576px) {
                .tab-button {
                    padding: 5px 15px;
                    font-size: 0.8rem;
                    margin: 0 2px;
                }
            }

            @media (max-width: 480px) {
                .tab-button {
                    padding: 5px 8px;
                    font-size: 0.75rem;
                    margin: 0 1px;
                }
            }

            /* Ngày - Color 7 - Outline */
            #daily-tab {
                border-color: var(--primary-color-7);
                color: var(--primary-color-7);
            }

            /* Tuần - Color 1 - Outline */
            #weekly-tab {
                border-color: var(--primary-color-4);
                color: var(--primary-color-4);
            }

            /* Tháng - Color 6 - Outline */
            #monthly-tab {
                border-color: var(--primary-color-6);
                color: var(--primary-color-6);
            }

            .tab-button:hover {
                color: var(--primary-color-3);
                background-color: rgba(0, 0, 0, 0.05);
            }

            /* Ngày - Color 7 */
            #daily-tab.active {
                color: #000;
                background-color: var(--primary-color-7);
                border-color: var(--primary-color-7);
            }

            /* Tuần - Color 1 */
            #weekly-tab.active {
                color: #000;
                background-color: var(--primary-color-1);
                border-color: var(--primary-color-1);
            }

            /* Tháng - Color 6 */
            #monthly-tab.active {
                color: #000;
                background-color: var(--primary-color-6);
                border-color: var(--primary-color-6);
            }

            /* Dark mode styles */
            body.dark-mode .sidebar-widget {
                background-color: #2d2d2d !important;
                border-color: #404040 !important;
            }

            body.dark-mode .widget-header {
                background-color: #404040 !important;
            }

            body.dark-mode .tab-button {
                color: #ccc;
            }

            /* Dark mode - Ngày - Color 7 - Outline */
            body.dark-mode #daily-tab {
                border-color: var(--primary-color-7);
                color: var(--primary-color-7);
            }

            /* Dark mode - Tuần - Color 1 - Outline */
            body.dark-mode #weekly-tab {
                border-color: var(--primary-color-1);
                color: var(--primary-color-1);
            }

            /* Dark mode - Tháng - Color 6 - Outline */
            body.dark-mode #monthly-tab {
                border-color: var(--primary-color-6);
                color: var(--primary-color-6);
            }

            body.dark-mode .tab-button:hover {
                color: var(--primary-color-3);
                background-color: rgba(255, 255, 255, 0.1);
            }

            /* Dark mode - Ngày - Color 7 */
            body.dark-mode #daily-tab.active {
                color: #000;
                background-color: var(--primary-color-7);
                border-color: var(--primary-color-7);
            }

            /* Dark mode - Tuần - Color 1 */
            body.dark-mode #weekly-tab.active {
                color: #000;
                background-color: var(--primary-color-1);
                border-color: var(--primary-color-1);
            }

            /* Dark mode - Tháng - Color 6 */
            body.dark-mode #monthly-tab.active {
                color: #000;
                background-color: var(--primary-color-6);
                border-color: var(--primary-color-6);
            }

            body.dark-mode .hot-stories-list {
                border-color: #404040 !important;
            }

            body.dark-mode .hot-stories-list:hover {
                background-color: rgba(255, 255, 255, 0.05) !important;
            }

            body.dark-mode .hot-story-title a {
                color: #e0e0e0 !important;
            }

            body.dark-mode .hot-story-title a:hover {
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .story-rank {
                background-color: #404040 !important;
                border-color: var(--primary-color-3) !important;
                color: var(--primary-color-3) !important;
            }

            body.dark-mode #hotStoriesTabs .nav-link {
                color: #ccc !important;
                border-color: #404040 !important;
            }

            body.dark-mode #hotStoriesTabs .nav-link.active {
                color: var(--primary-color-3) !important;
                background-color: #404040 !important;
                border-color: #404040 #404040 #2d2d2d !important;
            }

            body.dark-mode .badge.bg-1 {
                background-color: var(--primary-color-3) !important;
            }
        </style>
    @endpush
@endonce
