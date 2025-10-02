@if (isset($story) && $story->has_combo && (!auth()->check() || !\App\Models\StoryPurchase::hasUserPurchased(auth()->id(), $story->id)))
    <div class="combo-wrapper animate__animated animate__fadeIn">
        <div class="combo-card">

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


            <div class="combo-content text-center">

                <div class="">
                    <p class="fs-5 mb-0">
                        Ủng hộ <span class="color-3 fw-semibold">{{ number_format($story->combo_price) }} Cám</span> để
                        mở full truyện <strong>"{{ $story->title }}"</strong>
                    </p>

                    <span class="fs-6">Rẻ hơn {{ $savingPercent }}% so với đọc từng chương (tiết kiệm
                        {{ number_format($savingAmount) }} Cám)</span>

                    <p class="fs-7">Sau khi mua combo truyện, bạn có thể đọc truyện này mãi mãi,không giới hạn số lần.
                    </p>
                </div>


                <div class="combo-action">
                    @guest
                        <a href="{{ route('login') }}" class="btn buy-combo-btn">
                            <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập để mua
                        </a>
                    @else
                        <button class="btn buy-combo-btn fw-semibold"
                            onclick="showPurchaseModal('story', {{ $story->id }}, '{{ $story->title }}', {{ $story->combo_price }})">
                            Ủng hộ
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
                border: 2px solid var(--primary-color-2);
                background: #d2d8ab;
                border-radius: 12px;
                padding: 15px;
                box-shadow: 0 10px 30px var(--primary-color-1);
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }

            .combo-card::before {
                content: '';
                position: absolute;
                top: -2px;
                left: 20px;
                width: 160px;
                height: 2px;
                background-color: var(--primary-bg-3);
            }

            .combo-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px var(--primary-color-2);
            }

            /* Dark mode styles for combo_story component */
            body.dark-mode .combo-card::before {
                background-color: #1a1a1a;
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
                padding: 2rem 1.5rem;
                position: relative;
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
                padding: 0.5rem 2.5rem;
                font-size: 1.1rem;
                font-weight: 600;
                background: linear-gradient(90deg, var(--primary-color-2) 0%, var(--primary-color-7) 100%);
                border: none;
                border-radius: 50px;
                box-shadow: 0 5px 15px var(--primary-color-7);
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
                background: linear-gradient(90deg, transparent, var(--primary-color-7), transparent);
                transition: 0.5s;
            }

            .buy-combo-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px var(--primary-color-7);
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
