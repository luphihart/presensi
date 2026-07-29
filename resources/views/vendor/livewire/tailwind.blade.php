<div>
    @if ($paginator->hasPages())
        @php
            $currentPage = $paginator->currentPage();
            $lastPage    = $paginator->lastPage();

            // Build a very compact page list: page 1, last page, and current ± 1
            $pages = collect();
            for ($i = 1; $i <= $lastPage; $i++) {
                if (
                    $i === 1 || 
                    $i === $lastPage || 
                    abs($i - $currentPage) <= 1
                ) {
                    $pages->push($i);
                }
            }

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

        <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 py-2">
            <!-- Results Count Info -->
            <div class="text-xs text-[var(--color-text-muted)] text-center sm:text-left">
                Menampilkan <span class="font-bold text-[var(--color-text)]">{{ $paginator->firstItem() }}</span> 
                sampai <span class="font-bold text-[var(--color-text)]">{{ $paginator->lastItem() }}</span> 
                dari <span class="font-bold text-[var(--color-text)]">{{ $paginator->total() }}</span> data
            </div>

            <!-- Page Buttons -->
            <div class="flex items-center justify-center flex-wrap gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-2.5 py-1.5 rounded-xl border border-[var(--color-border)] bg-slate-100 dark:bg-slate-800/40 text-[var(--color-text-muted)] text-xs font-semibold cursor-not-allowed opacity-50 select-none">
                        ← Prev
                    </span>
                @else
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" type="button" class="px-2.5 py-1.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                        ← Prev
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($rendered as $item)
                    @if ($item === '...')
                        <span class="px-1.5 py-1.5 text-xs text-[var(--color-text-muted)] font-bold select-none">...</span>
                    @elseif ($item == $currentPage)
                        <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold shadow-md select-none">
                            {{ $item }}
                        </span>
                    @else
                        <button wire:click="gotoPage({{ $item }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" type="button" class="w-8 h-8 flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                            {{ $item }}
                        </button>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" type="button" class="px-2.5 py-1.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                        Next →
                    </button>
                @else
                    <span class="px-2.5 py-1.5 rounded-xl border border-[var(--color-border)] bg-slate-100 dark:bg-slate-800/40 text-[var(--color-text-muted)] text-xs font-semibold cursor-not-allowed opacity-50 select-none">
                        Next →
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
