<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Manajemen Pengumuman</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Buat, edit, dan publish pengumuman resmi sekolah untuk murid</p>
        </div>

        <button wire:click="openCreate" type="button" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-sm font-semibold shadow-md self-start sm:self-auto">
            + Buat Pengumuman
        </button>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium flex items-center justify-between">
            <span>✓ {{ $successMessage }}</span>
            <button type="button" wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Announcement Table -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--color-border)] text-xs font-semibold text-[var(--color-text-muted)] uppercase bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5">Judul Pengumuman</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Tanggal Publish</th>
                        <th class="px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)] text-xs">
                    @forelse($announcements as $announcement)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-[var(--color-text)] mb-0.5">{{ $announcement->title }}</div>
                                <div class="text-[var(--color-text-muted)] text-xs line-clamp-1 max-w-md">{{ Str::limit($announcement->content, 80) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($announcement->status->value === 'published')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        ● Published
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        ○ Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[var(--color-text-muted)]">
                                {{ $announcement->published_at ? $announcement->published_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="toggleStatus({{ $announcement->id }})" type="button" title="{{ $announcement->status->value === 'published' ? 'Jadikan Draft' : 'Publish Sekarang' }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ $announcement->status->value === 'published' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300' }} transition-all">
                                        {{ $announcement->status->value === 'published' ? 'Unpublish' : 'Publish' }}
                                    </button>
                                    <button wire:click="openEdit({{ $announcement->id }})" type="button" title="Edit Pengumuman" class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-[var(--color-primary)] hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteAnnouncement({{ $announcement->id }})" wire:confirm="Hapus pengumuman ini?" type="button" title="Hapus Pengumuman" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-[var(--color-text-muted)]">Belum ada pengumuman. Klik "+ Buat Pengumuman" untuk membuat baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-border)]">
            {{ $announcements->links() }}
        </div>
    </div>

    <!-- Create/Edit Form Modal -->
    @if($showFormModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-xl w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-lg text-[var(--color-text)]">{{ $announcementId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h3>
                    <button wire:click="$set('showFormModal', false)" type="button" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Judul Pengumuman</label>
                        <input type="text" wire:model="title" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Contoh: Pengumuman Libur Hari Raya">
                        @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Isi Pengumuman</label>
                        <textarea wire:model="content" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="Tuliskan isi pengumuman secara jelas di sini..."></textarea>
                        @error('content') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-[var(--color-border)]">
                        <button wire:click="$set('showFormModal', false)" type="button" class="w-full sm:w-1/3 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-[var(--color-text)]">
                            Batal
                        </button>
                        <button wire:click="save('draft')" type="button" class="w-full sm:w-1/3 py-2.5 rounded-xl bg-slate-600 hover:bg-slate-700 text-white text-xs font-semibold shadow-sm transition-all">
                            Simpan Draft
                        </button>
                        <button wire:click="save('published')" type="button" class="w-full sm:w-1/3 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all">
                            🚀 Publish Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
