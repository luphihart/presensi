<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Pengumuman Sekolah</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Informasi dan pengumuman resmi dari pihak sekolah</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="text-xs font-medium text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
            ← Kembali
        </a>
    </div>

    <!-- Announcement Cards -->
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">📢</span>
                        <h3 class="font-bold text-base text-[var(--color-text)] leading-snug">{{ $announcement->title }}</h3>
                    </div>
                    <span class="text-xs text-[var(--color-text-muted)] whitespace-nowrap shrink-0">
                        {{ $announcement->published_at ? $announcement->published_at->locale('id')->isoFormat('D MMMM YYYY') : '' }}
                    </span>
                </div>

                <div class="text-sm text-[var(--color-text)] whitespace-pre-line leading-relaxed pl-7">
                    {{ $announcement->content }}
                </div>
            </div>
        @empty
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-8 text-center space-y-2">
                <span class="text-4xl block mb-2">📭</span>
                <p class="font-bold text-[var(--color-text)] text-sm">Belum Ada Pengumuman</p>
                <p class="text-xs text-[var(--color-text-muted)]">Saat ini belum ada pengumuman resmi terbaru dari sekolah.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $announcements->links() }}
    </div>
</div>
