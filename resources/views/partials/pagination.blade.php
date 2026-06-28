@if ($paginator->hasPages())
    <nav class="mca-addr-pagination" aria-label="{{ mca_addr('common.pagination') }}">
        @if ($paginator->onFirstPage())
            <span class="mca-addr-pagination__btn mca-addr-pagination__btn--disabled" aria-hidden="true">&lsaquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="mca-addr-pagination__btn" rel="prev" aria-label="{{ mca_addr('common.previous') }}">&lsaquo;</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="mca-addr-pagination__dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="mca-addr-pagination__btn mca-addr-pagination__btn--active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="mca-addr-pagination__btn" aria-label="{{ mca_addr('common.page', ['page' => $page]) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="mca-addr-pagination__btn" rel="next" aria-label="{{ mca_addr('common.next') }}">&rsaquo;</a>
        @else
            <span class="mca-addr-pagination__btn mca-addr-pagination__btn--disabled" aria-hidden="true">&rsaquo;</span>
        @endif
    </nav>
@endif
