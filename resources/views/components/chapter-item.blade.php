<li class="d-flex align-items-center mb-3">
    <i class="fa-regular fa-sun me-2"></i>
    <a class="fw-semibold text-dark title-chapter-item" href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $chapter->slug]) }}"
        title="{{ $chapter->title }}">
        <span class="chapter-text"> Chương {{ $chapter->number }}
            @if ($chapter->title && $chapter->title !== 'Chương ' . $chapter->number)
                : {{ $chapter->title }}
            @endif
        <span>
    </a>
</li>
@push('styles')
    <style>
        .title-chapter-item:hover{
            text-decoration: underline;
        }
    </style>
@endpush
