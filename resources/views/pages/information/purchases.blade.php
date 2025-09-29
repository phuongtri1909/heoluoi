@extends('layouts.information')

@section('info_title', 'Truyện đã mua')
@section('info_description', 'Các truyện và chương bạn đã mua trên ' . request()->getHost())
@section('info_keyword', 'Truyện đã mua, chương đã mua, ' . request()->getHost())
@section('info_section_title', 'Truyện đã mua')
@section('info_section_desc', 'Xem lại các truyện và chương bạn đã mua')

@section('info_content')
    <ul class="nav nav-tabs mb-4" id="purchaseTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="chapters-tab" data-bs-toggle="tab" data-bs-target="#chapters-content" type="button" role="tab" aria-controls="chapters-content" aria-selected="true">
                <i class="fas fa-book-open me-2"></i>Chương đã mua
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="stories-tab" data-bs-toggle="tab" data-bs-target="#stories-content" type="button" role="tab" aria-controls="stories-content" aria-selected="false">
                <i class="fas fa-books me-2"></i>Combo truyện
            </button>
        </li>
    </ul>

    <div class="tab-content" id="purchaseTabContent">
        <!-- Purchased Chapters Tab -->
        <div class="tab-pane fade show active" id="chapters-content" role="tabpanel" aria-labelledby="chapters-tab">
            @if(count($purchasedChapters) > 0)
                <div class="purchased-chapters-list">
                    @foreach($purchasedChapters as $key => $purchase)
                        <div class="purchase-item" data-delay="{{ $key }}">
                            <div class="d-flex">
                                <div class="story-thumb-container me-3">
                                    <img src="{{ Storage::url($purchase->chapter->story->cover) }}" alt="{{ $purchase->chapter->story->title }}" class="story-thumb">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="story-title">
                                            <a href="{{ route('show.page.story', $purchase->chapter->story->slug) }}">
                                                {{ $purchase->chapter->story->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted ms-2">{{ Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}</small>
                                    </div>
                                    <div class="story-chapter">
                                        <a href="{{ route('chapter', [$purchase->chapter->story->slug, $purchase->chapter->slug]) }}">
                                            <i class="fas fa-bookmark me-1 text-primary"></i>
                                            Chương {{ $purchase->chapter->number }}: {{ $purchase->chapter->title }}
                                        </a>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="purchase-details">
                                            <i class="fas fa-coins text-warning"></i>
                                            <span>{{ number_format($purchase->price) }} xu</span>
                                        </span>
                                        <a href="{{ route('chapter', [$purchase->chapter->story->slug, $purchase->chapter->slug]) }}"
                                           class="btn btn-sm action-btn-primary">
                                            <i class="fas fa-book-open me-1"></i> Đọc ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3 opacity-25">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-cart-arrow-down empty-icon"></i>
                    <p class="empty-text">Bạn chưa mua chương truyện nào.</p>
                    <a href="{{ route('home') }}" class="btn discover-btn">Khám phá truyện ngay</a>
                </div>
            @endif
        </div>

        <!-- Purchased Stories Tab -->
        <div class="tab-pane fade" id="stories-content" role="tabpanel" aria-labelledby="stories-tab">
            @if(count($purchasedStories) > 0)
                <div class="purchased-stories-list">
                    @foreach($purchasedStories as $key => $purchase)
                        <div class="purchase-item" data-delay="{{ $key }}">
                            <div class="d-flex">
                                <div class="story-thumb-container me-3">
                                    <img src="{{ Storage::url($purchase->story->cover) }}" alt="{{ $purchase->story->title }}" class="story-thumb">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="story-title">
                                            <a href="{{ route('show.page.story', $purchase->story->slug) }}">
                                                {{ $purchase->story->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted ms-2">{{ Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}</small>
                                    </div>
                                    <div class="story-details mb-2">
                                        <span class="badge bg-success me-2">
                                            <i class="fas fa-check-circle me-1"></i> Combo Truyện
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-book me-1"></i>
                                            {{ $purchase->story->chapters()->where('is_free', 0)->count() }} chương VIP
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="purchase-details">
                                            <i class="fas fa-coins text-warning"></i>
                                            <span>{{ number_format($purchase->price) }} xu</span>
                                        </span>
                                        <a href="{{ route('show.page.story', $purchase->story->slug) }}"
                                           class="btn btn-sm action-btn-primary">
                                            <i class="fas fa-book me-1"></i> Xem truyện
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3 opacity-25">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-cart-arrow-down empty-icon"></i>
                    <p class="empty-text">Bạn chưa mua combo truyện nào.</p>
                    <a href="{{ route('home') }}" class="btn discover-btn">Khám phá truyện ngay</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            // Hiệu ứng hiện dần các item
            $('.purchase-item').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(100 * index).animate({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                }, 300);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Purchase Items */
        .purchase-item {
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .purchase-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .story-thumb {
            width: 60px;
            height: 90px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .story-title {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .story-title a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .story-chapter a {
            color: #666;
            text-decoration: none;
        }

        .story-chapter a:hover {
            color: var(--primary-color);
        }

        .purchase-details {
            font-size: 0.9rem;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background-color: rgba(0, 0, 0, 0.02);
            border-radius: 10px;
        }

        .empty-icon {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 15px;
        }

        .empty-text {
            font-size: 1.1rem;
            color: #888;
            margin-bottom: 20px;
        }

        .discover-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .discover-btn:hover {
            background-color: var(--primary-color-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Tab styling */
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            position: relative;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: transparent;
            border: none;
        }

        .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }

        .nav-tabs {
            border-bottom: 1px solid #eee;
        }

        /* Action button */
        .action-btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .action-btn-primary:hover {
            background-color: var(--primary-color-dark);
            color: white;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .story-thumb {
                width: 50px;
                height: 75px;
            }

            .story-title {
                font-size: 1rem;
            }
        }
    </style>
@endpush
