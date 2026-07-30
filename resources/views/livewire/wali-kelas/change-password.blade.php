<div class="max-w-xl mx-auto space-y-6" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-text)]">Ubah Password</h2>
        <p class="text-sm text-[var(--color-text-muted)]">Perbarui password akun Wali Kelas Anda secara berkala untuk menjaga keamanan</p>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-500/30 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center space-x-2 animate-in fade-in duration-300">
            <span class="text-lg">✓</span>
            <span>{{ $successMessage }}</span>
        </div>
    @endif

    <x-ui.card class="p-6">
        <form wire:submit="changePassword" class="space-y-4">
            <!-- Current Password -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Password Saat Ini</label>
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" wire:model="current_password" placeholder="Masukkan password lama..." class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <button type="button" @click="showCurrent = !showCurrent" class="absolute right-3 top-2.5 text-slate-400 hover:text-[var(--color-text)]">
                        <svg x-show="!showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showCurrent" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.18 8.18 0 013.122-.68c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.72-3.72a3 3 0 11-4.243-4.243"/></svg>
                    </button>
                </div>
                @error('current_password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Password Baru</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" wire:model="password" placeholder="Minimal 8 karakter..." class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <button type="button" @click="showNew = !showNew" class="absolute right-3 top-2.5 text-slate-400 hover:text-[var(--color-text)]">
                        <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showNew" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.18 8.18 0 013.122-.68c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.72-3.72a3 3 0 11-4.243-4.243"/></svg>
                    </button>
                </div>
                @error('password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" wire:model="password_confirmation" placeholder="Ketik ulang password baru..." class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-2.5 text-slate-400 hover:text-[var(--color-text)]">
                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showConfirm" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.18 8.18 0 013.122-.68c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.72-3.72a3 3 0 11-4.243-4.243"/></svg>
                    </button>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition-all flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Password Baru</span>
                </button>
            </div>
        </form>
    </x-ui.card>
</div>
