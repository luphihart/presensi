<div class="max-w-xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-text)]">Ubah Password</h2>
        <p class="text-sm text-[var(--color-text-muted)]">Perbarui password akun Wali Kelas Anda</p>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <x-ui.card>
        <form wire:submit="changePassword" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Password saat ini</label>
                <input type="password" wire:model="current_password" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                @error('current_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Password baru</label>
                <input type="password" wire:model="password" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Konfirmasi password baru</label>
                <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
            </div>

            <div class="pt-2">
                <x-ui.button type="submit" variant="primary" class="w-full">
                    Simpan Password Baru
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
