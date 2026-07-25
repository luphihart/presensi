<div class="space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-[var(--color-text)]">Ringkasan Presensi Hari Ini</h2>
        <p class="text-sm text-[var(--color-text-muted)]">{{ now()->isoFormat('D MMMM YYYY') }} • Total Murid Aktif: {{ $totalStudents }}</p>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-ui.stat-card title="Hadir Tepat Waktu" :value="$totalHadir" color="success">
            <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></x-slot:icon>
        </x-ui.stat-card>

        <x-ui.stat-card title="Terlambat" :value="$totalTerlambat" color="warning">
            <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot:icon>
        </x-ui.stat-card>

        <x-ui.stat-card title="Izin / Sakit" :value="$totalIzin" color="info">
            <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></x-slot:icon>
        </x-ui.stat-card>

        <x-ui.stat-card title="Alpa (Tanpa Keterangan)" :value="$totalAlpa" color="danger">
            <x-slot:icon><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></x-slot:icon>
        </x-ui.stat-card>
    </div>

    <!-- Quick Leave Approval Box -->
    <x-ui.card class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-[var(--color-text)]">Pengajuan Izin Pending (Menunggu Persetujuan)</h3>
            <a href="{{ route('admin.leave-requests.index') }}" class="text-xs text-[var(--color-primary)] font-semibold hover:underline">Lihat Semua →</a>
        </div>

        <div class="divide-y divide-[var(--color-border)]">
            @forelse($pendingLeaves as $item)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-sm text-[var(--color-text)]">{{ $item->student->user->name }} ({{ $item->student->classRoom->name }})</p>
                        <p class="text-xs text-[var(--color-text-muted)]">Tanggal: {{ $item->date->format('d/m/Y') }} • Jenis: <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $item->type->label() }}</span></p>
                        <p class="text-xs text-[var(--color-text)] mt-0.5">"{{ $item->reason }}"</p>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button wire:click="approveLeave({{ $item->id }})" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm">
                            Approve
                        </button>
                        <button wire:click="rejectLeave({{ $item->id }})" type="button" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm">
                            Reject
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-[var(--color-text-muted)] py-4 text-center">Tidak ada pengajuan izin yang pending saat ini.</p>
            @endforelse
        </div>
    </x-ui.card>
</div>
