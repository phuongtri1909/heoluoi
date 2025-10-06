<li class="d-flex align-items-center mb-3">
    <a class="fw-semibold text-dark title-chapter-item" href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}"
        title="{{ $chapter->title }}">
        <span class="chapter-text"> Chương {{ $chapter->number }}
            @if ($chapter->title && $chapter->title !== 'Chương ' . $chapter->number)
                : {{ $chapter->title }}
            @endif
        </span>
    </a>
</li>
@push('styles')
    <style>
        .title-chapter-item:hover{
            text-decoration: underline;
        }
        
        .title-chapter-item {
            display: block;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }
        
        .chapter-text {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            width: 100%;
        }
    </style>
@endpush
