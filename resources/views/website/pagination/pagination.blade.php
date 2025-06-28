@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
    @endphp

    <nav class="pagination-wrapper flex justify-center items-center mt-4 gap-1 text-sm flex-wrap">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-btn disabled left-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                    <polyline points="15 6 9 12 15 18" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn left-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary-03)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                    <polyline points="15 6 9 12 15 18" />
                </svg>
            </a>
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
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" >
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary-03)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                    <polyline points="9 6 15 12 9 18" />
                </svg>
            </a>
        @else
            <span class="pagination-btn disabled">
                 <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                    <polyline points="9 6 15 12 9 18" />
                </svg>
            </span>
        @endif
    </nav>
@endif
