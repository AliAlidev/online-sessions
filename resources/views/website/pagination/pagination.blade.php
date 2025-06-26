@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
    @endphp

    <nav class="pagination-wrapper flex justify-center items-center mt-4 gap-1 text-sm flex-wrap">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-btn disabled">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn">‹</a>
        @endif

        {{-- Show on small screens: only 3 main pages --}}
        {{-- Example: ‹ ... 3 4 5 ... › --}}
        <span class="pagination-responsive sm:hidden">
            @if ($current > 2)
                <span class="pagination-btn disabled">...</span>
            @endif

            @for ($i = max(1, $current - 1); $i <= min($last, $current + 1); $i++)
                @if ($i == $current)
                    <span class="pagination-btn active">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->url($i) }}" class="pagination-btn">{{ $i }}</a>
                @endif
            @endfor

            @if ($current < $last - 1)
                <span class="pagination-btn disabled">...</span>
            @endif
        </span>

        {{-- Show full pagination on larger screens --}}
        <span class="hidden sm:flex gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-btn disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $current)
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </span>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn">›</a>
        @else
            <span class="pagination-btn disabled">›</span>
        @endif
    </nav>
@endif
