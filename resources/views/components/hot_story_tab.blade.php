<div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
    <div class="hot-stories-list">
        @foreach ($stories as $index => $story)
            <a href="{{ route('show.page.story', $story->slug) }}" class="hot-story-row text-decoration-none d-flex align-items-stretch mb-3 p-2 rounded-2">
                <span class="story-rank flex-shrink-0 {{ $index + 1 == 1 ? 'rank-gold' : ($index + 1 == 2 ? 'rank-silver' : ($index + 1 == 3 ? 'rank-bronze' : '')) }}">{{ $index + 1 }}</span>
                <div class="story-cover flex-shrink-0 me-3">
                    <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                        alt="{{ $story->title }}" class="hot-story-thumb rounded-2">
                </div>
                <div class="story-info flex-grow-1 min-width-0 d-flex flex-column">
                    <h4 class="hot-story-title mb-1 fw-bold text-dark title-dark">
                        {{ Str::limit($story->title, 45) }}
                    </h4>
                    @if ($story->relationLoaded('categories') && $story->categories->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @foreach ($story->categories->take(3) as $cat)
                                <span class="badge border border-1 border-color-3 color-3 rounded-pill d-flex align-items-center me-2">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if (!empty($story->description))
                        <p class="hot-story-desc mb-0 text-muted small">{{ cleanDescription($story->description, 800) }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>

@push('styles')
    <style>
        .hot-story-row {
            transition: background-color 0.2s;
        }
        .hot-story-row:hover {
            background-color: rgba(0, 0, 0, 0.04);
        }
        body.dark-mode .hot-story-row:hover {
            background-color: rgba(255, 255, 255, 0.06);
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
        .category-tag {
            font-size: 0.7rem;
            font-weight: 500;
            border: 1px solid var(--primary-color-8);
            background-color: rgba(178, 88, 83, 0.08);
            color: var(--color-text, #333);
        }
        .hot-story-desc {
            font-size: 0.8rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 6;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        body.dark-mode .category-tag {
            border-color: var(--primary-color-9);
            background-color: rgba(255, 255, 255, 0.08);
            color: #e0e0e0;
        }
        body.dark-mode .hot-story-desc {
            color: #aaa !important;
        }
    </style>
@endpush