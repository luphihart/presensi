<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Rekap & Laporan Presensi Bulanan</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Pratinjau matriks presensi bulanan per tanggal & cetak laporan lanskap PDF</p>
        </div>

        <button wire:click="exportExcel" type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Download Excel Matriks (.xlsx)</span>
        </button>
    </div>

    <!-- Filter Card -->
    <x-ui.card class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Pilih Bulan & Tahun</label>
            <input type="month" wire:model.live="month" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Filter Kelas</label>
            <select wire:model.live="classFilter" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                <option value="">-- Semua Kelas --</option>
                @foreach($classRooms as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-700 dark:text-emerald-300 w-full">
                📊 Ekspor laporan dalam format <strong>Excel (.xlsx)</strong> lengkap dengan matriks tanggal.
            </div>
        </div>
    </x-ui.card>

    <!-- Interactive Matrix Grid Table Preview -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-[var(--color-border)] flex items-center justify-between">
            <h3 class="font-bold text-sm text-[var(--color-text)]">
                Pratinjau Matriks Presensi Bulan {{ \Carbon\Carbon::parse(($reportData['yearMonth'] ?? now()->format('Y-m')) . '-01')->isoFormat('MMMM YYYY') }}
            </h3>
            <span class="text-xs text-[var(--color-text-muted)]">Geser ke kanan untuk melihat rincian tanggal ➔</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[11px]">
                <thead>
                    <tr class="border-b border-[var(--color-border)] font-semibold text-[var(--color-text-muted)] uppercase bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-3 py-3 border-r border-[var(--color-border)] text-center w-8 bg-slate-50 dark:bg-slate-800 sticky left-0 z-20">No</th>
                        <th class="px-4 py-3 border-r border-[var(--color-border)] min-w-[140px] bg-slate-50 dark:bg-slate-800 sticky left-[33px] z-20 shadow-r">Nama Murid</th>
                        <th class="px-3 py-3 border-r border-[var(--color-border)] min-w-[80px]">NIS</th>
                        <th class="px-3 py-3 border-r border-[var(--color-border)] min-w-[70px]">Kelas</th>
                        <th class="px-2 py-3 border-r border-[var(--color-border)] text-center text-emerald-600 dark:text-emerald-400">H</th>
                        <th class="px-2 py-3 border-r border-[var(--color-border)] text-center text-amber-600 dark:text-amber-400">T</th>
                        <th class="px-2 py-3 border-r border-[var(--color-border)] text-center text-sky-600 dark:text-sky-400">I</th>
                        <th class="px-2 py-3 border-r border-[var(--color-border)] text-center text-indigo-600 dark:text-indigo-400">S</th>
                        <th class="px-2 py-3 border-r border-[var(--color-border)] text-center text-rose-600 dark:text-rose-400">A</th>
                        <th class="px-2 py-3 border-r border-[var(--color-border)] text-center font-bold">%</th>

                        @foreach($reportData['days'] as $dateStr => $day)
                            <th class="px-2 py-2 border-r border-[var(--color-border)] text-center min-w-[36px] {{ $day['isWeekend'] || isset($reportData['holidays'][$dateStr]) ? 'bg-slate-100 dark:bg-slate-800/60 text-slate-400' : '' }}">
                                {{ $day['dayNumber'] }}<br>
                                <span class="text-[10px] font-normal uppercase">{{ $day['dayName'] }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                    @forelse($reportData['matrix'] as $index => $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-3 py-2 border-r border-[var(--color-border)] text-center font-mono bg-[var(--color-surface)] sticky left-0 z-10">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border-r border-[var(--color-border)] font-bold text-xs truncate max-w-[160px] bg-[var(--color-surface)] sticky left-[33px] z-10 shadow-r">{{ $row['student']->user->name }}</td>
                            <td class="px-3 py-2 border-r border-[var(--color-border)] font-mono text-[10px]">{{ $row['student']->nis }}</td>
                            <td class="px-3 py-2 border-r border-[var(--color-border)] text-[10px]">{{ $row['student']->classRoom->name ?? '-' }}</td>
                            <td class="px-2 py-2 border-r border-[var(--color-border)] text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $row['summary']['hadir'] }}</td>
                            <td class="px-2 py-2 border-r border-[var(--color-border)] text-center font-bold text-amber-600 dark:text-amber-400">{{ $row['summary']['terlambat'] }}</td>
                            <td class="px-2 py-2 border-r border-[var(--color-border)] text-center font-bold text-sky-600 dark:text-sky-400">{{ $row['summary']['izin'] }}</td>
                            <td class="px-2 py-2 border-r border-[var(--color-border)] text-center font-bold text-indigo-600 dark:text-indigo-400">{{ $row['summary']['sakit'] }}</td>
                            <td class="px-2 py-2 border-r border-[var(--color-border)] text-center font-bold text-rose-600 dark:text-rose-400">{{ $row['summary']['alpa'] }}</td>
                            <td class="px-2 py-2 border-r border-[var(--color-border)] text-center font-bold">{{ $row['summary']['percentage'] }}%</td>

                            @foreach($reportData['days'] as $dateStr => $day)
                                @php
                                    $rec = $row['dateRecords'][$dateStr] ?? null;
                                    $lbl = $rec['label'] ?? '';
                                    $cellBg = match(strtolower($rec['status'] ?? '')) {
                                        'hadir' => 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-bold',
                                        'terlambat' => 'bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 font-bold',
                                        'izin' => 'bg-sky-100 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 font-bold',
                                        'sakit' => 'bg-indigo-100 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-bold',
                                        'alpa' => 'bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 font-bold',
                                        default => ($day['isWeekend'] || isset($reportData['holidays'][$dateStr])) ? 'bg-slate-100 dark:bg-slate-800/40 text-slate-400' : ''
                                    };
                                @endphp
                                <td class="px-1 py-1.5 border-r border-[var(--color-border)] text-center text-[10px] {{ $cellBg }}">
                                    @if($rec && $rec['time'])
                                        <span class="text-[9.5px] block font-mono leading-none text-slate-500 dark:text-slate-400">{{ $rec['time'] }}</span>
                                        <span>{{ $lbl }}</span>
                                    @else
                                        {{ $lbl }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 10 + count($reportData['days']) }}" class="px-6 py-12 text-center text-[var(--color-text-muted)]">
                                Belum ada data murid atau presensi untuk filter bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
