@if (isset($story) && $story->has_combo)
    <div class="combo-wrapper animate__animated animate__fadeIn">
        <div class="combo-card">
            <div class="combo-badge">
                @php
                    $totalChapterPrice = $story->total_chapter_price ?? 0;

                    if ($totalChapterPrice > 0) {
                        $savingPercent = round((($totalChapterPrice - $story->combo_price) / $totalChapterPrice) * 100);
                        $savingAmount = $totalChapterPrice - $story->combo_price;
                    } else {
                        $savingPercent = 0;
                        $savingAmount = 0;
                    }
                    
                @endphp

                @if ($savingPercent > 0)
                    <div class="combo-discount-badge">
                        <span class="discount-text">-{{ $savingPercent }}%</span>
                    </div>
                @endif
            </div>

            <div class="combo-content">


                <div class="combo-body">
                    <div class="combo-price-section">
                        <div class="price-comparison">
                            @if ($totalChapterPrice > 0)
                           <div>Tổng mua lẻ: <span class="old-price">{{ number_format($totalChapterPrice) }} Xu</span></div>
                            @endif
                            <div class="new-price">{{ number_format($story->combo_price) }} Xu</div>
                        </div>
                        <div class="savings-info">
                            @if ($totalChapterPrice > 0 && $savingAmount > 0)
                                <div class="savings-tag">
                                    <i class="fas fa-tags"></i> Tiết kiệm {{ number_format($savingAmount) }} Xu
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="combo-description">
                        <p>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Mua combo truyện <strong>"{{ $story->title }}"</strong> để được đọc tất cả
                            <strong>{{ $story->vip_chapters_count }}</strong> chương VIP
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Combo <strong>không bị giới hạn thời gian</strong>, đọc mọi lúc mọi nơi
                        </p>
                    </div>
                </div>

                <div class="combo-action">
                    @guest
                        <a href="{{ route('login') }}" class="btn buy-combo-btn">
                            <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập để mua
                        </a>
                    @else
                        <button class="btn buy-combo-btn"
                            onclick="showPurchaseModal('story', {{ $story->id }}, '{{ $story->title }}', {{ $story->combo_price }})">
                            <i class="fas fa-shopping-cart me-2"></i> Mua Ngay
                        </button>
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endif

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <style>
            /* Combo Wrapper */
            .combo-wrapper {
                margin: 1.5rem 0;
            }

            /* Main Card */
            .combo-card {
                position: relative;
                border: none;
                border-radius: 16px;
                background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
                box-shadow: 0 10px 30px var(--primary-color-1);
                overflow: hidden;
                padding: 0;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }

            .combo-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px var(--primary-color-2);
            }

            .combo-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 6px;
                background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-color-2) 100%);
            }

            /* Badge */
            .combo-discount-badge {
                position: absolute;
                top: 20px;
                right: -30px;
                background: #ff3860;
                color: white;
                transform: rotate(45deg);
                padding: 5px 40px;
                font-weight: bold;
                z-index: 100;
                box-shadow: 0 2px 10px rgba(255, 56, 96, 0.3);
                animation: pulse-red 2s infinite;
            }

            /* Content */
            .combo-content {
                padding: 1.5rem;
            }

            .combo-header {
                display: flex;
                align-items: center;
                margin-bottom: 1.25rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
            }

            .combo-icon {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: var(--primary-color);
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 1rem;
            }

            .combo-icon i {
                font-size: 1.5rem;
                color: white;
            }

            .combo-title {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 700;
                color: #333;
            }

            .combo-body {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
                margin-bottom: 1.5rem;
            }

            /* Price Section */
            .combo-price-section {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .price-comparison {
                display: flex;
                flex-direction: column;
            }

            .old-price {
                font-size: 1rem;
                color: #888;
                text-decoration: line-through;
                margin-bottom: 0.25rem;
            }

            .new-price {
                font-size: 1.75rem;
                font-weight: bold;
                color: var(--primary-color-3);
            }

            .savings-tag {
                display: inline-block;
                padding: 5px 12px;
                background-color: rgba(76, 175, 80, 0.1);
                color: #4CAF50;
                border-radius: 50px;
                font-weight: 500;
                font-size: 0.9rem;
            }

            /* Description */
            .combo-description {
                padding: 1rem;
                background-color: rgba(67, 80, 255, 0.05);
                border-left: 4px solid var(--primary-color);
                border-radius: 0 8px 8px 0;
            }

            .combo-description p {
                margin-bottom: 0.75rem;
                font-size: 1rem;
                color: #444;
            }

            /* Action Button */
            .combo-action {
                display: flex;
                justify-content: center;
            }

            .buy-combo-btn {
                padding: 0.75rem 2.5rem;
                font-size: 1.1rem;
                font-weight: 600;
                background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-color-2) 100%);
                border: none;
                border-radius: 50px;
                box-shadow: 0 5px 15px var(--primary-color-2);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .buy-combo-btn::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, var(--primary-color-3), transparent);
                transition: 0.5s;
            }

            .buy-combo-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px var(--primary-color-3);
            }

            .buy-combo-btn:hover::after {
                left: 100%;
            }

            .buy-combo-btn:active {
                transform: translateY(0);
            }

            /* Animations */
            .pulse-animation {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(67, 80, 255, 0.6);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(67, 80, 255, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(67, 80, 255, 0);
                }
            }

            @keyframes pulse-red {
                0% {
                    box-shadow: 0 0 0 0 rgba(255, 56, 96, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(255, 56, 96, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(255, 56, 96, 0);
                }
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .combo-price-section {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .combo-action {
                    width: 100%;
                }

                .buy-combo-btn {
                    width: 100%;
                }

                .combo-title {
                    font-size: 1.3rem;
                }

                .new-price {
                    font-size: 1.5rem;
                }

                .combo-description {
                    padding: 0.75rem;
                }

                .combo-description p {
                    font-size: 0.95rem;
                }
            }

            /* Dark mode styles for combo_story component */
            body.dark-mode .combo-card {
                background: linear-gradient(135deg, #2d2d2d 0%, #1a1a2e 100%) !important;
            }

            body.dark-mode .combo-title {
                color: #e0e0e0 !important;
            }

            body.dark-mode .combo-description {
                background-color: rgba(57, 205, 224, 0.1) !important;
            }

            body.dark-mode .combo-description p {
                color: #e0e0e0 !important;
            }

            body.dark-mode .old-price {
                color: rgba(224, 224, 224, 0.6) !important;
            }

            body.dark-mode .new-price {
                color: var(--primary-color-3) !important;
            }

            body.dark-mode .savings-tag {
                background-color: rgba(76, 175, 80, 0.2) !important;
                color: #81c784 !important;
            }
        </style>
    @endpush
@endonce
