@if ($paginator->hasPages())
    <nav class="flex items-center justify-between mt-4">
        <div class="text-sm text-gray-600">
            Halaman <span class="font-bold">{{ $paginator->currentPage() }}</span>
            dari <span class="font-bold">{{ $paginator->lastPage() }}</span>
        </div>

        <ul class="flex items-center space-x-2">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <li class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&lt;</li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" 
                       class="px-3 py-1 bg-white text-purple-600 border border-purple-600 hover:bg-purple-600 hover:text-white rounded">
                        &lt;
                    </a>
                </li>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator ("...") --}}
                @if (is_string($element))
                    <li class="px-3 py-1 text-gray-400">{{ $element }}</li>
                @endif

                {{-- Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="px-3 py-1 bg-purple-600 text-white rounded font-bold">{{ $page }}</li>
                        @else
                            <li>
                                <a href="{{ $url }}" 
                                   class="px-3 py-1 bg-white text-purple-600 border border-purple-600 hover:bg-purple-600 hover:text-white rounded">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" 
                       class="px-3 py-1 bg-white text-purple-600 border border-purple-600 hover:bg-purple-600 hover:text-white rounded">
                        &gt;
                    </a>
                </li>
            @else
                <li class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">&gt;</li>
            @endif
        </ul>
    </nav>
@endif
