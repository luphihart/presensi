<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-[var(--color-text)]">Pengaturan Profil</h2>
        <a href="{{ route('student.dashboard') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]">← Kembali</a>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <x-ui.card>
        <form wire:submit="updateProfile" class="space-y-5">
            <!-- Photo Preview -->
            <div class="flex flex-col items-center space-y-3 py-2">
                <div class="w-24 h-24 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold text-3xl flex items-center justify-center overflow-hidden border-2 border-[var(--color-primary)] shadow-md">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                    @elseif($student?->profile_photo_path)
                        <img src="{{ asset('storage/' . $student->profile_photo_path) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <input type="file" wire:model="photo" id="photo_input" class="hidden">
                <label for="photo_input" class="cursor-pointer text-xs font-semibold text-[var(--color-primary)] bg-[var(--color-primary-soft)] px-4 py-2 rounded-xl">
                    Ganti Foto Profil
                </label>
                @error('photo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp</label>
                <input type="text" wire:model="phone" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="081234567890">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Alamat Tempat Tinggal</label>
                <textarea wire:model="address" rows="2" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none"></textarea>
                @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-2">Pilihan Tema Tampilan</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" wire:click="$set('themePreference', 'light')" class="border rounded-2xl p-3 flex flex-col items-center justify-center space-y-1.5 transition-all {{ $themePreference === 'light' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-500 text-amber-600 dark:text-amber-400 font-bold shadow-sm' : 'border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text-muted)] hover:text-[var(--color-text)]' }}">
                        <span class="text-2xl">☀️</span>
                        <span class="text-xs font-semibold">Terang</span>
                    </button>

                    <button type="button" wire:click="$set('themePreference', 'dark')" class="border rounded-2xl p-3 flex flex-col items-center justify-center space-y-1.5 transition-all {{ $themePreference === 'dark' ? 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text-muted)] hover:text-[var(--color-text)]' }}">
                        <span class="text-2xl">🌙</span>
                        <span class="text-xs font-semibold">Gelap</span>
                    </button>

                    <button type="button" wire:click="$set('themePreference', 'system')" class="border rounded-2xl p-3 flex flex-col items-center justify-center space-y-1.5 transition-all {{ $themePreference === 'system' ? 'bg-slate-100 dark:bg-slate-800 border-slate-500 text-slate-800 dark:text-slate-200 font-bold shadow-sm' : 'border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text-muted)] hover:text-[var(--color-text)]' }}">
                        <span class="text-2xl">💻</span>
                        <span class="text-xs font-semibold">Sistem HP</span>
                    </button>
                </div>
            </div>

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                Simpan Perubahan
            </x-ui.button>
        </form>
    </x-ui.card>

    <div class="pt-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 font-semibold text-sm border border-rose-200 dark:border-rose-900">
                Keluar Dari Akun
            </button>
        </form>
    </div>
</div>
