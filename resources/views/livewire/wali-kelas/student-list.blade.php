<div class="space-y-6">
    @if(!$classRoom)
        <div class="p-8 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-3xl text-center">
            <h2 class="text-lg font-bold text-amber-900 dark:text-amber-200">Belum Di-assign ke Kelas</h2>
            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Harap hubungi Administrator untuk assign kelas.</p>
        </div>
    @else
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Daftar Murid Kelas {{ $classRoom->name }}</h2>
                <p class="text-sm text-[var(--color-text-muted)]">Data murid terdaftar di kelas yang Anda ampu (Read-Only)</p>
            </div>
            <div class="px-4 py-2 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-500/20 text-xs font-semibold text-emerald-700 dark:text-emerald-300 self-start sm:self-auto flex items-center space-x-2">
                <span>🏫</span>
                <span>Total {{ $students->total() }} Murid Terdaftar</span>
            </div>
        </div>

        <!-- Search Bar -->
        <x-ui.card class="p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIS murid..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                <svg class="w-4 h-4 absolute left-3 top-3.5 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="text-xs text-[var(--color-text-muted)] font-medium flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span>Mode Tampilan Wali Kelas</span>
            </div>
        </x-ui.card>

        <!-- Students Table -->
        <x-ui.card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">NIS</th>
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">No. HP</th>
                            <th class="px-6 py-4">Jenis Kelamin</th>
                            <th class="px-6 py-4">Streak Presensi</th>
                            <th class="px-6 py-4">Status Akun</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($students as $student)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition-all">
                                <td class="px-6 py-4 font-mono font-semibold">{{ $student->nis ?? '-' }}</td>
                                <td class="px-6 py-4 font-bold">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                                            {{ strtoupper(substr($student->user->name ?? 'M', 0, 1)) }}
                                        </div>
                                        <span>{{ $student->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-muted)]">{{ $student->user->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-[var(--color-text-muted)] font-mono">{{ $student->phone ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($student->gender === 'L')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300">👨 Laki-Laki</span>
                                    @elseif($student->gender === 'P')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300">👩 Perempuan</span>
                                    @else
                                        <span class="text-[var(--color-text-muted)]">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    @php
                                        $streak = $student->current_streak;
                                        $badgeClass = match(true) {
                                            $streak >= 10 => 'bg-gradient-to-r from-amber-500 to-rose-500 text-white shadow-sm',
                                            $streak >= 5 => 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800',
                                            $streak > 0 => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300',
                                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-400'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[11px] inline-flex items-center space-x-1 {{ $badgeClass }}">
                                        <span>🔥</span>
                                        <span>{{ $streak }} Hari</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">Non-Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-[var(--color-text-muted)] space-y-2">
                                    <span class="text-3xl block opacity-50">🔍</span>
                                    <p class="font-medium">Tidak ada data murid ditemukan.</p>
                                    <p class="text-[11px]">Coba cari dengan kata kunci nama atau NIS yang lain.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="p-4 border-t border-[var(--color-border)]">
                    {{ $students->links() }}
                </div>
            @endif
        </x-ui.card>
    @endif
</div>
