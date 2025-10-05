<div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
    <div class="hot-stories-list">
        @foreach ($stories as $index => $story)
            <div class="d-flex align-items-center mb-2">
                <div class="d-flex align-items-center mt-2">
                    <span class="story-rank {{ $index + 1 == 1 ? 'rank-gold' : ($index + 1 == 2 ? 'rank-silver' : ($index + 1 == 3 ? 'rank-bronze' : '')) }}">
                        {{ $index + 1 }}
                    </span>
                </div>

                <div class="hot-story-item d-flex">
                    <div class="story-cover me-3">
                        <a class="text-decoration-none" href="{{ route('show.page.story', $story->slug) }}">
                            <img src="{{ asset('storage/' . $story->cover) }}" alt="{{ $story->title }}"
                                class="hot-story-thumb rounded-2">
                        </a>
                    </div>
                    <div class="story-info w-100 d-flex flex-column justify-content-center">
                        <h4 class="hot-story-title">
                            <a class="text-decoration-none text-dark fs-5"
                                href="{{ route('show.page.story', $story->slug) }}">{{ $story->title }}</a>
                        </h4>

                        <div class="d-flex align-items-center flex-wrap">
                            @if ($story->author_name)
                                <p class="mb-0 fs-6 fw-semibold text-dark">{{ $story->author_name }}</p>

                                <span class="chapter-separator fs-6">|</span>
                            @endif

                            <div class="stats-info">
                                <span class="text-warning">
                                    <i class="fa-solid fa-star"></i>
                                    {{ number_format($story->average_rating, 1) }}
                                </span>
                            </div>
                        </div>

                        <div class="fs-6 fw-semibold">
                            <img src="{{ asset('images/svg/views.svg') }}" alt="Eye" class="eye-icon">
                            {{ number_format($story->total_views) }} lượt xem
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('styles')
    <style>
        body.dark-mode .eye-icon {
            filter: invert(1);
        }
          
        .story-rank.rank-gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3);
            border: 1px solid #FFD700;
            color: #000;
        }
        
        .story-rank.rank-silver {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
            box-shadow: 0 2px 4px rgba(192, 192, 192, 0.3);
            border: 1px solid #C0C0C0;
            color: #000;
        }
        
        .story-rank.rank-bronze {
            background: linear-gradient(135deg, #CD7F32, #B8860B);
            box-shadow: 0 2px 4px rgba(205, 127, 50, 0.3);
            border: 1px solid #CD7F32;
            color: #000;
        }
    </style>
@endpush