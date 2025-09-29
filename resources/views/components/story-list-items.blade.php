<div class="row g-3">
    @forelse ($newStories as $story)
        <div class="col-12 col-sm-6">
            <div class="story-item p-3 rounded h-100 bg-white">
                <div class="d-flex flex-column h-100">
                    <!-- Story Header with Image and Title -->
                    <div class="d-flex align-items-start mb-2">
                        <img src="{{ Storage::url($story->cover) }}" class="story-thumb me-3" alt="{{ $story->title }}">
                        <div class="flex-grow-1">
                            <h2 class="fs-6 mb-1">
                                <a href="{{ route('show.page.story', $story->slug) }}"
                                    class="text-dark text-decoration-none fw-semibold hover-cl-8ed7ff">{{ $story->title }}</a>
                            </h2>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @foreach ($story->categories as $category)
                                    <span
                                        class="badge bg-light text-dark small rounded-pill">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Story Badges -->
                    <div class="d-flex gap-2 mb-2">
                        @if ($story->latestChapter && $story->latestChapter->created_at->diffInHours() < 24)
                            <span class="badge-new">New</span>
                        @endif
                        @if (isset($story->hot_score) && $story->hot_score > 1000)
                            <span class="badge-hot">Hot</span>
                        @endif
                        @if ($story->completed)
                            <span class="badge-full">Full</span>
                        @endif
                    </div>

                    <!-- Story Info -->
                    <div class="mt-auto text-end">
                        <div class="story-meta small">
                            <div class="text-muted mb-1">
                                <i class="fas fa-book-open me-1 cl-8ed7ff"></i>
                                @if ($story->latestChapter)
                                    <a class="text-muted text-decoration-none hover-cl-53e09f"
                                        href="{{ route('chapter', ['storySlug' => $story->slug, 'chapterSlug' => $story->latestChapter->slug]) }}">Chương
                                        {{ $story->latestChapter->number }}</a>
                                @else
                                    Chương 0
                                @endif
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-clock me-1 text-warning"></i>
                                @if ($story->latestChapter)
                                    {{ $story->latestChapter->created_at->diffForHumans() }}
                                @else
                                    Chưa cập nhật
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($story->last)
            <div class="text-success">
                <hr>
            </div>
        @endif

    @empty
        <div class="col-12">
            <div class="py-5 text-center bg-white rounded">
                <div class="mb-3">
                    <i class="fas fa-book-open text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-2">Không có truyện mới</h5>
                <p class="text-muted">Hiện chưa có truyện nào được cập nhật trong danh mục này.</p>
            </div>
        </div>
    @endforelse
</div>
