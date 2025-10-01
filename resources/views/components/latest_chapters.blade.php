@if ($latestChapters && $latestChapters->count() > 0)
    <div class="latest-chapters-section">
        <div class="latest-chapters-title">Chương Mới Nhất</div>
        <div class="latest-chapters-border">
            <div class="latest-chapters-content px-2 py-3">
                <ul class="latest-chapters-list ps-0 mb-0">
                    @foreach ($latestChapters as $chapter)
                        <li class="latest-chapter-item mb-2">
                            <div class="latest-chapter-name">
                                <span class="latest-chapter-icon text-dark">
                                    <img src="{{ asset('images/d/play.png') }}" alt="Chapter Icon" class="" height="10">
                                </span>
                                <span class="fw-semibold">
                                    Chương {{ $chapter->number }}
                                    @if($chapter->title && $chapter->title !== 'Chương ' . $chapter->number)
                                        : {{ $chapter->title }}
                                    @endif
                                </span>
                            </div>
                            <span class="border border-1 border-color-2 fs-7 p-1">
                                New
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    @once
        @push('styles')
            <style>
                .latest-chapters-section {
                    position: relative;
                    margin-top: 20px;
                }

                .latest-chapters-title {
                    position: absolute;
                    top: -12px;
                    left: 23px;
                    font-size: 15px;
                    font-weight: bold;
                    color: #333;
                    padding: 0 10px;
                    z-index: 1;
                }

                .latest-chapters-title::before {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 0;
                    right: 0;
                    height: 2px;
                    background-color: inherit;
                    z-index: -1;
                }

                .dark-mode .latest-chapters-title {
                    color: #e0e0e0;
                }

                .latest-chapters-border {
                    border: 2px solid var(--primary-color-2);
                    border-radius: 12px;
                    padding: 15px;
                    position: relative;
                }

                .latest-chapters-border::before {
                    content: '';
                    position: absolute;
                    top: -2px;
                    left: 20px;
                    width: 160px;
                    height: 2px;
                    background-color: var(--primary-bg-3);
                }

                .dark-mode .latest-chapters-border::before {
                    background-color: #1a1a1a;
                }

                .latest-chapters-content {
                    background: #d2d8ab;
                    border-radius: 8px;
                }

                .latest-chapters-list {
                    list-style: none;
                }

                .latest-chapter-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background-color 0.2s;
                }

                .latest-chapter-name {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #2c3e50;
                    font-size: 15px;
                }

                .latest-chapter-icon {
                    width: 20px;
                    height: 20px;
                    background-color: #000000;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 11px;
                    font-weight: bold;
                }
            </style>
        @endpush
    @endonce
@endif
