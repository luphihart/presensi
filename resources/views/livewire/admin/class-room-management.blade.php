<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Pengaturan Kelas & Jurusan</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Kelola daftar kelas dan konsentrasi jurusan per tahun ajaran</p>
        </div>

        <button wire:click="openCreate" type="button" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-sm font-semibold shadow-md">
            + Tambah Kelas / Jurusan
        </button>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-medium">
            ⚠️ {{ $errorMessage }}
        </div>
    @endif

    <!-- Class Rooms Grid / Table -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse($classRooms as $class)
            <x-ui.card class="space-y-3 relative">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-muted)]">
                            {{ $class->schoolYear->name ?? 'Tahun Ajaran Active' }}
                        </span>
                        <h3 class="font-bold text-lg text-[var(--color-text)]">{{ $class->name }}</h3>
                        @if($class->major)
                            <x-ui.badge type="info" :value="'Jurusan: ' . $class->major" class="mt-1" />
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        <button wire:click="openEdit({{ $class->id }})" type="button" title="Edit Kelas" class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-[var(--color-primary)] hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="delete({{ $class->id }})" wire:confirm="Hapus kelas ini?" type="button" title="Hapus Kelas" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-[var(--color-border)] flex items-center justify-between text-xs text-[var(--color-text-muted)]">
                    <span>👥 Total Murid</span>
                    <strong class="text-[var(--color-text)] font-semibold">{{ $class->students_count }} Murid</strong>
                </div>
            </x-ui.card>
        @empty
            <x-ui.card class="md:col-span-3 text-center py-12">
                <p class="text-sm text-[var(--color-text-muted)]">Belum ada data kelas atau jurusan yang dibuat.</p>
            </x-ui.card>
        @endforelse
    </div>

    <!-- Create / Edit Modal Pop-up -->
    @if($showFormModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-lg text-[var(--color-text)]">{{ $classRoomId ? 'Edit Kelas & Jurusan' : 'Tambah Kelas Baru' }}</h3>
                    <button wire:click="$set('showFormModal', false)" type="button" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Tahun Ajaran</label>
                        <select wire:model="schoolYearId" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                            @foreach($schoolYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('schoolYearId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Nama Kelas</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Contoh: 10 RPL 1, 11 IPA 2">
                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Nama Jurusan (Opsional)</label>
                        <input type="text" wire:model="major" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Contoh: Rekayasa Perangkat Lunak, IPA, IPS">
                        <p class="text-[10px] text-[var(--color-text-muted)] mt-1">Kosongkan jika sekolah tidak memiliki penjurusan khusus.</p>
                        @error('major') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3 pt-3 border-t border-[var(--color-border)]">
                        <button wire:click="$set('showFormModal', false)" type="button" class="w-1/2 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-[var(--color-text)]">
                            Batal
                        </button>
                        <x-ui.button type="submit" variant="primary" size="md" class="w-1/2">
                            Simpan Kelas
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
