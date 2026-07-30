<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Kelola Wali Kelas</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Tambah akun wali kelas (format email NIP@walikelas.com) dan assign ke kelas</p>
        </div>

        <button wire:click="openCreate" type="button" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-sm font-semibold shadow-md self-start sm:self-auto hover:opacity-90 transition-all">
            + Tambah Wali Kelas
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

    <!-- Table List Wali Kelas -->
    <x-ui.card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-[var(--color-border)] text-xs text-[var(--color-text-muted)] uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">NIP / Username</th>
                        <th class="px-6 py-4">Kelas Diampu</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @forelse($waliKelasList as $wk)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-all">
                            <td class="px-6 py-4 font-semibold text-[var(--color-text)]">
                                {{ $wk->name }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-[var(--color-text-muted)]">
                                <div>NIP: <strong class="text-[var(--color-text)]">{{ $wk->nip ?? '-' }}</strong></div>
                                <div class="text-[11px] opacity-75">{{ $wk->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($wk->homeroomClass)
                                    <x-ui.badge type="success" :value="'🏫 ' . $wk->homeroomClass->name" />
                                @else
                                    <x-ui.badge type="neutral" value="Belum di-assign" />
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $wk->id }})" type="button" class="inline-flex items-center">
                                    @if($wk->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">Non-Aktif</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="resetPassword({{ $wk->id }})" wire:confirm="Reset password wali kelas ini ke walikelas123?" type="button" title="Reset Password" class="px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-300 text-xs font-semibold hover:bg-amber-100 transition-all">
                                    🔑 Reset Pass
                                </button>
                                <button wire:click="openEdit({{ $wk->id }})" type="button" class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-[var(--color-primary)] hover:bg-indigo-100 transition-all">
                                    ✏️ Edit
                                </button>
                                <button wire:click="delete({{ $wk->id }})" wire:confirm="Hapus Wali Kelas ini?" type="button" class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 transition-all">
                                    🗑️ Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-[var(--color-text-muted)]">
                                Belum ada akun Wali Kelas yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Create / Edit Modal Pop-up -->
    @if($showFormModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-lg text-[var(--color-text)]">{{ $waliKelasId ? 'Edit Wali Kelas' : 'Tambah Wali Kelas Baru' }}</h3>
                    <button wire:click="$set('showFormModal', false)" type="button" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Nama Lengkap Wali Kelas</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="Contoh: Drs. Budi Santoso, M.Pd">
                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" wire:model.live="nip" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="Contoh: 198501012010011001">
                        @if($nip)
                            <p class="text-[11px] text-[var(--color-text-muted)] mt-1">Username Login: <strong class="text-[var(--color-primary)] font-mono">{{ trim($nip) }}@walikelas.com</strong></p>
                        @endif
                        @error('nip') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if(!$waliKelasId)
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-xs text-blue-700 dark:text-blue-300">
                            ℹ️ Password awal otomatis diset ke: <strong class="font-mono">walikelas123</strong>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Assign Ke Kelas (Opsional)</label>
                        <select wire:model="classRoomId" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                            <option value="">-- Belum Assign Kelas --</option>
                            @foreach($availableClasses as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }} {{ $class->wali_kelas_id && $class->wali_kelas_id != $waliKelasId ? '(Diampu oleh ' . ($class->waliKelas->name ?? 'lain') . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('classRoomId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3 pt-3 border-t border-[var(--color-border)]">
                        <button wire:click="$set('showFormModal', false)" type="button" class="w-1/2 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-[var(--color-text)]">
                            Batal
                        </button>
                        <x-ui.button type="submit" variant="primary" size="md" class="w-1/2">
                            Simpan Wali Kelas
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
