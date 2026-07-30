<!DOCTYPE html>
<html lang="id" data-theme="{{ auth()->user()->theme_preference ?? 'system' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel Wali Kelas' }} - Presensi Murid</title>
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
<body class="min-h-screen bg-[var(--color-bg)] text-[var(--color-text)] flex flex-col lg:flex-row" x-data="{ mobileSidebarOpen: false }">

    <!-- Mobile Header -->
    <header class="lg:hidden h-16 bg-[var(--color-surface)] border-b border-[var(--color-border)] px-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center space-x-3">
            <button @click="mobileSidebarOpen = true" type="button" class="p-2 rounded-xl text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-base">WK</div>
                <span class="font-bold text-sm">Wali Kelas</span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold flex items-center justify-center text-xs">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar Drawer Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

    <!-- Mobile Slide-Over Sidebar Drawer -->
    <aside x-show="mobileSidebarOpen"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 w-72 bg-[var(--color-surface)] border-r border-[var(--color-border)] p-6 flex flex-col z-50 lg:hidden shadow-2xl overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg">WK</div>
                <div>
                    <h1 class="font-bold text-base leading-tight">Wali Kelas</h1>
                    <p class="text-xs text-[var(--color-text-muted)]">{{ auth()->user()->homeroomClass->name ?? 'Belum ada kelas' }}</p>
                </div>
            </div>
            <button @click="mobileSidebarOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                ✕
            </button>
        </div>

        @include('layouts.partials.wali-kelas-nav')
    </aside>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex w-64 bg-[var(--color-surface)] border-r border-[var(--color-border)] p-6 flex-col shrink-0 min-h-screen sticky top-0">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg">WK</div>
            <div>
                <h1 class="font-bold text-base leading-tight">Wali Kelas</h1>
                <p class="text-xs text-[var(--color-text-muted)]">{{ auth()->user()->homeroomClass->name ?? 'Belum ada kelas' }}</p>
            </div>
        </div>

        @include('layouts.partials.wali-kelas-nav')
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
        <header class="hidden lg:flex h-16 bg-[var(--color-surface)] border-b border-[var(--color-border)] px-8 items-center justify-between sticky top-0 z-20">
            <h2 class="font-semibold text-lg text-[var(--color-text)]">{{ $header ?? 'Dashboard Wali Kelas' }}</h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-[var(--color-text-muted)]">{{ auth()->user()->name }} ({{ auth()->user()->homeroomClass->name ?? 'Tanpa Kelas' }})</span>
                <div class="w-8 h-8 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold flex items-center justify-center text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
