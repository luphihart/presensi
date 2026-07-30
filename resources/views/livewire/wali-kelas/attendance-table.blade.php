<div class="space-y-6">
    @if(!$classRoom)
        <div class="p-8 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-3xl text-center">
            <h2 class="text-lg font-bold text-amber-900 dark:text-amber-200">Belum Di-assign ke Kelas</h2>
            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Harap hubungi Administrator untuk assign kelas.</p>
        </div>
    @else
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Data Presensi Kelas {{ $classRoom->name }}</h2>
                <p class="text-sm text-[var(--color-text-muted)]">Riwayat presensi harian murid di kelas yang Anda ampu</p>
            </div>
        </div>

        <!-- Filters -->
        <x-ui.card class="p-4 flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Tanggal:</label>
                <input type="date" wire:model.live="dateFilter" class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Status:</label>
                <select wire:model.live="statusFilter" class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Cari Murid:</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama murid..." class="w-full px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
            </div>
        </x-ui.card>

        <!-- Attendance Table -->
        <x-ui.card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Keluar</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30">
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $att->date->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 font-bold">{{ $att->student->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 font-mono text-[var(--color-text-muted)]">
                                    {{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-[var(--color-text-muted)]">
                                    {{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-ui.badge :type="strtolower($att->status->value)" :value="$att->status->label()" />
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-muted)] italic">
                                    {{ $att->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-[var(--color-text-muted)]">Belum ada data presensi untuk filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendances->hasPages())
                <div class="p-4 border-t border-[var(--color-border)]">
                    {{ $attendances->links() }}
                </div>
            @endif
        </x-ui.card>
    @endif
</div>
