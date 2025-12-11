@if ($paginator->hasPages())
    <nav class="flex items-center justify-between mt-4">
        {{-- Tombol Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-gray-400">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 text-purple-600">‹</a>
        @endif

        {{-- Nomor Halaman --}}
        <span class="mx-2">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1 bg-purple-600 text-white rounded">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 text-purple-600">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </span>

        {{-- Tombol Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 text-purple-600">›</a>
        @else
            <span class="px-3 py-1 text-gray-400">›</span>
        @endif
    </nav>
@endif
