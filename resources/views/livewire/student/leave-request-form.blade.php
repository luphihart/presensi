<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-[var(--color-text)]">Pengajuan Izin / Sakit</h2>
        <a href="{{ route('student.dashboard') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]">← Kembali</a>
    </div>

    <!-- Segmented Tab Navigation -->
    <div class="flex bg-[var(--color-surface)] border border-[var(--color-border)] p-1 rounded-2xl shadow-sm">
        <button wire:click="$set('activeTab', 'create')" type="button" class="w-1/2 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'create' ? 'bg-[var(--color-primary)] text-white shadow-md' : 'text-[var(--color-text-muted)] hover:text-[var(--color-text)]' }}">
            Ajukan Baru
        </button>
        <button wire:click="$set('activeTab', 'history')" type="button" class="w-1/2 py-2.5 rounded-xl text-xs font-semibold transition-all {{ $activeTab === 'history' ? 'bg-[var(--color-primary)] text-white shadow-md' : 'text-[var(--color-text-muted)] hover:text-[var(--color-text)]' }}">
            Riwayat Pengajuan
        </button>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    @if($activeTab === 'create')
        <!-- Form Ajukan Baru -->
        <x-ui.card>
            <form wire:submit="submitLeave" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-2">Pilih Jenis Pengajuan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border border-[var(--color-border)] rounded-2xl p-4 flex items-center space-x-3 cursor-pointer {{ $type === 'izin' ? 'bg-[var(--color-primary-soft)] border-[var(--color-primary)] text-[var(--color-primary)]' : 'bg-[var(--color-bg)]' }}">
                            <input type="radio" wire:model.live="type" value="izin" class="hidden">
                            <span class="text-xl">📝</span>
                            <div>
                                <p class="text-sm font-bold">Izin</p>
                                <p class="text-xs text-[var(--color-text-muted)]">Keperluan keluarga/lainnya</p>
                            </div>
                        </label>

                        <label class="border border-[var(--color-border)] rounded-2xl p-4 flex items-center space-x-3 cursor-pointer {{ $type === 'sakit' ? 'bg-[var(--color-primary-soft)] border-[var(--color-primary)] text-[var(--color-primary)]' : 'bg-[var(--color-bg)]' }}">
                            <input type="radio" wire:model.live="type" value="sakit" class="hidden">
                            <span class="text-xl">🏥</span>
                            <div>
                                <p class="text-sm font-bold">Sakit</p>
                                <p class="text-xs text-[var(--color-text-muted)]">Kondisi kesehatan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Tanggal Tidak Masuk</label>
                    <input type="date" wire:model="date" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    @error('date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Alasan / Keterangan</label>
                    <textarea wire:model="reason" rows="3" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none" placeholder="Jelaskan alasan pengajuan secara singkat..."></textarea>
                    @error('reason') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Lampiran Bukti (Opsional)</label>
                    <input type="file" wire:model="attachment" class="w-full text-xs text-[var(--color-text-muted)] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[var(--color-primary-soft)] file:text-[var(--color-primary)] hover:file:opacity-90">
                    <p class="text-xs text-[var(--color-text-muted)] mt-1">Format: JPG, PNG, PDF (Maks. 2MB)</p>
                    @error('attachment') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                    Kirim Pengajuan
                </x-ui.button>
            </form>
        </x-ui.card>
    @else
        <!-- List Riwayat Pengajuan -->
        <div class="space-y-3">
            @forelse($history as $item)
                <x-ui.card class="p-4 flex items-center justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-sm text-[var(--color-text)]">{{ $item->type->label() }}</span>
                            <x-ui.badge :type="strtolower($item->status->value)" :value="$item->status->label()" />
                        </div>
                        <p class="text-xs text-[var(--color-text-muted)]">Tanggal: {{ $item->date->isoFormat('D MMMM YYYY') }}</p>
                        <p class="text-xs text-[var(--color-text)] line-clamp-2">"{{ $item->reason }}"</p>
                        @if($item->review_note)
                            <p class="text-xs text-amber-600 dark:text-amber-400 italic">Catatan Admin: {{ $item->review_note }}</p>
                        @endif
                    </div>
                </x-ui.card>
            @empty
                <x-ui.card class="text-center py-8">
                    <p class="text-sm text-[var(--color-text-muted)]">Belum ada riwayat pengajuan izin.</p>
                </x-ui.card>
            @endforelse
        </div>
    @endif
</div>
