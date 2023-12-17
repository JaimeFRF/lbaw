@section('css')
    <link href="{{ url('css/shop.css') }}" rel="stylesheet">
@endsection

<ul class="pagination">
    {{-- Left Arrow --}}
    @if($paginator->onFirstPage())
        <li class="disabled" aria-disabled="true"><span>&laquo;</span></li>
    @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&laquo;</a></li>
    @endif

    {{-- Pagination Elements --}}
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        {{-- Display up to 3 pages --}}
        @if ($i == $paginator->currentPage() || $i == $paginator->currentPage() - 1 || $i == $paginator->currentPage() + 1)
            <li class="{{ $i == $paginator->currentPage() ? 'active' : '' }}">
                <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
            </li>
        @elseif (($i == $paginator->currentPage() - 2 && $paginator->currentPage() > 3) || ($i == $paginator->currentPage() + 2 && $paginator->currentPage() < $paginator->lastPage() - 2))
            {{-- Display dots before and after --}}
            <li class="pagination-dots" aria-disabled="true"><span>...</span></li>
        @endif
    @endfor

    {{-- Right Arrow --}}
    @if($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&raquo;</a></li>
    @else
        <li class="disabled" aria-disabled="true"><span>&raquo;</span></li>
    @endif
</ul>
