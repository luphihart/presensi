<nav class="space-y-1 flex-1">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.students.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.students.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span>Data Murid</span>
    </a>
    <a href="{{ route('admin.classes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.classes.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <span>Kelas & Jurusan</span>
    </a>
    <a href="{{ route('admin.attendance.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.attendance.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <span>Presensi</span>
    </a>
    <a href="{{ route('admin.leave-requests.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.leave-requests.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Pengajuan Izin</span>
    </a>
    <a href="{{ route('admin.schedules.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.schedules.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Jadwal & Jam</span>
    </a>
    <a href="{{ route('admin.locations.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.locations.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>Geofence & Lokasi</span>
    </a>
    <a href="{{ route('admin.holidays.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.holidays.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span>Kalender Libur</span>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Rekap & Laporan</span>
    </a>
    <a href="{{ route('admin.announcements.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.announcements.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.684A1.761 1.761 0 013 12c0-.663.364-1.24.908-1.543l3.526-1.958"/></svg>
        <span>Pengumuman</span>
    </a>
    <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>Pengaturan Sekolah</span>
    </a>
</nav>

<div class="pt-4 border-t border-[var(--color-border)] space-y-1">
    <a href="{{ route('admin.password') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.password') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold' : 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/></svg>
        <span>Ubah Password</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Keluar</span>
        </button>
    </form>
</div>
