<div class="d-flex align-items-start mb-2 text-gray-600">
    <div class="story-image-wrapper position-relative d-inline-block">
        <img src="{{ Storage::url($story->cover) }}" class="story-image me-3 rounded-start" alt="{{ $story->title }}">

        @if ($story->is_18_plus === 1)
            @include('components.tag18plus')
        @endif
    </div>
    <div class="flex-grow-1">
        <h2 class="fs-6 mb-1">
            <a href="{{ route('show.page.story', $story->slug) }}"
                class="text-dark text-decoration-none fw-semibold hover-color-3">{{ $story->title }}</a>
        </h2>

        <div class="d-flex justify-content-between align-items-center">
            <span class="rating-stars text-sm" title="{{ number_format($story->average_rating, 1) }} sao">
                @php
                    $rating = $story->average_rating ?? 0;

                    $displayRating = round($rating * 2) / 2;
                @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($displayRating >= $i)
                        <i class="fas fa-star cl-ffe371 "></i>
                    @elseif ($displayRating >= $i - 0.5)
                        <i class="fas fa-star-half-alt cl-ffe371 "></i>
                    @else
                        <i class="far fa-star cl-ffe371 "></i>
                    @endif
                @endfor
                {{ rtrim(rtrim(number_format($rating, 1, '.', ''), '0'), '.') }}
            </span>

            <div class="fs-8"><i class="fa-solid fa-bookmark fs-8 text-danger"></i>
                {{ number_format($story->bookmarks_count) }}</div>
        </div>


        <div class="fs-8"><i class="fa-solid fa-user fs-8 text-info"></i> {{ $story->author_name }}</div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex flex-wrap gap-1 my-2 text-sm">
                @php
                    $mainCategories = $story->categories->where('is_main', true);
                    $subCategories = $story->categories->where('is_main', false);
                    $displayCategories = collect();

                    if ($mainCategories->isNotEmpty()) {
                        foreach ($mainCategories->take(2) as $category) {
                            $displayCategories->push($category);
                        }

                        // Nếu chỉ có 1 danh mục chính, thêm 1 danh mục phụ
                        if ($displayCategories->count() === 1 && $subCategories->isNotEmpty()) {
                            $displayCategories->push($subCategories->first());
                        }
                    } else {
                        foreach ($subCategories->take(2) as $category) {
                            $displayCategories->push($category);
                        }
                    }
                @endphp


                @foreach ($displayCategories as $category)
                    <span
                        class="badge bg-1 text-white small rounded-pill d-flex align-items-center">{{ $category->name }}</span>
                @endforeach

            </div>

            <div>
                <div class="text-muted text-sm mb-1 fs-8">
                    @if ($story->latestChapter)
                        {{ $story->latestChapter->created_at->diffForHumans() }}
                    @else
                        Chưa cập nhật
                    @endif
                </div>

                <p class="badge bg-1 text-white small rounded-pill d-flex align-items-center">
                    {{ $story->chapters_count ?? ($story->chapters ? $story->chapters->count() : 0) }} chương</p>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .story-image {
                width: 90px;
                height: 130px;
                object-fit: cover;
                display: block;
                flex-shrink: 0;
            }

            .story-image:hover {
                transform: scale(1.05);
                transition: transform 0.3s ease;
            }
        </style>
    @endpush
@endonce
