@if (isset($tagSections) && $tagSections->isNotEmpty())
<section class="truyen-theo-chu-de mt-5">
    <h2 class="fs-3 text-center m-0 fw-bold title-dark font-FzAstila mb-4" style="color: #8b7355;">Truyện theo chủ đề</h2>

    <div class="tag-sections-grid">
        @foreach ($tagSections as $section)
            @php
                $tag = $section['tag'];
                $stories = $section['stories'];
                $first = $stories->first();
                $rest = $stories->slice(1);
            @endphp
            <div class="tag-card rounded-4">
                <div class="tag-card-header">
                    <span class="tag-card-name">{{ $tag->name }}</span>
                    <a href="{{ route('tag.stories', $tag->slug) }}" class="tag-card-more" title="Xem thêm">&raquo;</a>
                </div>
                <div class="tag-card-body">
                    @if ($first)
                        <a href="{{ route('show.page.story', $first->slug) }}" class="tag-first-story">
                            <div class="tag-first-cover">
                                <img src="{{ $first->cover ? Storage::url($first->cover) : asset('assets/images/story_default.jpg') }}"
                                    alt="{{ $first->title }}">
                            </div>
                            <span class="tag-first-title">{{ Str::limit($first->title, 50) }}</span>
                        </a>
                    @endif
                    <ul class="tag-story-list">
                        @foreach ($rest as $story)
                            <li>
                                <a href="{{ route('show.page.story', $story->slug) }}" class="tag-list-link fw-semibold">{{ Str::limit($story->title, 50) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</section>

@push('styles')
<style>
    /* Giao diện đúng như hình: header #717457, body #e9c485, tên truyện đầu #a4a68e */
    .tag-card {
        overflow: hidden;
        border: 2px solid #a4a68e;
    }
    .tag-card-header {
        background-color: #717457;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    .tag-card-name {
        font-weight: 500;
        color: #e9c485;
    }
    .tag-card-more {
        color: #e9c485;
        font-size: 1.2rem;
        text-decoration: none;
        line-height: 1;
    }
    .tag-card-more:hover {
        color: #e9c485;
    }
    .tag-card-body {
        padding: 10px;
    }
    .tag-first-story {
        display: flex;
        gap: 12px;
        text-decoration: none;
        margin-bottom: 12px;
        align-items: center;
    }
    .tag-first-cover {
        width: 80px;
        height: 120px;
        flex-shrink: 0;
        border-radius: 4px;
        overflow: hidden;
    }
    .tag-first-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .tag-first-title {
        color: #a4a68e;
        font-size: 0.9rem;
        font-weight: 600;
        line-height: 1.4;
        display: block;
        flex: 1;
        min-width: 0;
    }
    .tag-story-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .tag-story-list li {
        padding: 4px 0;
        font-size: 0.8rem;
    }
    .tag-list-link {
        color: #000;
        text-decoration: none;
    }
    .tag-list-link:hover {
        color: #717457;
    }
    .tag-sections-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(2, 1fr);
    }
    @media (min-width: 768px) {
        .tag-sections-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    body.dark-mode .tag-card-header {
        background-color: #5a5d47;
    }
    body.dark-mode .tag-card-body {
        background-color: #6b5d4a;
    }
    body.dark-mode .tag-first-title {
        color: #b5b79e !important;
    }
    body.dark-mode .tag-list-link {
        color: #c8c8c8;
    }
    body.dark-mode .tag-list-link:hover {
        color: #a4a68e;
    }
</style>
@endpush
@endif
