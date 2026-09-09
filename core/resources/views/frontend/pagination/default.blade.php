@if ($paginator->hasPages())
    <div class="pagination-area text-center">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <a class="prev disabled pagination-btn" href="#" style="pointer-events: none;">
                        <span class="d-none d-sm-inline">{{ __('Previous') }}</span>
                        <i class="fa-solid fa-chevron-left d-inline d-sm-none"></i>
                    </a>
                </li>
            @else
                <li>
                    <a class="prev pagination-btn active" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <span class="d-none d-sm-inline">{{ __('Previous') }}</span>
                        <i class="fa-solid fa-chevron-left d-inline d-sm-none"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @php
                        $current = $paginator->currentPage();
                        $last = $paginator->lastPage();
                        $range = 1;
                        $start = max(1, $current - $range);
                        $end = min($last, $current + $range);
                    @endphp

                    @if ($start > 1)
                        <li>
                            <a class="pagination-btn disabled" href="#" style="pointer-events: none;">...</a>
                        </li>
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $current)
                            <li>
                                <a class="pagination-btn active" href="#">{{ $page }}</a>
                            </li>
                        @else
                            <li>
                                <a class="pagination-btn" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    @if ($end < $last)
                        <li>
                            <a class="pagination-btn disabled" href="#" style="pointer-events: none;">...</a>
                        </li>
                    @endif
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a class="next pagination-btn active" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <span class="d-none d-sm-inline">{{ __('Next') }}</span>
                        <i class="fa-solid fa-chevron-right d-inline d-sm-none"></i>
                    </a>
                </li>
            @else
                <li>
                    <a class="next disabled pagination-btn" href="#" style="pointer-events: none;">
                        <span class="d-none d-sm-inline">{{ __('Next') }}</span>
                        <i class="fa-solid fa-chevron-right d-inline d-sm-none"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif
