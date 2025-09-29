@props(['paginator'])

@if ($paginator->hasPages())
    <nav aria-label="Page navigation" class="pagination-container">
        <ul class="pagination pagination-sm flex-wrap justify-content-center gap-2">
            {{-- First Page Link --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="First">
                    <i class="fas fa-angle-double-left"></i>
                </a>
            </li>

            {{-- Previous Page Link --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>

            {{-- Numbered pages --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $delta = 2;
            @endphp

            @for ($i = 1; $i <= $lastPage; $i++)
                @if ($i == 1 || $i == $lastPage || ($i >= $currentPage - $delta && $i <= $currentPage + $delta))
                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @elseif ($i == $currentPage - $delta - 1 || $i == $currentPage + $delta + 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endfor

            {{-- Next Page Link --}}
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>

            {{-- Last Page Link --}}
            <li class="page-item {{ $currentPage == $lastPage ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->url($lastPage) }}" aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@endif

@once
    @push('styles')
        <style>
            .pagination-container {
                margin: 2rem 0;
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .pagination {
                display: flex;
                list-style: none;
                padding: 0;
                margin: 0;
                flex-wrap: wrap;
                gap: 5px;
            }

            .page-item {
                margin: 0 2px;
            }

            .page-link {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 36px;
                height: 36px;
                padding: 0 12px;
                font-size: 0.875rem;
                line-height: 1.5;
                color: var(--text-color, #333);
                background-color: #fff;
                border: 1px solid var(--primary-color-4, #e4e7ea);
                border-radius: 4px;
                transition: all 0.2s ease;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }

            .page-link:hover {
                color: #fff;
                background-color: var(--primary-color-2);
                border-color: var(--primary-color);
                transform: translateY(-2px);
                box-shadow: 0 3px 5px var(--primary-color-1);
            }

            .page-item.active .page-link {
                color: #fff;
                background-color: var(--primary-color-2);
                border-color: var(--primary-color-3);
                font-weight: 600;
                transform: scale(1.05);
                box-shadow: 0 3px 8px var(--primary-color-1);
            }

            .page-item.disabled .page-link {
                color: #9aa0a6;
                pointer-events: none;
                background-color: var(--primary-color-5, #f5f5f7);
                border-color: var(--primary-color-4);
                box-shadow: none;
                opacity: 0.7;
            }

            /* Icon styling */
            .page-link i {
                font-size: 12px;
                line-height: 1;
            }

            /* Mobile responsive */
            @media (max-width: 576px) {
                .pagination {
                    gap: 3px;
                }

                .page-link {
                    min-width: 32px;
                    height: 32px;
                    padding: 0 8px;
                    font-size: 0.8rem;
                }
            }

            /* Animation for page changes */
            .pagination .page-item {
                transition: transform 0.15s ease-in-out;
            }

            .pagination .page-item:active {
                transform: scale(0.95);
            }

            /* Hover effect for pagination items */
            .pagination .page-item:not(.active):not(.disabled):hover .page-link {
                background: var(--primary-color-3);
                color: #fff;
            }

            /* Custom styles for first/last page buttons */
            .pagination .page-item:first-child .page-link,
            .pagination .page-item:last-child .page-link {
                font-weight: 600;
            }

            /* Optional: Theme styles - Light & Dark mode compatibility */
            @media (prefers-color-scheme: dark) {
                .page-link {
                    background-color: var(--primary-color-3);
                    color: #fff;
                    border-color: var(--primary-color-2);
                }
                
                .page-item.disabled .page-link {
                    background-color: var(--primary-color-4);
                    color: rgba(255, 255, 255, 0.7);
                    border-color: var(--primary-color-3);
                }
                
                .pagination .page-item:not(.active):not(.disabled):hover .page-link {
                    background: var(--primary-color-3);
                    color: #fff;
                }
                
                .page-item.active .page-link {
                    background-color: var(--primary-color-2);
                    border-color: var(--primary-color-1);
                    box-shadow: 0 3px 8px var(--primary-color-1);
                }
            }
        </style>
    @endpush
@endonce
