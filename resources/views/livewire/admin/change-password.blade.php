<div class="max-w-xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-text)]">Ubah Password Admin</h2>
        <p class="text-sm text-[var(--color-text-muted)]">Perbarui kata sandi akun administrator Anda</p>
    </div>

    @if ($successMessage)
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-sm font-medium flex items-center justify-between">
            <span>✓ {{ $successMessage }}</span>
            <button type="button" wire:click="$set('successMessage', null)" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <x-ui.card>
        <form wire:submit="changePassword" class="space-y-5" x-data="{ showPasswords: false }">
            <div>
                <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Password Saat Ini</label>
                <div class="relative">
                    <input :type="showPasswords ? 'text' : 'password'" wire:model="current_password"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none text-sm">
                </div>
                @error('current_password')
                    <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Password Baru</label>
                <div class="relative">
                    <input :type="showPasswords ? 'text' : 'password'" wire:model="password"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none text-sm">
                </div>
                @error('password')
                    <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input :type="showPasswords ? 'text' : 'password'" wire:model="password_confirmation"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center gap-2 text-xs text-[var(--color-text-muted)] cursor-pointer">
                    <input type="checkbox" x-model="showPasswords" class="rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                    Tampilkan password
                </label>

                <x-ui.button type="submit" variant="primary">
                    Simpan Password
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
