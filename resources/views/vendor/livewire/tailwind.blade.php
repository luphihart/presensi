<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between flex-col sm:flex-row gap-4 py-2">
            <!-- Results Count Info -->
            <div class="text-xs text-[var(--color-text-muted)]">
                Menampilkan <span class="font-bold text-[var(--color-text)]">{{ $paginator->firstItem() }}</span> 
                sampai <span class="font-bold text-[var(--color-text)]">{{ $paginator->lastItem() }}</span> 
                dari <span class="font-bold text-[var(--color-text)]">{{ $paginator->total() }}</span> data
            </div>

            <!-- Page Buttons -->
            <div class="flex items-center space-x-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1.5 rounded-xl border border-[var(--color-border)] bg-slate-100 dark:bg-slate-800/40 text-[var(--color-text-muted)] text-xs font-semibold cursor-not-allowed opacity-50 select-none">
                        ← Prev
                    </span>
                @else
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" type="button" class="px-3 py-1.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                        ← Prev
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2 py-1.5 text-xs text-[var(--color-text-muted)] font-bold">...</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3.5 py-1.5 rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold shadow-md select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" type="button" class="px-3 py-1.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" type="button" class="px-3 py-1.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                        Next →
                    </button>
                @else
                    <span class="px-3 py-1.5 rounded-xl border border-[var(--color-border)] bg-slate-100 dark:bg-slate-800/40 text-[var(--color-text-muted)] text-xs font-semibold cursor-not-allowed opacity-50 select-none">
                        Next →
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
