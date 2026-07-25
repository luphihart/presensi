<div class="space-y-6" x-data="{ selectedDate: null, selectedInfo: null }">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-[var(--color-text)]">Riwayat Presensi</h2>
        <a href="{{ route('student.dashboard') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]">← Kembali</a>
    </div>

    <!-- Month Picker Header -->
    <x-ui.card class="flex items-center justify-between p-4">
        <button wire:click="previousMonth" @click="selectedDate = null; selectedInfo = null" type="button" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-[var(--color-text)] font-bold">
            ←
        </button>
        <h3 class="text-base font-bold text-[var(--color-text)]">
            {{ $date->isoFormat('MMMM YYYY') }}
        </h3>
        <button wire:click="nextMonth" @click="selectedDate = null; selectedInfo = null" type="button" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-[var(--color-text)] font-bold">
            →
        </button>
    </x-ui.card>

    <!-- Calendar Grid Card -->
    <x-ui.card>
        <div class="grid grid-cols-7 gap-1 text-center font-semibold text-xs text-[var(--color-text-muted)] mb-3">
            <span class="text-rose-500">Min</span>
            <span>Sen</span>
            <span>Sel</span>
            <span>Rab</span>
            <span>Kam</span>
            <span>Jum</span>
            <span>Sab</span>
        </div>

        <div class="grid grid-cols-7 gap-1.5">
            <!-- Empty offset days -->
            @for($i = 0; $i < $startDayOfWeek; $i++)
                <div class="aspect-square"></div>
            @endfor

            <!-- Month Days -->
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $currentDateObj = $date->copy()->day($d);
                    $currentDateStr = $currentDateObj->format('Y-m-d');
                    $formattedDateLabel = $currentDateObj->isoFormat('D MMMM YYYY');
                    $att = $attendances[$currentDateStr] ?? null;
                    $holiday = $holidays[$currentDateStr] ?? null;
                    $isSunday = $currentDateObj->dayOfWeek === 0;

                    $dotClass = null;
                    $statusLabel = null;
                    $statusType = null;
                    $checkInTime = null;
                    $checkOutTime = null;

                    if ($att) {
                        $statusType = strtolower($att->status->value);
                        $statusLabel = $att->status->label();
                        $checkInTime = $att->check_in_at ? $att->check_in_at->format('H:i') . ' WIB' : null;
                        $checkOutTime = $att->check_out_at ? $att->check_out_at->format('H:i') . ' WIB' : null;

                        $dotClass = match($statusType) {
                            'hadir' => 'bg-emerald-500',
                            'terlambat' => 'bg-amber-500',
                            'izin', 'sakit' => 'bg-cyan-500',
                            'alpa' => 'bg-rose-500',
                            default => 'bg-slate-400'
                        };
                    } elseif ($holiday || $isSunday) {
                        $statusLabel = $holiday ? $holiday->name : 'Hari Minggu / Libur';
                        $statusType = 'holiday';
                        $dotClass = 'bg-rose-400';
                    }
                @endphp

                <button type="button"
                    @click="selectedDate = '{{ $currentDateStr }}'; selectedInfo = {
                        dateLabel: '{{ $formattedDateLabel }}',
                        statusLabel: '{{ $statusLabel ?? 'Tidak ada data presensi' }}',
                        statusType: '{{ $statusType ?? 'none' }}',
                        checkIn: '{{ $checkInTime ?? '-' }}',
                        checkOut: '{{ $checkOutTime ?? '-' }}'
                    }"
                    :class="selectedDate === '{{ $currentDateStr }}' ? 'ring-2 ring-[var(--color-primary)] shadow-md scale-105' : ''"
                    class="aspect-square rounded-2xl flex flex-col items-center justify-center text-xs p-1 relative border transition-all hover:scale-105 border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)]">
                    
                    <span class="font-semibold {{ $isSunday || $holiday ? 'text-rose-500 font-bold' : '' }}">{{ $d }}</span>
                    
                    @if($dotClass)
                        <span class="w-2 h-2 rounded-full mt-1 {{ $dotClass }}"></span>
                    @endif
                </button>
            @endfor
        </div>
    </x-ui.card>

    <!-- Interactive Selected Date Detail Card -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-5 shadow-sm">
        <template x-if="selectedInfo">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h4 class="font-bold text-sm text-[var(--color-text)]" x-text="selectedInfo.dateLabel"></h4>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                        :class="{
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300': selectedInfo.statusType === 'hadir',
                            'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300': selectedInfo.statusType === 'terlambat',
                            'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-300': selectedInfo.statusType === 'izin' || selectedInfo.statusType === 'sakit',
                            'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300': selectedInfo.statusType === 'alpa' || selectedInfo.statusType === 'holiday',
                            'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400': selectedInfo.statusType === 'none'
                        }"
                        x-text="selectedInfo.statusLabel">
                    </span>
                </div>

                <template x-if="selectedInfo.statusType !== 'holiday' && selectedInfo.statusType !== 'none'">
                    <div class="grid grid-cols-2 gap-4 text-xs pt-1">
                        <div class="bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-[var(--color-border)]">
                            <span class="text-[var(--color-text-muted)] block font-medium mb-1">Jam Masuk</span>
                            <span class="font-bold text-sm text-[var(--color-text)] font-mono" x-text="selectedInfo.checkIn"></span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-[var(--color-border)]">
                            <span class="text-[var(--color-text-muted)] block font-medium mb-1">Jam Pulang</span>
                            <span class="font-bold text-sm text-[var(--color-text)] font-mono" x-text="selectedInfo.checkOut"></span>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!selectedInfo">
            <p class="text-xs text-center text-[var(--color-text-muted)] py-2">
                💡 Ketuk salah satu tanggal pada kalender untuk melihat rincian jam presensi.
            </p>
        </template>
    </div>

    <!-- Calendar Legend -->
    <x-ui.card class="p-4 flex flex-wrap items-center justify-around gap-2 text-xs">
        <div class="flex items-center space-x-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span class="text-[var(--color-text-muted)]">Hadir</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            <span class="text-[var(--color-text-muted)]">Terlambat</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
            <span class="text-[var(--color-text-muted)]">Izin/Sakit</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
            <span class="text-[var(--color-text-muted)]">Alpa / Libur</span>
        </div>
    </x-ui.card>
</div>
