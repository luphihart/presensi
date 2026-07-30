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
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-600/10 via-teal-500/5 to-transparent p-6 rounded-3xl border border-emerald-500/20">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Selamat Datang, {{ auth()->user()->name }} 👋</h2>
                <p class="text-sm text-[var(--color-text-muted)] mt-1">
                    Wali Kelas <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $classRoom->name }}</span> {{ $classRoom->major ? '('.$classRoom->major.')' : '' }} | {{ $todayDate }}
                </p>
            </div>
            <a href="{{ route('wali_kelas.leave-requests') }}" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 self-start sm:self-auto transition-all flex items-center space-x-2.5 group">
                <span>📋 Review Izin</span>
                @if(count($pendingLeaves) > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold animate-bounce">{{ count($pendingLeaves) }}</span>
                @endif
            </a>
        </div>

        <!-- Attendance Rate Progress Banner -->
        @php
            $attendanceRate = $studentsCount > 0 ? round(($hadirCount / $studentsCount) * 100) : 0;
        @endphp
        <x-ui.card class="p-5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-lg">📊</span>
                    <h3 class="font-bold text-sm text-[var(--color-text)]">Tingkat Kehadiran Kelas Hari Ini</h3>
                </div>
                <span class="font-bold text-base text-emerald-600 dark:text-emerald-400">{{ $attendanceRate }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-[var(--color-border)]">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
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

            <x-ui.card class="p-4 space-y-1 border-l-4 border-l-amber-500 col-span-2 sm:col-span-1">
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider">Belum Absen</p>
                <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $belumPresensiCount }}</h3>
                <p class="text-[10px] text-[var(--color-text-muted)]">Belum presensi hari ini</p>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Leave Requests -->
            <x-ui.card class="p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">📩</span>
                        <h3 class="font-bold text-sm text-[var(--color-text)]">Permohonan Izin Pending</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300">
                        {{ count($pendingLeaves) }} Menunggu
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingLeaves as $leave)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-[var(--color-border)] flex items-center justify-between text-xs hover:border-emerald-500/30 transition-all">
                            <div class="space-y-0.5 min-w-0 flex-1 pr-3">
                                <strong class="text-[var(--color-text)] font-semibold truncate block">{{ $leave->student->user->name }}</strong>
                                <p class="text-[var(--color-text-muted)] text-[11px]">{{ $leave->type->label() }} • {{ $leave->date->isoFormat('D MMMM YYYY') }}</p>
                                <p class="text-[11px] text-slate-500 italic line-clamp-1">"{{ $leave->reason }}"</p>
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
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Absences / Special Status Today -->
            <x-ui.card class="p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">📋</span>
                        <h3 class="font-bold text-sm text-[var(--color-text)]">Catatan Izin / Alpa Hari Ini</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[var(--color-text-muted)]">
                        {{ count($absentToday) }} Murid
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($absentToday as $att)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-[var(--color-border)] flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-[var(--color-text)] font-semibold">{{ $att->student->user->name }}</strong>
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
