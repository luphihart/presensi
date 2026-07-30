@php
    $classId = auth()->user()->homeroomClass?->id;
    $pendingLeaveCount = $classId 
        ? \App\Models\LeaveRequest::whereHas('student', fn($q) => $q->where('class_room_id', $classId))
            ->where('status', \App\Enums\LeaveStatus::Pending)
            ->count() 
        : 0;
@endphp

<nav class="space-y-1 flex-1">
    <a href="{{ route('wali_kelas.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium border-l-4 transition-all {{ request()->routeIs('wali_kelas.dashboard') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold border-emerald-500 shadow-sm' : 'border-transparent text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Dashboard Kelas</span>
    </a>
    <a href="{{ route('wali_kelas.students') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium border-l-4 transition-all {{ request()->routeIs('wali_kelas.students') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold border-emerald-500 shadow-sm' : 'border-transparent text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span>Data Murid Kelas</span>
    </a>
    <a href="{{ route('wali_kelas.attendance') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium border-l-4 transition-all {{ request()->routeIs('wali_kelas.attendance') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold border-emerald-500 shadow-sm' : 'border-transparent text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <span>Presensi Kelas</span>
    </a>
    <a href="{{ route('wali_kelas.leave-requests') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium border-l-4 transition-all {{ request()->routeIs('wali_kelas.leave-requests') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold border-emerald-500 shadow-sm' : 'border-transparent text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <div class="flex items-center space-x-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Pengajuan Izin</span>
        </div>
        @if($pendingLeaveCount > 0)
            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold animate-pulse">
                {{ $pendingLeaveCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('wali_kelas.reports') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium border-l-4 transition-all {{ request()->routeIs('wali_kelas.reports') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold border-emerald-500 shadow-sm' : 'border-transparent text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Laporan Kelas</span>
    </a>
</nav>

<div class="pt-4 mt-2 border-t border-[var(--color-border)] space-y-1">
    <a href="{{ route('wali_kelas.password') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium border-l-4 transition-all {{ request()->routeIs('wali_kelas.password') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-semibold border-emerald-500 shadow-sm' : 'border-transparent text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/></svg>
        <span>Ubah Password</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all border-l-4 border-transparent">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Keluar</span>
        </button>
    </form>
</div>
