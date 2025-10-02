<div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
    <div class="hot-stories-list">
        @foreach ($stories as $index => $story)
            <div class="d-flex align-items-center mb-2">
                <div class="d-flex align-items-center mt-2">
                    <span class="story-rank">
                        @if ($index + 1 == 1)
                            <img src="/images/ranks/level1.png" alt="Level 1" class="rank-icon">
                        @elseif($index + 1 == 2)
                            <img src="/images/ranks/level2.png" alt="Level 2" class="rank-icon">
                        @elseif($index + 1 == 3)
                            <img src="/images/ranks/level3.png" alt="Level 3" class="rank-icon">
                        @else
                            {{ $index + 1 }}
                        @endif
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
                                <p class="mb-0 fs-6 fw-semibold">{{ $story->author_name }}</p>

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
