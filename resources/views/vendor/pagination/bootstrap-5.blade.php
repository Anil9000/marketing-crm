@if ($paginator->hasPages())
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 px-1">
    <div class="text-muted" style="font-size:0.8rem;">
        Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> results
    </div>
    <div class="d-flex gap-1 flex-wrap align-items-center">

        @if ($paginator->onFirstPage())
            <span class="btn btn-sm btn-outline-secondary disabled" style="font-size:0.8rem;opacity:0.4;pointer-events:none;">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary" style="font-size:0.8rem;">Prev</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="btn btn-sm btn-outline-secondary disabled" style="font-size:0.8rem;opacity:0.5;pointer-events:none;">...</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn btn-sm" style="background:#6366f1;color:#fff;border:1px solid #6366f1;font-size:0.8rem;min-width:32px;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn btn-sm btn-outline-secondary" style="font-size:0.8rem;min-width:32px;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm btn-outline-secondary" style="font-size:0.8rem;">Next</a>
        @else
            <span class="btn btn-sm btn-outline-secondary disabled" style="font-size:0.8rem;opacity:0.4;pointer-events:none;">Next</span>
        @endif

    </div>
</div>
@endif
