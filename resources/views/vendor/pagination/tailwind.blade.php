@if ($paginator->hasPages())
    <div class="flex justify-center mt-4">
        <ul class="flex flex-row items-center gap-1"> <!-- Changed to flex-row with small gap -->
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded cursor-not-allowed">&laquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" 
                       class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 block">
                        &laquo;
                    </a>
                </li>
            @endif

            {{-- Pagination Numbers --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-1 bg-blue-600 text-white rounded block">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1 bg-white text-blue-600 rounded hover:bg-gray-100 block">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" 
                       class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 block">
                        &raquo;
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded cursor-not-allowed">&raquo;</span>
                </li>
            @endif
        </ul>
    </div>
@endif