@push('styles')
    <style>

    </style>
@endpush

@push('scripts')
    {{-- couter --}}
@endpush

<section id="info-book ">
    <div class="container">

        <div class="row g-2 mt-2">
            <div class="col-12 col-lg-6">
                <div class="info-card h-100">
                    <h6 class="info-title text-dark">THỐNG KÊ</h6>
                    <div class="stats-list">
                        <div class="stat-item text-dark">
                            <i class="fas fa-bookmark text-danger"></i>
                            <span class="counter" data-target="{{ $stats['total_chapters'] }}">0</span>
                            <span>Chương</span>
                        </div>
                        <div class="stat-item text-dark">
                            <i class="fas fa-eye text-success"></i>
                            <span class="counter" data-target="{{ $stats['total_views'] }}">0</span>
                            <span>Lượt Xem</span>
                        </div>
                        <div class="stat-item text-dark">
                            <i class="fas fa-star text-warning"></i>
                            <span class="counter" data-target="{{ $stats['ratings']['count'] }}">0</span>
                            <span>{{ number_format($stats['ratings']['average'], 1) }}/5
                                ({{ $stats['ratings']['count'] }} đánh giá)</span>
                        </div>
                        <div class="stat-item text-dark">
                            Tình trạng : @if ($status->status == 'done')
                                <span class="text-success fw-bold">Hoàn Thành</span>
                            @else
                                <span class="text-danger fw-bold">Đang viết</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="info-card h-100">
                    <h6 class="info-title text-dark">ĐÁNH GIÁ</h6>
                    <div class="rating">
                        @php
                            $rating = auth()->check() ? auth()->user()->rating ?? 0 : 0;
                            $fullStars = floor($rating);
                            $hasHalfStar = $rating - $fullStars >= 0.5;
                        @endphp

                        <div class="stars" id="rating-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $fullStars ? 'full' : 'empty' }}"
                                    data-rating="{{ $i }}"></i>
                            @endfor
                        </div>
                        <div class="rating-number mt-2">{{ number_format($rating, 1) }}/5</div>
                    </div>
                </div>
            </div>

           
        </div>
    </div>
</section>
@push('scripts')
    <script>
        $(document).ready(function() {
            // Hover effect
            $('.stars i').hover(
                function() {
                    let rating = $(this).data('rating');
                    highlightStars(rating);
                },
                function() {
                    let currentRating = {{ $rating }};
                    highlightStars(currentRating);
                }
            );

            // Click handler
            $('.stars i').click(function() {
                @if (!auth()->check())
                    window.location.href = '{{ route('login') }}';
                    return;
                @endif

                let rating = $(this).data('rating');

                $.ajax({
                    url: '{{ route('ratings.store') }}',
                    type: 'POST',
                    data: {
                        rating: rating,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                      
                        if (res.status === 'success') {
                            highlightStars(rating);
                            $('.rating-number').text(rating + '/5');
                            showToast(res.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        
                        showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                    }
                });
            });

            function highlightStars(rating) {
                $('.stars i').each(function(index) {
                    if (index < rating) {
                        $(this).removeClass('empty').addClass('full');
                    } else {
                        $(this).removeClass('full').addClass('empty');
                    }
                });
            }
        });
    </script>
@endpush
