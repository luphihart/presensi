<div class="space-y-6" x-data="{ selectedDate: null, selectedInfo: null, previewPhotoUrl: null, previewPhotoTitle: '' }">
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
            {{ $date->locale('id')->isoFormat('MMMM YYYY') }}
        </h3>
        <button wire:click="nextMonth" @click="selectedDate = null; selectedInfo = null" type="button" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-[var(--color-text)] font-bold">
            →
        </button>
    </x-ui.card>

    <!-- Calendar Grid Card -->
    <x-ui.card>
        <div class="grid grid-cols-7 gap-1 text-center font-semibold text-xs text-[var(--color-text-muted)] mb-3">
            <span class="text-rose-500 font-bold">Min</span>
            <span>Sen</span>
            <span>Sel</span>
            <span>Rab</span>
            <span>Kam</span>
            <span>Jum</span>
            <span class="{{ (isset($schedules[6]) && !$schedules[6]) || !isset($schedules[6]) ? 'text-rose-500 font-bold' : '' }}">Sab</span>
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
                    $formattedDateLabel = $currentDateObj->locale('id')->isoFormat('D MMMM YYYY');
                    $att = $attendances[$currentDateStr] ?? null;
                    $holiday = $holidays[$currentDateStr] ?? null;
                    $dayOfWeek = $currentDateObj->dayOfWeek; // 0=Sunday, 6=Saturday
                    $isNonSchoolDay = isset($schedules[$dayOfWeek]) ? !$schedules[$dayOfWeek] : ($dayOfWeek === 0 || $dayOfWeek === 6);

                    $dotClass = null;
                    $statusLabel = null;
                    $statusType = null;
                    $checkInTime = null;
                    $checkOutTime = null;
                    $checkInPhoto = null;
                    $checkOutPhoto = null;

                    if ($att) {
                        $statusType = strtolower($att->status->value);
                        $statusLabel = $att->status->label();
                        $checkInTime = $att->check_in_at ? $att->check_in_at->format('H:i') . ' WIB' : null;
                        $checkOutTime = $att->check_out_at ? $att->check_out_at->format('H:i') . ' WIB' : null;
                        $checkInPhoto = $att->check_in_photo_path ? asset('storage/' . $att->check_in_photo_path) : null;
                        $checkOutPhoto = $att->check_out_photo_path ? asset('storage/' . $att->check_out_photo_path) : null;

                        $dotClass = match($statusType) {
                            'hadir' => 'bg-emerald-500',
                            'terlambat' => 'bg-amber-500',
                            'izin', 'sakit' => 'bg-cyan-500',
                            'alpa' => 'bg-rose-500',
                            default => 'bg-slate-400'
                        };
                    } elseif ($holiday || $isNonSchoolDay) {
                        $statusLabel = $holiday ? $holiday->name : ($dayOfWeek === 0 ? 'Hari Minggu' : ($dayOfWeek === 6 ? 'Hari Sabtu (Libur)' : 'Hari Libur Sekolah'));
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
                        checkOut: '{{ $checkOutTime ?? '-' }}',
                        checkInPhoto: '{{ $checkInPhoto ?? '' }}',
                        checkOutPhoto: '{{ $checkOutPhoto ?? '' }}'
                    }"
                    :class="selectedDate === '{{ $currentDateStr }}' ? 'ring-2 ring-[var(--color-primary)] shadow-md scale-105' : ''"
                    class="aspect-square rounded-2xl flex flex-col items-center justify-center text-xs p-1 relative border transition-all hover:scale-105 border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)]">
                    
                    <span class="font-semibold {{ $isNonSchoolDay || $holiday ? 'text-rose-500 font-bold' : '' }}">{{ $d }}</span>
                    
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
            <div class="space-y-4">
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
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-[var(--color-border)]">
                                <span class="text-[var(--color-text-muted)] block font-medium mb-1">Jam Masuk</span>
                                <span class="font-bold text-sm text-[var(--color-text)] font-mono" x-text="selectedInfo.checkIn"></span>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-[var(--color-border)]">
                                <span class="text-[var(--color-text-muted)] block font-medium mb-1">Jam Pulang</span>
                                <span class="font-bold text-sm text-[var(--color-text)] font-mono" x-text="selectedInfo.checkOut"></span>
                            </div>
                        </div>

                        <!-- Photos Section -->
                        <div class="flex items-center space-x-4 pt-1">
                            <template x-if="selectedInfo.checkInPhoto">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-semibold text-[var(--color-text-muted)] uppercase block">Foto Masuk</span>
                                    <button type="button" @click="previewPhotoUrl = selectedInfo.checkInPhoto; previewPhotoTitle = 'Foto Presensi Masuk (' + selectedInfo.dateLabel + ')'" class="w-14 h-14 rounded-2xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all">
                                        <img :src="selectedInfo.checkInPhoto" class="w-full h-full object-cover">
                                    </button>
                                </div>
                            </template>

                            <template x-if="selectedInfo.checkOutPhoto">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-semibold text-[var(--color-text-muted)] uppercase block">Foto Pulang</span>
                                    <button type="button" @click="previewPhotoUrl = selectedInfo.checkOutPhoto; previewPhotoTitle = 'Foto Presensi Pulang (' + selectedInfo.dateLabel + ')'" class="w-14 h-14 rounded-2xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all">
                                        <img :src="selectedInfo.checkOutPhoto" class="w-full h-full object-cover">
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!selectedInfo">
            <p class="text-xs text-center text-[var(--color-text-muted)] py-2">
                💡 Ketuk salah satu tanggal pada kalender untuk melihat rincian jam & bukti foto presensi.
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

    <!-- Photo Preview Pop-up Modal -->
    <template x-if="previewPhotoUrl">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4"
            @keydown.escape.window="previewPhotoUrl = null"
            @click.self="previewPhotoUrl = null">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-5 max-w-md w-full shadow-2xl space-y-4 relative animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-sm text-[var(--color-text)]" x-text="previewPhotoTitle"></h3>
                    <button @click="previewPhotoUrl = null" type="button" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-[var(--color-text)] flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <div class="w-full aspect-[3/4] max-h-[65vh] rounded-2xl bg-slate-900 overflow-hidden flex items-center justify-center shadow-inner">
                    <img :src="previewPhotoUrl" class="w-full h-full object-contain">
                </div>

                <div class="text-center">
                    <button @click="previewPhotoUrl = null" type="button" class="px-6 py-2.5 rounded-xl bg-[var(--color-primary)] text-white font-semibold text-xs shadow-md">
                        Tutup Pratinjau
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
