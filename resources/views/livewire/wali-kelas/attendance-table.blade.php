<div class="space-y-6" x-data="{ previewPhotoUrl: null, previewPhotoTitle: '' }">
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
            <div class="text-xs text-[var(--color-text-muted)] font-medium flex items-center space-x-2 bg-slate-50 dark:bg-slate-900/50 px-3 py-1.5 rounded-xl border border-[var(--color-border)] self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                <span>Real-time Monitoring</span>
            </div>
        </div>

        <!-- Filters Container -->
        <x-ui.card class="p-4 flex flex-col md:flex-row items-stretch md:items-end gap-3">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Tanggal:</label>
                    <input type="date" wire:model.live="dateFilter" class="w-full px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Status Presensi:</label>
                    <select wire:model.live="statusFilter" class="w-full px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Alpa</option>
                    </select>
                </div>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-semibold text-[var(--color-text-muted)] uppercase mb-1">Cari Murid:</label>
                <div class="relative w-full">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama murid..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--color-text-muted)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Attendance Table -->
        <x-ui.card class="overflow-hidden p-0 relative">
            <!-- Loading Overlay -->
            <div wire:loading.flex class="absolute inset-0 bg-slate-900/10 dark:bg-slate-950/30 backdrop-blur-[1px] z-10 items-center justify-center">
                <div class="px-4 py-2 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] shadow-lg flex items-center space-x-2 text-xs font-semibold">
                    <svg class="w-4 h-4 animate-spin text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memuat data...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Keluar</th>
                            <th class="px-6 py-4">Bukti Foto</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition-all">
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $att->date->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 font-bold">
                                    <p class="text-[var(--color-text)]">{{ $att->student->user->name ?? '-' }}</p>
                                    <p class="text-[10px] font-mono text-[var(--color-text-muted)]">NIS: {{ $att->student->nis ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    {{ $att->check_in_at ? $att->check_in_at->format('H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    {{ $att->check_out_at ? $att->check_out_at->format('H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        @if($att->check_in_photo_path)
                                            <div class="text-center group">
                                                <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $att->check_in_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Masuk - {{ addslashes($att->student->user->name ?? 'Murid') }} ({{ $att->check_in_at?->format('H:i') }} WIB)'" class="w-10 h-10 rounded-xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all focus:outline-none">
                                                    <img src="{{ asset('storage/' . $att->check_in_photo_path) }}" class="w-full h-full object-cover">
                                                </button>
                                                <span class="text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 block mt-0.5">Masuk</span>
                                            </div>
                                        @endif

                                        @if($att->check_out_photo_path)
                                            <div class="text-center group">
                                                <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $att->check_out_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Pulang - {{ addslashes($att->student->user->name ?? 'Murid') }} ({{ $att->check_out_at?->format('H:i') }} WIB)'" class="w-10 h-10 rounded-xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all focus:outline-none">
                                                    <img src="{{ asset('storage/' . $att->check_out_photo_path) }}" class="w-full h-full object-cover">
                                                </button>
                                                <span class="text-[9px] font-semibold text-amber-600 dark:text-amber-400 block mt-0.5">Pulang</span>
                                            </div>
                                        @endif

                                        @if(!$att->check_in_photo_path && !$att->check_out_photo_path)
                                            <span class="text-[var(--color-text-muted)]">-</span>
                                        @endif
                                    </div>
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
                                <td colspan="7" class="px-6 py-12 text-center text-[var(--color-text-muted)] space-y-2">
                                    <span class="text-3xl block opacity-50">📅</span>
                                    <p class="font-medium">Belum ada data presensi untuk filter ini.</p>
                                </td>
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
                    <button @click="previewPhotoUrl = null" type="button" class="px-6 py-2.5 rounded-xl bg-[var(--color-primary)] text-white font-semibold text-xs shadow-md hover:opacity-90 transition-all">
                        Tutup Pratinjau
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
