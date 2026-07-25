<div class="relative">
    <button wire:click="toggle" type="button" class="w-10 h-10 rounded-2xl bg-[var(--color-surface)] border border-[var(--color-border)] flex items-center justify-center text-[var(--color-text)] shadow-sm relative hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-rose-500 text-white font-bold text-[10px] flex items-center justify-center animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    @if($isOpen)
        <div class="absolute right-0 mt-3 w-80 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl shadow-2xl z-50 p-4 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-[var(--color-border)]">
                <h4 class="font-bold text-sm text-[var(--color-text)]">Notifikasi</h4>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-[var(--color-primary)] font-semibold hover:underline">
                        Tandai Semua Dibaca
                    </button>
                @endif
            </div>

            <div class="space-y-2 max-h-80 overflow-y-auto">
                @forelse($notifications as $item)
                    <div wire:click="markAsRead({{ $item->id }})" class="p-3 rounded-xl cursor-pointer text-xs space-y-1 transition-all {{ $item->is_read ? 'opacity-70 bg-slate-50 dark:bg-slate-800/30' : 'bg-[var(--color-primary-soft)] border-l-2 border-[var(--color-primary)]' }}">
                        <p class="font-bold text-[var(--color-text)]">{{ $item->title }}</p>
                        <p class="text-[var(--color-text-muted)] leading-relaxed">{{ $item->body }}</p>
                        <p class="text-[10px] text-[var(--color-text-muted)] pt-0.5">{{ $item->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-xs text-center text-[var(--color-text-muted)] py-4">Belum ada notifikasi.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
