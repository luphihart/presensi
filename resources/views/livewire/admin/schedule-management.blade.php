<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-text)]">Pengaturan Jam Masuk & Jadwal Sekolah</h2>
        <p class="text-sm text-[var(--color-text-muted)]">Atur jam masuk, jam pulang, dan toleransi keterlambatan per hari</p>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <x-ui.card>
        <form wire:submit="saveSchedules" class="space-y-6">
            <div class="divide-y divide-[var(--color-border)]">
                @foreach($schedulesData as $day => $item)
                    <div class="py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="w-32">
                            <label class="flex items-center space-x-2 font-bold text-sm text-[var(--color-text)] cursor-pointer">
                                <input type="checkbox" wire:model="schedulesData.{{ $day }}.is_school_day" class="rounded text-[var(--color-primary)]">
                                <span>{{ $item['day_name'] }}</span>
                            </label>
                        </div>

                        <div class="flex-1 grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Jam Masuk</label>
                                <input type="time" wire:model="schedulesData.{{ $day }}.check_in_time" class="w-full px-3 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Toleransi (Mnt)</label>
                                <input type="number" wire:model="schedulesData.{{ $day }}.check_in_tolerance_minutes" min="0" max="60" class="w-full px-3 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Jam Pulang</label>
                                <input type="time" wire:model="schedulesData.{{ $day }}.check_out_time" class="w-full px-3 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                Simpan Jadwal Sekolah
            </x-ui.button>
        </form>
    </x-ui.card>
</div>
