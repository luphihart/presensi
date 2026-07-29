<!DOCTYPE html>
<html lang="id" data-theme="{{ auth()->user()->theme_preference ?? 'system' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Beranda' }} - Presensi Murid</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        function applyTheme(theme) {
            if (theme === 'system') {
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            } else {
                document.documentElement.setAttribute('data-theme', theme);
            }
        }
        applyTheme("{{ auth()->user()->theme_preference ?? 'system' }}");
        window.addEventListener('theme-changed', (e) => applyTheme(e.detail.theme));
    </script>
</head>
<body class="min-h-screen pb-20 lg:pb-0 lg:flex">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:block w-64 bg-[var(--color-surface)] border-r border-[var(--color-border)] p-6 shrink-0">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg">P</div>
            <div>
                <h1 class="font-bold text-base leading-tight">Presensi Murid</h1>
                <p class="text-xs text-[var(--color-text-muted)]">{{ \App\Models\Setting::get('school_name', 'Presensi Sekolah') }}</p>
            </div>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('student.dashboard') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('student.attendance.check-in') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('student.attendance.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Presensi</span>
            </a>
            <a href="{{ route('student.leave.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('student.leave.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Ajukan Izin</span>
            </a>
            <a href="{{ route('student.history') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('student.history') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Riwayat</span>
            </a>
            <a href="{{ route('student.announcements') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('student.announcements') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.684A1.761 1.761 0 013 12c0-.663.364-1.24.908-1.543l3.526-1.958"/></svg>
                <span>Pengumuman</span>
            </a>
            <a href="{{ route('student.profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('student.profile', 'student.password') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Profil</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 max-w-xl mx-auto w-full p-4 lg:p-8">
        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-[var(--color-surface)] border-t border-[var(--color-border)] px-2 py-2 flex justify-around items-center z-40">
        <a href="{{ route('student.dashboard') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('student.dashboard') ? 'text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)]' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('student.attendance.check-in') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('student.attendance.*') ? 'text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)]' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Presensi</span>
        </a>
        <a href="{{ route('student.leave.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('student.leave.*') ? 'text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)]' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Izin</span>
        </a>
        <a href="{{ route('student.history') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('student.history') ? 'text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)]' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('student.profile') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('student.profile', 'student.password') ? 'text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)]' }}">
            <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Profil</span>
        </a>
    </nav>

    @livewireScripts
</body>
</html>
