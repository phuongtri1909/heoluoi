<section class="the-loai-section mt-5">
    <div class="text-center mb-3">
        <h2 class="fs-3 text-center m-0 color-8 fw-bold title-dark font-FzAstila">Thể loại</h2>
    </div>

    <div class="the-loai-grid rounded p-3">
        @foreach ($categories as $index => $category)
            <a href="{{ route('categories.story.show', $category->slug) }}"
                class="the-loai-item {{ $index >= 18 ? 'the-loai-item-hidden' : '' }}">{{ $category->name }}</a>
        @endforeach
       
    </div>
    @if ($categories->count() > 18)
    <div class="text-center">
        <button type="button" class="btn btn-link p-0 text-decoration-none color-2 mt-3 text-center" id="theLoaiToggle">
            Xem thêm &raquo;
        </button>
    </div>
@endif
</section>

@push('styles')
    <style>
        .the-loai-header-bar {
            width: 4px;
            height: 1.2em;
            background: var(--primary-color-2);
            border-radius: 2px;
        }

        .the-loai-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem 1rem;
        }

        @media (min-width: 576px) {
            .the-loai-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 992px) {
            .the-loai-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        .the-loai-item {
            color: #374151;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .the-loai-item:hover {
            color: var(--primary-color-3);
        }

        .the-loai-item-hidden {
            display: none;
        }

        .the-loai-item-hidden.the-loai-expanded {
            display: block;
        }

        body.dark-mode .the-loai-item {
            color: #e0e0e0;
        }

        body.dark-mode .the-loai-item:hover {
            color: var(--primary-color-3);
        }
    </style>
@endpush

@if ($categories->count() > 18)
    @push('scripts')
        <script>
            (function() {
                const toggle = document.getElementById('theLoaiToggle');
                const hiddenItems = document.querySelectorAll('.the-loai-item-hidden');
                if (!toggle || hiddenItems.length === 0) return;
                let expanded = false;
                toggle.addEventListener('click', function() {
                    expanded = !expanded;
                    hiddenItems.forEach(el => el.classList.toggle('the-loai-expanded', expanded));
                    toggle.innerHTML = expanded ?
                        'thu gọn <i class="fa-solid fa-chevron-up fa-xs"></i>' :
                        'Xem thêm &raquo;';
                });
            })();
        </script>
    @endpush
@endif
