<div class="row">
    {{-- Mobile View: Single Column --}}
    <div class="d-block d-md-none">
        <ul class="chapter-list text-muted">
            @foreach ($chapters as $chapter)
                <li>
                    @include('components.chapter-item', ['chapter' => $chapter])
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Desktop View: Two Columns --}}
    <div class="col-6 d-none d-md-block">
        <ul class="chapter-list text-muted">
            @foreach ($chapters->take(ceil($chapters->count() / 2)) as $chapter)
               <li>
                @include('components.chapter-item', ['chapter' => $chapter])
               </li>
            @endforeach
        </ul>
    </div>
    <div class="col-6 d-none d-md-block">
        <ul class="chapter-list text-muted">
            @foreach ($chapters->skip(ceil($chapters->count() / 2)) as $chapter)
                <li>
                    @include('components.chapter-item', ['chapter' => $chapter])
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('styles')
    <style>
        .chapter-card {
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .chapter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .chapter-link {
            display: flex;
            align-items: flex-start;
            padding: 5px 0;
            transition: all 0.2s;
            text-decoration: none;
            color: var(--text-color) !important;
        }

        .chapter-link:hover {
            color: var(--primary-color) !important;
            transform: translateX(5px);
        }

        /* Truyện đã đọc */
        .chapter-read {
            background-color: var(--primary-color-1) !important;
            border-radius: 4px;
            padding: 5px 8px !important;
            color: #fff !important;
        }

        .chapter-read .chapter-title {
            text-decoration: none;
            color: #fff;
            opacity: 0.9;
        }

        .chapter-read .chapter-date {
            color: rgba(255, 255, 255, 0.7);
        }

        .chapter-read .free-box {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .chapter-read .coin-box {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .vip-chapter .chapter-title {
            font-weight: 500;
        }

        .chapter-info {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .chapter-date {
            font-size: 0.75rem;
            color: #777;
            margin-top: 3px;
        }

        .vip-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #f1c40f;
            margin-left: 6px;
            font-size: 0.85em;
        }

        .chapter-read .vip-badge {
            color: #fff;
        }

        .vip-badge i {
            animation: glow 2s infinite;
        }

        .coin-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(241, 196, 15, 0.15);
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            min-width: 45px;
            text-align: center;
            font-weight: 500;
            color: #d4ac0d;
            border: 1px solid rgba(241, 196, 15, 0.3);
        }

        .free-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(46, 204, 113, 0.1);
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            min-width: 45px;
            text-align: center;
            font-weight: 500;
            color: #27ae60;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .password-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(52, 152, 219, 0.1);
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            min-width: 45px;
            text-align: center;
            font-weight: 500;
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.2);
            margin-left: 5px;
        }

        .coin-box .fs-7,
        .free-box .fs-7,
        .password-box .fs-7 {
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        /* Các style khác giữ nguyên */

        li {
            position: relative;
        }

        li .chapter-read+hr {
            opacity: 0 !important;
        }

        .stats-list-chapter {
            display: flex;
            flex-direction: row;
            gap: 0.8rem;
        }

        .counter-chapter {
            font-weight: bold;
            margin-right: 5px;
            transition: all 0.3s ease-out;
        }

        .stat-item-chapter {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }

        .new-badge {
            color: #e74c3c;
            font-weight: 500;
            margin-left: 5px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            animation: pulse 1s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes glow {
            0% {
                text-shadow: 0 0 0px #f1c40f;
            }

            50% {
                text-shadow: 0 0 8px #f1c40f;
            }

            100% {
                text-shadow: 0 0 0px #f1c40f;
            }
        }
    </style>
@endpush
