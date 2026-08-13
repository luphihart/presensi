<div class="space-y-6" x-data="{ previewPhotoUrl: null, previewPhotoTitle: '', announcementDismissed: sessionStorage.getItem('announcement_dismissed_{{ $announcements->first()?->id }}') === '1' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-2xl bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold text-lg flex items-center justify-center border border-indigo-100 dark:border-indigo-900">
                @if($student?->profile_photo_path)
                    <img src="{{ asset('storage/' . $student->profile_photo_path) }}" class="w-full h-full rounded-2xl object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <p class="text-xs text-[var(--color-text-muted)] font-medium">Selamat Datang 👋</p>
                <h2 class="text-lg font-bold text-[var(--color-text)] leading-tight">{{ $user->name }}</h2>
                <p class="text-xs text-[var(--color-primary)] font-semibold">{{ $student?->classRoom?->name ?? 'Murid' }} • NIS {{ $student?->nis }}</p>
            </div>
        </div>
        <livewire:shared.notification-center />
    </div>

    <!-- Announcement Banner (Terbaru, Dismissable) -->
    @if($announcements->isNotEmpty())
        @php $latestAnnouncement = $announcements->first(); @endphp
        <div
            x-show="!announcementDismissed"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="relative flex items-start gap-3 px-4 py-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700 shadow-sm overflow-hidden"
        >
            <!-- Accent bar kiri -->
            <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl bg-amber-400"></div>

            <!-- Icon megaphone -->
            <div class="w-8 h-8 rounded-xl bg-amber-400 text-white flex items-center justify-center shrink-0 ml-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>

            <!-- Konten -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">📢 Pengumuman Sekolah</span>
                    <span class="text-[10px] text-amber-500 dark:text-amber-500">
                        {{ $latestAnnouncement->published_at?->locale('id')->isoFormat('D MMM') }}
                    </span>
                </div>
                <a href="{{ route('student.announcements') }}" class="block">
                    <p class="text-sm font-bold text-amber-900 dark:text-amber-100 leading-snug line-clamp-1 hover:underline">
                        {{ $latestAnnouncement->title }}
                    </p>
                    <p class="text-xs text-amber-700 dark:text-amber-300 line-clamp-2 mt-0.5 leading-relaxed">
                        {{ $latestAnnouncement->content }}
                    </p>
                </a>
                @if($announcements->count() > 1)
                    <a href="{{ route('student.announcements') }}" class="inline-flex items-center gap-1 mt-1.5 text-[11px] font-bold text-amber-700 dark:text-amber-400 hover:underline">
                        +{{ $announcements->count() - 1 }} pengumuman lainnya
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>

            <!-- Tombol tutup (dismiss) -->
            <button
                type="button"
                @click="announcementDismissed = true; sessionStorage.setItem('announcement_dismissed_{{ $latestAnnouncement->id }}', '1')"
                class="w-7 h-7 rounded-full bg-amber-200 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 hover:bg-amber-300 dark:hover:bg-amber-800 flex items-center justify-center shrink-0 transition-colors"
                aria-label="Tutup pengumuman"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Birthday Banner (Gen Z / Alpha style) -->
    @if($isBirthday)
        <div class="bg-gradient-to-r from-amber-400 via-rose-400 to-indigo-500 rounded-3xl p-5 text-white shadow-xl relative overflow-hidden animate-bounce-short">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="flex items-start space-x-3">
                <span class="text-3xl">🥳</span>
                <div>
                    <h3 class="font-bold text-lg">Selamat Ulang Tahun! 🎉</h3>
                    <p class="text-sm opacity-95 mt-1">{{ $birthdayGreeting }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Today Attendance Card -->
    <x-ui.card class="relative overflow-hidden border-[var(--color-primary)]/20 shadow-md">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)]">Presensi Hari Ini</span>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-[var(--color-bg)] text-[var(--color-text)] border border-[var(--color-border)] shadow-xs">
                {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
            </span>
        </div>

        @if(!$isSchoolDay)
            <div class="text-center py-6">
                <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-500 flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                    🏖️
                </div>
                <h3 class="text-lg font-bold text-[var(--color-text)]">Hari Ini Libur / Tidak Ada Sekolah</h3>
                <p class="text-xs text-[var(--color-text-muted)] mt-1">
                    {{ $todayHoliday ? $todayHoliday->name : 'Tidak ada jadwal presensi untuk hari ini.' }}
                </p>
            </div>
        @elseif(!$todayAttendance)
            <!-- State 1: Belum Presensi Masuk -->
            <div class="text-center py-6">
                <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-[var(--color-primary)] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[var(--color-text)]">Belum Presensi Masuk</h3>
                <p class="text-xs text-[var(--color-text-muted)] mt-1 mb-6">Jam Masuk: {{ $schedule ? substr($schedule->check_in_time, 0, 5) : '07:00' }} WIB (Toleransi {{ $schedule->check_in_tolerance_minutes ?? 10 }} mnt)</p>
                
                <a href="{{ route('student.attendance.check-in') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-2xl bg-[var(--color-primary)] text-white font-semibold text-base shadow-lg shadow-indigo-500/30 hover:opacity-95 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Presensi Masuk Sekarang
                </a>
            </div>
        @elseif($todayAttendance->check_in_at && !$todayAttendance->check_out_at)
            <!-- State 2: Sudah Masuk, Belum Pulang -->
            <div class="py-4 space-y-4">
                <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">✓</div>
                        <div>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 font-medium">Sudah Masuk Jam {{ $todayAttendance->check_in_at->format('H:i') }} WIB</p>
                            <div class="mt-0.5"><x-ui.badge :type="strtolower($todayAttendance->status->value)" :value="$todayAttendance->status->label()" /></div>
                        </div>
                    </div>

                    @if($todayAttendance->check_in_photo_path)
                        <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $todayAttendance->check_in_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Masuk Hari Ini'" class="w-12 h-12 rounded-xl overflow-hidden block border border-emerald-300 dark:border-emerald-700 shadow-sm hover:scale-105 transition-all shrink-0">
                            <img src="{{ asset('storage/' . $todayAttendance->check_in_photo_path) }}" class="w-full h-full object-cover">
                        </button>
                    @endif
                </div>

                <a href="{{ route('student.attendance.check-out') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-2xl bg-amber-500 text-white font-semibold text-base shadow-lg shadow-amber-500/30 hover:opacity-95 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Presensi Pulang
                </a>
            </div>
        @else
            <!-- State 3: Presensi Lengkap Hari Ini -->
            <div class="text-center py-6 space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-[var(--color-text)]">Presensi Lengkap</h3>
                    <p class="text-xs text-[var(--color-text-muted)] mt-1">Masuk: {{ $todayAttendance->check_in_at?->format('H:i') }} WIB • Pulang: {{ $todayAttendance->check_out_at?->format('H:i') }} WIB</p>
                    <div class="mt-2"><x-ui.badge :type="strtolower($todayAttendance->status->value)" :value="$todayAttendance->status->label()" /></div>
                </div>

                <!-- Photos Grid -->
                <div class="flex items-center justify-center space-x-4 pt-2">
                    @if($todayAttendance->check_in_photo_path)
                        <div class="text-center">
                            <span class="text-[10px] font-semibold text-[var(--color-text-muted)] uppercase block mb-1">Foto Masuk</span>
                            <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $todayAttendance->check_in_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Masuk Hari Ini'" class="w-14 h-14 rounded-2xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all">
                                <img src="{{ asset('storage/' . $todayAttendance->check_in_photo_path) }}" class="w-full h-full object-cover">
                            </button>
                        </div>
                    @endif

                    @if($todayAttendance->check_out_photo_path)
                        <div class="text-center">
                            <span class="text-[10px] font-semibold text-[var(--color-text-muted)] uppercase block mb-1">Foto Pulang</span>
                            <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $todayAttendance->check_out_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Pulang Hari Ini'" class="w-14 h-14 rounded-2xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all">
                                <img src="{{ asset('storage/' . $todayAttendance->check_out_photo_path) }}" class="w-full h-full object-cover">
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </x-ui.card>

    <!-- Monthly Summary Card -->
    <x-ui.card>
        <h3 class="text-sm font-semibold text-[var(--color-text)] mb-4">Ringkasan Bulan Ini</h3>
        <div class="flex items-center justify-around">
            <div class="text-center">
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalHadir }}</p>
                <p class="text-xs text-[var(--color-text-muted)] font-medium mt-0.5">Hadir</p>
            </div>
            <div class="w-px h-8 bg-[var(--color-border)]"></div>
            <div class="text-center">
                <p class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ $totalIzin }}</p>
                <p class="text-xs text-[var(--color-text-muted)] font-medium mt-0.5">Izin/Sakit</p>
            </div>
            <div class="w-px h-8 bg-[var(--color-border)]"></div>
            <div class="text-center">
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $totalAlpa }}</p>
                <p class="text-xs text-[var(--color-text-muted)] font-medium mt-0.5">Alpa</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Quick Shortcuts -->
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('student.leave.index') }}" class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-4 text-center hover:border-[var(--color-primary)] transition-all shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-[var(--color-text)]">Ajukan Izin</p>
            <p class="text-xs text-[var(--color-text-muted)] mt-0.5">Sakit atau izin</p>
        </a>

        <a href="{{ route('student.history') }}" class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-4 text-center hover:border-[var(--color-primary)] transition-all shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-[var(--color-text)]">Lihat Riwayat</p>
            <p class="text-xs text-[var(--color-text-muted)] mt-0.5">Kalender presensi</p>
        </a>
    </div>

    <!-- Streak Kehadiran Card (Gamifikasi) -->
    @if($student)
        <div class="rounded-3xl p-5 bg-gradient-to-br from-amber-500/10 via-indigo-500/10 to-purple-500/10 border border-amber-200 dark:border-amber-900/40 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white font-bold text-lg flex items-center justify-center shadow-md">
                        🔥
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[var(--color-text)]">Streak Kehadiran</h3>
                        <p class="text-xs text-[var(--color-text-muted)] font-medium">
                            @if(($student->current_streak ?? 0) > 0)
                                {{ $student->current_streak }} hari berturut-turut! 🎉
                            @else
                                Belum ada streak aktif — yuk mulai hari ini! 💪
                            @endif
                        </p>
                    </div>
                </div>

                @if($badge)
                    <div class="px-3 py-1.5 rounded-2xl bg-amber-100 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-xs font-bold flex items-center gap-1.5">
                        <span class="text-sm">{{ $badge['icon'] }}</span>
                        <span>{{ $badge['name'] }}</span>
                    </div>
                @endif
            </div>

            <!-- Progress Bar ke Milestone Berikutnya -->
            @if($nextMilestone)
                @php
                    $current = $student->current_streak ?? 0;
                    $target = $nextMilestone['threshold'];
                    $prevThreshold = 0;
                    foreach(\App\Services\AttendanceStreakService::MILESTONES as $thresh => $b) {
                        if ($thresh < $target && $thresh > $prevThreshold) { $prevThreshold = $thresh; }
                    }
                    $range = max(1, $target - $prevThreshold);
                    $progressVal = max(0, $current - $prevThreshold);
                    $percentage = min(100, round(($progressVal / $range) * 100));
                    $remaining = $target - $current;
                @endphp
                <div class="space-y-1.5 pt-1">
                    <div class="flex justify-between text-[11px] font-semibold text-[var(--color-text-muted)]">
                        <span>Target Berikutnya</span>
                        <span class="text-[var(--color-primary)] font-bold">{{ $remaining }} hari lagi → {{ $nextMilestone['icon'] }} {{ $nextMilestone['name'] }}</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden p-0.5 border border-slate-300/50 dark:border-slate-600/50">
                        <div class="h-full rounded-full bg-gradient-to-r from-amber-400 via-orange-500 to-indigo-600 transition-all duration-500" style="width: {{ $percentage }}%;"></div>
                    </div>
                </div>
            @else
                <div class="text-[11px] font-bold text-amber-600 dark:text-amber-400 text-center pt-1">
                    🏆 Luar biasa! Kamu sudah mencapai Milestone Tertinggi (Hadir Champion)! 👑
                </div>
            @endif

            <div class="flex items-center justify-between pt-2 border-t border-amber-200/50 dark:border-amber-900/30 text-[11px] text-[var(--color-text-muted)]">
                <span>Rekor Terbaikmu: <strong class="text-[var(--color-text)]">{{ $student->longest_streak ?? 0 }} Hari</strong> 🏅</span>
                <span class="italic">Izin/Sakit disetujui tidak memutus streak ✨</span>
            </div>
        </div>

        <!-- Leaderboard & Prestasi Banner Card -->
        <a href="{{ route('student.leaderboard') }}" class="block rounded-3xl p-5 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white shadow-md hover:shadow-lg hover:scale-[1.01] transition-all relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none group-hover:scale-125 transition-all"></div>
            
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shadow-inner border border-white/20">
                        🏆
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="font-bold text-base leading-tight">Kedisiplinan & Peringkat</h3>
                            <span class="bg-amber-400 text-slate-900 text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                                #{{ $student->monthly_rank ?? '-' }}
                            </span>
                        </div>
                        <p class="text-xs text-indigo-100 mt-1">
                            Total Poin: <strong class="text-amber-300">{{ number_format($student->monthly_points ?? 0) }} pt</strong> bulan ini • Cek klasemen kelas & badges
                        </p>
                    </div>
                </div>

                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0 group-hover:translate-x-1 transition-all">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>
    @endif

    <!-- Link Lihat Semua Pengumuman (di bawah konten utama) -->
    @if($announcements->isNotEmpty())
        <a href="{{ route('student.announcements') }}" class="flex items-center justify-center gap-2 py-3 rounded-2xl border border-dashed border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-400 text-xs font-semibold hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            Lihat Semua Pengumuman Sekolah
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
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
                    <button @click="previewPhotoUrl = null" type="button" class="px-6 py-2.5 rounded-xl bg-[var(--color-primary)] text-white font-semibold text-xs shadow-md">
                        Tutup Pratinjau
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
