<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Pengaturan Profil Sekolah</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Atur identitas resmi sekolah yang akan tampil di aplikasi murid dan laporan</p>
        </div>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <x-ui.card>
        <form wire:submit="saveSettings" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Nama Resmi Sekolah</label>
                <input type="text" wire:model="schoolName" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="Contoh: SMA Negeri 1 Jakarta">
                <p class="text-[11px] text-[var(--color-text-muted)] mt-1">Nama ini akan tampil di bilah samping (sidebar) aplikasi murid dan judul cetak laporan.</p>
                @error('schoolName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Alamat Lengkap Sekolah</label>
                <textarea wire:model="schoolAddress" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="Contoh: Jl. Pemuda No. 10, Jakarta Pusat"></textarea>
                @error('schoolAddress') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Nomor Telepon / Kontak Sekolah</label>
                <input type="text" wire:model="schoolPhone" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="021-12345678">
                @error('schoolPhone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-3">
                <x-ui.button type="submit" variant="primary" size="md">
                    Simpan Pengaturan
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
