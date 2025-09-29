@if($chapters->count() > 0)
    @foreach($chapters as $chapter)
        <a href="{{ route('chapter', ['storySlug' => $chapter->story->slug, 'chapterSlug' => $chapter->slug]) }}" 
            class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <div>
                    <span class="badge bg-primary me-2">Chương {{ $chapter->number }}</span>
                    <span class="chapter-title">{{ $chapter->title }}</span>
                </div>
                <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>{{ $chapter->created_at->format('d/m/Y') }}</small>
            </div>
        </a>
    @endforeach
@else
    <div class="list-group-item text-center py-3">
        <i class="fas fa-search me-2"></i>Không tìm thấy kết quả
    </div>
@endif