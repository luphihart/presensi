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
            localStorage.setItem('theme_preference', theme);
        }
        const savedTheme = localStorage.getItem('theme_preference') || "{{ auth()->user()->theme_preference ?? 'system' }}";
        applyTheme(savedTheme);
        window.addEventListener('theme-changed', (e) => applyTheme(e.detail.theme));
    </script>
</head>
<body class="min-h-screen bg-[var(--color-bg)] text-[var(--color-text)] flex flex-col lg:flex-row" x-data="{ mobileSidebarOpen: false, currentTheme: localStorage.getItem('theme_preference') || 'system' }">

    <!-- Mobile Header -->
    <header class="lg:hidden h-16 bg-[var(--color-surface)] border-b border-[var(--color-border)] px-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center space-x-3">
            <button @click="mobileSidebarOpen = true" type="button" class="p-2 rounded-xl text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <span class="font-bold text-sm block leading-tight">Wali Kelas</span>
                    <span class="text-[10px] text-[var(--color-text-muted)] font-medium">{{ auth()->user()->homeroomClass->name ?? 'Tanpa Kelas' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Theme Switcher Button Mobile -->
            <button type="button" @click="currentTheme = (currentTheme === 'dark' ? 'light' : 'dark'); applyTheme(currentTheme);" class="p-2 rounded-xl text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800 transition-all" title="Toggle Tema">
                <svg x-show="currentTheme === 'dark'" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg x-show="currentTheme !== 'dark'" class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center text-xs ring-2 ring-emerald-500/20">
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
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-[var(--color-border)]">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <h1 class="font-bold text-base leading-tight text-[var(--color-text)]">Panel Wali Kelas</h1>
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ auth()->user()->homeroomClass->name ?? 'Belum ada kelas' }}</p>
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
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-[var(--color-border)]">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="font-bold text-base leading-tight text-[var(--color-text)] truncate">Wali Kelas</h1>
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 truncate">{{ auth()->user()->homeroomClass->name ?? 'Belum ada kelas' }}</p>
            </div>
        </div>

        <!-- Identity Box -->
        <div class="mb-6 p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-[var(--color-border)] flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center text-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-[var(--color-text)] truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] font-mono text-[var(--color-text-muted)] truncate">NIP: {{ auth()->user()->nip ?? '-' }}</p>
            </div>
        </div>

        @include('layouts.partials.wali-kelas-nav')
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
        <header class="hidden lg:flex h-16 bg-[var(--color-surface)] border-b border-[var(--color-border)] px-8 items-center justify-between sticky top-0 z-20">
            <h2 class="font-semibold text-lg text-[var(--color-text)]">{{ $header ?? 'Dashboard Wali Kelas' }}</h2>
            <div class="flex items-center space-x-4">
                <!-- Theme Switcher Desktop -->
                <button type="button" @click="currentTheme = (currentTheme === 'dark' ? 'light' : 'dark'); applyTheme(currentTheme);" class="p-2 rounded-xl text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800 transition-all flex items-center space-x-2 text-xs font-medium border border-[var(--color-border)]" title="Toggle Tema">
                    <svg x-show="currentTheme === 'dark'" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="currentTheme !== 'dark'" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <span x-text="currentTheme === 'dark' ? 'Gelap' : 'Terang'" class="capitalize"></span>
                </button>

                <div class="h-4 w-px bg-[var(--color-border)]"></div>

                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <span class="text-xs font-bold text-[var(--color-text)] block leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">{{ auth()->user()->homeroomClass->name ?? 'Tanpa Kelas' }}</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
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
