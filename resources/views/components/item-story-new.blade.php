<div class="row h-100 align-items-center">
    <div class="col-4 col-md-4">
        <a href="{{ route('show.page.story', $story->slug) }}" class="d-block position-relative">
            <img src="{{ $story->cover ? Storage::url($story->cover) : asset('assets/images/story_default.jpg') }}"
                alt="{{ $story->title }}" class="img-fluid rounded-3 image-story-new-item">
            @if ($story->is_18_plus === 1)
                @include('components.tag18plus')
            @endif
        </a>
    </div>
    <div class="col-8 col-md-8">
        <div class="d-flex flex-column h-100 justify-content-between">

            <h5 class="story-title mb-1 fw-semibold lh-base">
                <a href="{{ route('show.page.story', $story->slug) }}" class="text-decoration-none color-hover">
                    {{ $story->title }}
                </a>
            </h5>

            <div class="flex-grow-1">
                <div class="text-muted text-ssm story-description-new">
                    {{ cleanDescription($story->description, 800) }}
                </div>
            </div>

            <div class="mt-auto">
                <small class="">
                    <img src="{{ asset('assets/images/svg/user.svg') }}" alt="">
                    <a href="{{ route('search.translator', ['query' => $story->user->name]) }}"
                        class="text-decoration-none text-dark">
                        {{ Str::limit($story->user->name, 15) }}
                    </a>

                </small>

            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .image-story-new-item {
                width: 100%;
                height: 125px;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            @media (min-width: 400px) {
                .image-story-new-item {
                    height: 140px;
                }
            }

            @media (min-width: 500px) {
                .image-story-new-item {
                    height: 180px;
                }
            }

            @media (min-width: 992px) {
                .image-story-new-item {
                    height: 250px;
                }
            }

            .story-title {
                line-height: 1.3;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                font-size: 0.9rem;
            }

            .story-title a:hover {
                color: var(--primary-color-3) !important;
                text-decoration: none;
            }

            .story-description-new {
                display: -webkit-box;
                -webkit-line-clamp: 7;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.4;
            }

            .text-ssm {
                font-size: 0.75rem;
            }

            .text-sm {
                font-size: 0.875rem;
            }

            @media (max-width: 767.98px) {
                .story-title {
                    font-size: 0.85rem;
                }

                .story-description-new {
                    -webkit-line-clamp: 2;
                }

                .text-ssm {
                    font-size: 0.7rem;
                }
            }
        </style>
    @endpush
@endonce
