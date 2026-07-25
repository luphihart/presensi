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
        <!-- Form Add Holiday -->
        <x-ui.card class="md:col-span-1">
            <h3 class="font-bold text-base text-[var(--color-text)] mb-4">+ Tambah Hari Libur</h3>
            
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

                <x-ui.button type="submit" variant="primary" size="md" class="w-full">
                    Tambah Tanggal Libur
                </x-ui.button>
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
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($holidays as $h)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="px-4 py-3 font-semibold">{{ $h->date->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-4 py-3 font-bold">{{ $h->name }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :type="$h->type === 'national' ? 'danger' : 'info'" :value="$h->type === 'national' ? 'Nasional' : 'Sekolah'" />
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="deleteHoliday({{ $h->id }})" wire:confirm="Hapus hari libur ini?" type="button" class="text-xs text-rose-600 font-semibold hover:underline">
                                        Hapus
                                    </button>
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
