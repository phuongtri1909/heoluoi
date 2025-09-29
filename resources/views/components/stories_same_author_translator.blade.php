<section id="comments" class="my-3 my-md-5">
    <div class="container px-2 px-md-3">
        <div class="row">
            @if($authorStories && $authorStories->count() > 0)
                <div class="col-12 {{ $translatorStories && $translatorStories->count() > 0 ? 'col-md-6' : 'col-md-12' }}">
                    <div
                        class="section-title d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                        <div class="title-container mb-2">
                            <i class="fa-solid fa-pen-nib fa-xl color-2"></i>
                            <h5 class="fw-bold ms-2 d-inline mb-0">Truyện của tác giả <a
                                    href="{{ route('search.author', ['query' => $story->author_name]) }}"
                                    class="text-decoration-none color-3">{{ $story->author_name }}</a></h5>
                        </div>
                    </div>

                    <div>
                        @foreach ($authorStories as $story)
                            <div class="story-item d-flex align-items-center mb-3">
                                <img src="{{ asset('storage/' . $story->cover) }}" alt="{{ $story->title }}"
                                    class="story-thumb me-3">
                                <a href="{{ route('show.page.story', $story->slug) }}"
                                    class="text-decoration-none text-dark story-link">
                                    {{ $story->title }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($translatorStories && $translatorStories->count() > 0)
                <div class="col-12 {{ $authorStories && $authorStories->count() > 0 ? 'col-md-6' : 'col-md-12' }}">
                    <div
                        class="section-title d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                        <div class="title-container mb-2">
                            <i class="fa-solid fa-pen fa-xl color-2"></i>
                            <h5 class="fw-bold ms-2 d-inline mb-0">Truyện của dịch giả <a
                                    href="{{ route('search.translator', ['query' => $story->user->name]) }}"
                                    class="text-decoration-none color-3">{{ $story->user->name }}</a></h5>
                        </div>
                    </div>

                    <div>
                        @foreach ($translatorStories as $story)
                            <div class="story-item d-flex align-items-center mb-3">
                                <img src="{{ asset('storage/' . $story->cover) }}" alt="{{ $story->title }}"
                                    class="story-thumb me-3">
                                <a href="{{ route('show.page.story', $story->slug) }}"
                                    class="text-decoration-none text-dark story-link">
                                    {{ $story->title }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>


@once
    @push('scripts')
    @endpush

    @push('styles')
        <style>
            .story-thumb {
                width: 50px;
                height: 70px;
                object-fit: cover;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .story-item:hover .story-thumb {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            .story-link {
                font-size: 14px;
                font-weight: 500;
                line-height: 1.3;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                transition: color 0.2s ease;
            }

            .story-item:hover .story-link {
                color: var(--primary-color-3) !important;
            }

            .story-item {
                transition: all 0.3s ease;
                padding: 5px;
                border-radius: 8px;
            }

            .story-item:hover {
                background-color: rgba(0, 0, 0, 0.02);
            }

            @media (max-width: 768px) {
                .story-thumb {
                    width: 40px;
                    height: 60px;
                }

                .story-link {
                    font-size: 13px;
                }
            }

            /* Dark mode styles for stories_same_author_translator component */
            body.dark-mode .section-title {
                color: #e0e0e0 !important;
            }

            body.dark-mode .story-link {
                color: #e0e0e0 !important;
            }

            body.dark-mode .story-item:hover {
                background-color: rgba(255, 255, 255, 0.05) !important;
            }

            body.dark-mode .story-item:hover .story-link {
                color: var(--primary-color-3) !important;
            }
        </style>
    @endpush
@endonce
