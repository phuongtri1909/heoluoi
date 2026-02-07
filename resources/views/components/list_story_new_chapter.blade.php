<section class="">
    <div class="p-3 pb-5 px-md-0 rounded-top-custom text-center">
        <h2 class="m-0 color-8 fw-bold title-dark fs-2 text-center font-FzAstila"> TRUYỆN MỚI CẬP NHẬT</h2>
    </div>
    @foreach ($latestUpdatedStories as $index => $story)
        @include('components.story_new', ['story' => $story])

        <hr class="my-3 color-1 {{ $index == $latestUpdatedStories->count() - 1 ? 'd-none' : '' }}">
    @endforeach
</section>

@push('styles')
    <style>
        .story-column {
            display: flex;
            flex-direction: column;
        }

        .story-item-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .story-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .story-content .mb-2 {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 0 !important;
        }

        .story-content .story-info-section {
            flex: 1;
            min-width: 0;
        }

        .story-content .time-info {
            flex-shrink: 0;
            text-align: right;
            min-width: 80px;
        }

        /* HR Management */
        .hr-desktop {
            display: none;
        }

        .hr-mobile {
            display: none;
        }

        /* Desktop: Show desktop HR logic */
        @media (min-width: 768px) {
            .hr-desktop {
                display: block;
            }

            .hr-mobile {
                display: none !important;
            }
        }

        /* Mobile: Show mobile HR logic */
        @media (max-width: 767.98px) {
            .hr-desktop {
                display: none !important;
            }

            .hr-mobile {
                display: block;
            }

            .story-content .time-info {
                text-align: left;
                min-width: auto;
            }
        }

        /* Dark mode styles */
        body.dark-mode .bg-list {
            background-color: #2d2d2d !important;
        }

        body.dark-mode hr {
            border-color: #404040 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            applySmartEqualHeight();
        });

        window.addEventListener('resize', function() {
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(applySmartEqualHeight, 150);
        });

        function applySmartEqualHeight() {
            document.querySelectorAll('.story-column').forEach(col => {
                col.style.alignItems = '';
                col.querySelector('.story-item-wrapper').style.height = '';
            });

            if (window.innerWidth >= 768) {
                const storyColumns = document.querySelectorAll('.story-column');

                for (let i = 0; i < storyColumns.length; i += 2) {
                    const leftColumn = storyColumns[i];
                    const rightColumn = storyColumns[i + 1];

                    if (leftColumn && rightColumn) {
                        const leftHeight = leftColumn.offsetHeight;
                        const rightHeight = rightColumn.offsetHeight;
                        const heightDiff = Math.abs(leftHeight - rightHeight);

                        if (heightDiff > 25) {
                            const maxHeight = Math.max(leftHeight, rightHeight);

                            leftColumn.style.alignItems = 'stretch';
                            rightColumn.style.alignItems = 'stretch';

                            leftColumn.querySelector('.story-item-wrapper').style.height = maxHeight + 'px';
                            rightColumn.querySelector('.story-item-wrapper').style.height = maxHeight + 'px';
                        }
                    }
                }
            }
        }
    </script>
@endpush
