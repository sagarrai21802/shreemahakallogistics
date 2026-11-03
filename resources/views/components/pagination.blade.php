@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="pagination__link is-disabled">Previous</span>
        @else
            <a class="pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif

        <span class="pagination__status">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="pagination__link is-disabled">Next</span>
        @endif
    </nav>
@endif
