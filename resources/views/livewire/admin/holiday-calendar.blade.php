<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-text)]">Kalender Libur & Tanggal Merah</h2>
        <p class="text-sm text-[var(--color-text-muted)] font-medium">Kelola tanggal merah nasional dan hari libur internal sekolah</p>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Form Add/Edit Holiday -->
        <x-ui.card class="md:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base text-[var(--color-text)]">
                    {{ $editingId ? '✏️ Edit Hari Libur' : '+ Tambah Hari Libur' }}
                </h3>
                @if($editingId)
                    <button wire:click="cancelEdit" type="button" class="text-xs text-slate-500 hover:text-slate-700 underline font-medium">
                        Batal
                    </button>
                @endif
            </div>
            
            <form wire:submit="addHoliday" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Tanggal</label>
                    <input type="date" wire:model="date" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                    @error('date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Nama / Keterangan Libur</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Contoh: Hari Kemerdekaan">
                    @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Kategori Libur</label>
                    <select wire:model="type" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                        <option value="school">Libur Khusus Sekolah</option>
                        <option value="national">Tanggal Merah Nasional</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <x-ui.button type="submit" variant="primary" size="md" class="w-full">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Tanggal Libur' }}
                    </x-ui.button>
                    @if($editingId)
                        <button wire:click="cancelEdit" type="button" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-xs hover:bg-slate-200">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <!-- Holiday List Table -->
        <x-ui.card class="md:col-span-2">
            <h3 class="font-bold text-base text-[var(--color-text)] mb-4">Daftar Hari Libur Terdaftar</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Nama Libur</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($holidays as $h)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 {{ $editingId === $h->id ? 'bg-amber-50/50 dark:bg-amber-950/20' : '' }}">
                                <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ $h->date->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-4 py-3 font-bold">{{ $h->name }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :type="$h->type === 'national' ? 'danger' : 'info'" :value="$h->type === 'national' ? 'Nasional' : 'Sekolah'" />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <button wire:click="editHoliday({{ $h->id }})" type="button" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-100 dark:hover:bg-amber-950/60 transition-all" title="Edit Hari Libur">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:click="deleteHoliday({{ $h->id }})" wire:confirm="Hapus hari libur '{{ addslashes($h->name) }}'?" type="button" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-950/60 transition-all" title="Hapus Hari Libur">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-[var(--color-text-muted)]">Belum ada hari libur terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
