<div class="space-y-6">
    @if(!$classRoom)
        <div class="p-8 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-3xl text-center space-y-3 shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-300 flex items-center justify-center text-3xl mx-auto">
                ⚠️
            </div>
            <h2 class="text-xl font-bold text-amber-900 dark:text-amber-200">Belum Di-assign ke Kelas</h2>
            <p class="text-sm text-amber-700 dark:text-amber-400 max-w-md mx-auto">
                Akun Wali Kelas Anda belum ditautkan ke kelas manapun. Silakan hubungi Administrator untuk menentukan kelas yang Anda ampu.
            </p>
        </div>
    @else
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[var(--color-surface)] p-6 rounded-3xl border border-[var(--color-border)] shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Selamat Datang, {{ auth()->user()->name }} 👋</h2>
                <p class="text-sm text-[var(--color-text-muted)] mt-1">
                    Wali Kelas <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $classRoom->name }}</span> {{ $classRoom->major ? '('.$classRoom->major.')' : '' }} | {{ $todayDate }}
                </p>
            </div>
        </div>

        <!-- Attendance Rate Progress Banner -->
        @php
            $attendanceRate = $studentsCount > 0 ? round(($hadirCount / $studentsCount) * 100) : 0;
        @endphp
        <x-ui.card class="p-5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-bold text-sm text-[var(--color-text)]">Tingkat Kehadiran Kelas Hari Ini</h3>
                </div>
                <span class="font-bold text-base text-emerald-600 dark:text-emerald-400">{{ $attendanceRate }}%</span>
            </div>
            <div class="w-full bg-[var(--color-bg)] rounded-full h-2.5 overflow-hidden border border-[var(--color-border)]">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-[var(--color-text-muted)] font-medium">
                <span>{{ $hadirCount }} dari {{ $studentsCount }} Murid Hadir</span>
                <span>{{ $studentsCount - $hadirCount }} Murid Belum/Tidak Hadir</span>
            </div>
        </x-ui.card>

        <!-- Summary Cards Grid (5 Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <x-ui.card class="p-4 space-y-1">
                <p class="text-[11px] text-[var(--color-text-muted)] font-medium uppercase tracking-wider">Total Murid</p>
                <h3 class="text-2xl font-bold text-[var(--color-text)]">{{ $studentsCount }}</h3>
                <p class="text-[10px] text-[var(--color-text-muted)]">Terdaftar di kelas</p>
            </x-ui.card>

            <x-ui.card class="p-4 space-y-1 border-l-4 border-l-emerald-500">
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wider">Hadir</p>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $hadirCount }}</h3>
                <p class="text-[10px] text-[var(--color-text-muted)]">Sudah check-in</p>
            </x-ui.card>

            <x-ui.card class="p-4 space-y-1 border-l-4 border-l-cyan-500">
                <p class="text-[11px] text-cyan-600 dark:text-cyan-400 font-semibold uppercase tracking-wider">Izin / Sakit</p>
                <h3 class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ $izinSakitCount }}</h3>
                <p class="text-[10px] text-[var(--color-text-muted)]">Izin disetujui</p>
            </x-ui.card>

            <x-ui.card class="p-4 space-y-1 border-l-4 border-l-rose-500">
                <p class="text-[11px] text-rose-600 dark:text-rose-400 font-semibold uppercase tracking-wider">Alpa</p>
                <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $alpaCount }}</h3>
                <p class="text-[10px] text-[var(--color-text-muted)]">Tanpa keterangan</p>
            </x-ui.card>

            <x-ui.card class="p-4 space-y-1 border-l-4 border-l-amber-500">
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider">Belum Absen</p>
                <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $belumPresensiCount }}</h3>
                <p class="text-[10px] text-[var(--color-text-muted)]">Belum presensi hari ini</p>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Leave Requests -->
            <x-ui.card class="p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-[var(--color-text)]">Permohonan Izin Pending</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                        {{ count($pendingLeaves) }} Menunggu
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingLeaves as $leave)
                        <div class="p-3.5 rounded-2xl bg-[var(--color-bg)] border border-[var(--color-border)] flex items-center justify-between text-xs transition-all">
                            <div class="space-y-0.5 min-w-0 flex-1 pr-3">
                                <strong class="text-[var(--color-text)] font-semibold truncate block">{{ $leave->student->user->name }}</strong>
                                <p class="text-[var(--color-text-muted)] text-[11px]">{{ $leave->type->label() }} • {{ $leave->date->isoFormat('D MMMM YYYY') }}</p>
                                <p class="text-[11px] text-[var(--color-text-muted)] italic line-clamp-1">"{{ $leave->reason }}"</p>
                            </div>
                            <a href="{{ route('wali_kelas.leave-requests') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-500 transition-all shrink-0">
                                Proses
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8 space-y-2">
                            <span class="text-3xl block opacity-60">✨</span>
                            <p class="text-xs text-[var(--color-text-muted)]">Tidak ada permohonan izin yang pending.</p>
                        </div>
                    @empty
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Absences / Special Status Today -->
            <x-ui.card class="p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h3 class="font-bold text-sm text-[var(--color-text)]">Catatan Izin / Alpa Hari Ini</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[var(--color-bg)] border border-[var(--color-border)] text-[var(--color-text-muted)]">
                        {{ count($absentToday) }} Murid
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($absentToday as $att)
                        <div class="p-3.5 rounded-2xl bg-[var(--color-bg)] border border-[var(--color-border)] flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-[var(--color-text)] font-bold text-xs block">{{ $att->student->user->name }}</strong>
                                <p class="text-[var(--color-text-muted)] text-[11px] mt-0.5">NIS: {{ $att->student->nis ?? '-' }}</p>
                            </div>
                            <x-ui.badge :type="strtolower($att->status->value)" :value="$att->status->label()" />
                        </div>
                    @empty
                        <div class="text-center py-8 space-y-2">
                            <span class="text-4xl block">🎉</span>
                            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Semua murid di kelas hadir hari ini!</p>
                            <p class="text-[11px] text-[var(--color-text-muted)]">Tidak ada murid yang izin, sakit, maupun alpa.</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    @endif
</div>
