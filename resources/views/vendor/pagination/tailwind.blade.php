@if ($paginator->hasPages())
    @php
        $currentPage  = $paginator->currentPage();
        $lastPage     = $paginator->lastPage();
        $window       = 1; // pages shown on each side of current

        // Build the page list with smart ellipsis
        $pages = collect();

        for ($i = 1; $i <= $lastPage; $i++) {
            if (
                $i === 1 ||                          // always show first
                $i === $lastPage ||                  // always show last
                abs($i - $currentPage) <= $window    // within window
            ) {
                $pages->push($i);
            }
        }

        // Insert '...' where gaps exist
        $rendered = [];
        $prev = null;
        foreach ($pages as $p) {
            if ($prev !== null && $p - $prev > 1) {
                $rendered[] = '...';
            }
            $rendered[] = $p;
            $prev = $p;
        }
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
         class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-2">

        {{-- Info teks: Menampilkan X sampai Y dari Z data --}}
        <p class="text-xs text-gray-500 dark:text-gray-400 text-center sm:text-left">
            @if ($paginator->firstItem())
                Menampilkan
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $paginator->firstItem() }}</span>
                sampai
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $paginator->lastItem() }}</span>
                dari
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $paginator->total() }}</span>
                data
            @else
                {{ $paginator->count() }} data
            @endif
        </p>

        {{-- Navigasi halaman --}}
        <div class="flex items-center justify-center gap-1" wire:ignore.self>

            {{-- Tombol Prev --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed select-none dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                    ← Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-800 transition dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                   wire:navigate>
                    ← Prev
                </a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($rendered as $item)
                @if ($item === '...')
                    <span class="px-2 py-1.5 text-xs text-gray-400 select-none">…</span>
                @elseif ($item === $currentPage)
                    <span aria-current="page"
                          class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-indigo-600 border border-indigo-600 rounded-lg shadow-sm">
                        {{ $item }}
                    </span>
                @else
                    <a href="{{ $paginator->url($item) }}"
                       class="inline-flex items-center justify-center w-8 h-8 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-800 transition dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                       aria-label="{{ __('Go to page :page', ['page' => $item]) }}"
                       wire:navigate>
                        {{ $item }}
                    </a>
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-800 transition dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                   wire:navigate>
                    Next →
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed select-none dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                    Next →
                </span>
            @endif

        </div>
    </nav>
@endif
