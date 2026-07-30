<div class="space-y-6">
    @if(!$classRoom)
        <div class="p-8 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-3xl text-center space-y-3">
            <div class="text-4xl">⚠️</div>
            <h2 class="text-xl font-bold text-amber-900 dark:text-amber-200">Belum Di-assign ke Kelas</h2>
            <p class="text-sm text-amber-700 dark:text-amber-400 max-w-md mx-auto">
                Akun Wali Kelas Anda belum ditautkan ke kelas manapun. Silakan hubungi Administrator untuk menentukan kelas yang Anda ampu.
            </p>
        </div>
    @else
        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Selamat Datang, {{ auth()->user()->name }} 👋</h2>
                <p class="text-sm text-[var(--color-text-muted)]">Wali Kelas {{ $classRoom->name }} {{ $classRoom->major ? '('.$classRoom->major.')' : '' }} | {{ $todayDate }}</p>
            </div>
            <a href="{{ route('wali_kelas.leave-requests') }}" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-xs font-semibold shadow-md self-start sm:self-auto hover:opacity-90 transition-all flex items-center space-x-2">
                <span>📋 Review Izin</span>
                @if(count($pendingLeaves) > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold">{{ count($pendingLeaves) }}</span>
                @endif
            </a>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.card class="space-y-1">
                <p class="text-xs text-[var(--color-text-muted)] font-medium">👥 Total Murid</p>
                <h3 class="text-2xl font-bold text-[var(--color-text)]">{{ $studentsCount }}</h3>
                <p class="text-[11px] text-[var(--color-text-muted)]">Terdaftar di kelas ini</p>
            </x-ui.card>

            <x-ui.card class="space-y-1 border-l-4 border-l-emerald-500">
                <p class="text-xs text-[var(--color-text-muted)] font-medium">✅ Hadir Hari Ini</p>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $hadirCount }}</h3>
                <p class="text-[11px] text-[var(--color-text-muted)]">Termasuk tepat waktu & terlambat</p>
            </x-ui.card>

            <x-ui.card class="space-y-1 border-l-4 border-l-cyan-500">
                <p class="text-xs text-[var(--color-text-muted)] font-medium">✉️ Izin / Sakit</p>
                <h3 class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ $izinSakitCount }}</h3>
                <p class="text-[11px] text-[var(--color-text-muted)]">Permohonan disetujui</p>
            </x-ui.card>

            <x-ui.card class="space-y-1 border-l-4 border-l-rose-500">
                <p class="text-xs text-[var(--color-text-muted)] font-medium">❌ Alpa / Belum Presensi</p>
                <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $alpaCount + $belumPresensiCount }}</h3>
                <p class="text-[11px] text-[var(--color-text-muted)]">{{ $alpaCount }} Alpa, {{ $belumPresensiCount }} Belum Absen</p>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Leave Requests -->
            <x-ui.card class="space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-sm text-[var(--color-text)]">📩 Permohonan Izin Menunggu Persetujuan</h3>
                    <span class="text-xs text-[var(--color-text-muted)]">{{ count($pendingLeaves) }} Izin</span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingLeaves as $leave)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-[var(--color-border)] flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-[var(--color-text)] font-semibold">{{ $leave->student->user->name }}</strong>
                                <p class="text-[var(--color-text-muted)] mt-0.5">{{ $leave->type->label() }} - {{ $leave->date->isoFormat('D MMMM YYYY') }}</p>
                                <p class="text-[11px] text-[var(--color-text-muted)] italic line-clamp-1">"{{ $leave->reason }}"</p>
                            </div>
                            <a href="{{ route('wali_kelas.leave-requests') }}" class="px-3 py-1.5 rounded-xl bg-[var(--color-primary)] text-white font-semibold hover:opacity-90 transition-all shrink-0">
                                Process
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--color-text-muted)] text-center py-6">Tidak ada permohonan izin pending.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Absences Today -->
            <x-ui.card class="space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-sm text-[var(--color-text)]">📊 Catatan Tidak Hadir / Izin Hari Ini</h3>
                    <span class="text-xs text-[var(--color-text-muted)]">{{ count($absentToday) }} Murid</span>
                </div>

                <div class="space-y-3">
                    @forelse($absentToday as $att)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-[var(--color-border)] flex items-center justify-between text-xs">
                            <div>
                                <strong class="text-[var(--color-text)] font-semibold">{{ $att->student->user->name }}</strong>
                                <p class="text-[var(--color-text-muted)] mt-0.5">NIS: {{ $att->student->nis ?? '-' }}</p>
                            </div>
                            <x-ui.badge :type="strtolower($att->status->value)" :value="$att->status->label()" />
                        </div>
                    @empty
                        <p class="text-xs text-[var(--color-text-muted)] text-center py-6">Semua murid di kelas hadir hari ini! 🎉</p>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    @endif
</div>
