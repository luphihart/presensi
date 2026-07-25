<div class="max-w-2xl mx-auto space-y-6" x-data="{ isUploading: false, progress: 0 }">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Import Data Murid Massal</h2>
            <p class="text-xs text-[var(--color-text-muted)]">Unggah berkas Excel (.xlsx) untuk menambahkan data murid secara sekaligus</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]">← Kembali</a>
    </div>

    <!-- Download Template Box -->
    <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-3">
            <span class="text-2xl">📊</span>
            <div>
                <h4 class="font-bold text-sm text-[var(--color-primary)]">Belum punya format Excel?</h4>
                <p class="text-xs text-[var(--color-text-muted)]">Unduh template Excel dengan struktur kolom dan petunjuk pengisian yang sudah disesuaikan.</p>
            </div>
        </div>
        <button wire:click="downloadTemplate" type="button" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-xs font-semibold shadow-sm hover:opacity-90 shrink-0 flex items-center space-x-1.5">
            <span>📥 Unduh Template (.xlsx)</span>
        </button>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    @if(count($importErrors) > 0)
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1">
            <p class="font-bold">Catatan / Error Import:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($importErrors as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-ui.card>
        <form wire:submit="import" class="space-y-6"
            x-on:livewire-upload-start="isUploading = true"
            x-on:livewire-upload-finish="isUploading = false"
            x-on:livewire-upload-error="isUploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">

            <div class="border-2 border-dashed border-[var(--color-border)] rounded-2xl p-8 text-center space-y-4">
                <span class="text-4xl">📄</span>
                <div>
                    <h3 class="font-bold text-base text-[var(--color-text)]">Unggah Berkas Excel / CSV</h3>
                    <p class="text-xs text-[var(--color-text-muted)] mt-1">
                        Urutan Kolom Excel: <strong>NIS | Nama Lengkap | Email | Password | Kelas | Gender (L/P) | Tanggal Lahir (YYYY-MM-DD) | No Telp | Alamat</strong>
                    </p>
                </div>

                <input type="file" wire:model="file" class="block w-full text-xs text-[var(--color-text-muted)] mx-auto file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[var(--color-primary-soft)] file:text-[var(--color-primary)] cursor-pointer">
                @error('file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                <!-- File Upload Progress Bar -->
                <div x-show="isUploading" class="space-y-2 pt-2">
                    <div class="flex items-center justify-between text-xs font-semibold text-[var(--color-primary)]">
                        <span>Mengunggah Berkas...</span>
                        <span x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-[var(--color-primary)] transition-all duration-150" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- Import Server Processing Progress Indicator -->
            <div wire:loading wire:target="import" class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 space-y-3">
                <div class="flex items-center space-x-3 text-xs font-bold text-[var(--color-primary)]">
                    <svg class="animate-spin h-5 w-5 text-[var(--color-primary)]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Sedang memproses & memvalidasi data murid dari berkas Excel...</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-indigo-200 dark:bg-indigo-900 overflow-hidden">
                    <div class="h-full bg-[var(--color-primary)] animate-pulse w-full"></div>
                </div>
            </div>

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="import">Proses Import Murid</span>
                <span wire:loading wire:target="import">Memproses Data...</span>
            </x-ui.button>
        </form>
    </x-ui.card>
</div>
