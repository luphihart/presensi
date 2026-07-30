<div class="space-y-6">
    @if(!$classRoom)
        <div class="p-8 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-3xl text-center">
            <h2 class="text-lg font-bold text-amber-900 dark:text-amber-200">Belum Di-assign ke Kelas</h2>
            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Harap hubungi Administrator untuk assign kelas.</p>
        </div>
    @else
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Pengajuan Izin Kelas {{ $classRoom->name }}</h2>
                <p class="text-sm text-[var(--color-text-muted)]">Persetujuan pengajuan izin dan sakit murid kelas yang Anda ampu</p>
            </div>
        </div>

        <!-- Filter -->
        <x-ui.card class="p-4 flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase">Filter Status:</span>
            <select wire:model.live="statusFilter" class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu (Pending)</option>
                <option value="approved">Disetujui (Approved)</option>
                <option value="rejected">Ditolak (Rejected)</option>
            </select>
        </x-ui.card>

        <x-ui.card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Alasan</th>
                            <th class="px-6 py-4">Bukti</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Ditinjau Oleh</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($requests as $item)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30">
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $item->date->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 font-bold whitespace-nowrap">{{ $item->student->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 font-semibold text-cyan-600 dark:text-cyan-400 whitespace-nowrap">{{ $item->type->label() }}</td>
                                <td class="px-6 py-4 max-w-xs" title="{{ $item->reason }}">
                                    <p class="line-clamp-2">"{{ $item->reason }}"</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->attachment_path)
                                        <a href="{{ asset('storage/' . $item->attachment_path) }}" target="_blank" class="text-xs font-semibold text-[var(--color-primary)] hover:underline">
                                            Lihat File
                                        </a>
                                    @else
                                        <span class="text-[var(--color-text-muted)]">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <x-ui.badge :type="strtolower($item->status->value)" :value="$item->status->label()" />
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-muted)]">
                                    @if($item->reviewer)
                                        <div class="font-medium text-[var(--color-text)]">{{ $item->reviewer->name }}</div>
                                        <div class="text-[10px] text-slate-400">({{ $item->reviewer->role->label() }})</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        @if($item->status->value === 'pending')
                                            <button wire:click="approve({{ $item->id }})" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-700 transition-all">
                                                Approve
                                            </button>
                                            <button wire:click="reject({{ $item->id }})" type="button" class="px-3 py-1.5 rounded-xl bg-rose-600 text-white font-semibold text-xs hover:bg-rose-700 transition-all">
                                                Reject
                                            </button>
                                        @else
                                            <span class="text-xs text-[var(--color-text-muted)] italic">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-[var(--color-text-muted)]">Belum ada pengajuan izin di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="p-4 border-t border-[var(--color-border)]">
                    {{ $requests->links() }}
                </div>
            @endif
        </x-ui.card>
    @endif
</div>
