<style>
    .pagination_2 {
        display: flex;
        justify-content: space-between;
        list-style-type: none;
        padding: 0;
    }

    .pagination_2 li {
        display: inline;
    }

    .pagination_2 li a {
        text-decoration: none;
        color: black;
    }

    .pagination_2 li.disabled {
        color: grey;
    }

    .pagination_2 .page-item {
        padding-left: 10px;
        margin-top: 19px;
    }

    .pagination_2 .page-link {
        background-color: rgb(71, 186, 117);
        border: 1px none;
        border-radius: 4px;
        cursor: pointer;
        padding: 5px 13px;
        color: #ffff;
    }

    .pagination_2 .page-link.active {
        background-color: rgb(19, 108, 55);
    }
</style>
@if ($paginator->hasPages())
    <ul class="pagination_2">
        {{-- Link Previous --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">&laquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->appends(request()->except('page'))->previousPageUrl() }}"
                    rel="prev">&laquo;</a>
            </li>
        @endif

        {{-- Link Next --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->appends(request()->except('page'))->nextPageUrl() }}"
                    rel="next">&raquo;</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">&raquo;</span>
            </li>
        @endif
    </ul>
@endif
