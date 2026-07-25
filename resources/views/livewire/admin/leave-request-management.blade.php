<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Manajemen Pengajuan Izin</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Persetujuan pengajuan izin dan sakit murid</p>
        </div>
    </div>

    <!-- Filter -->
    <x-ui.card class="p-4 flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase">Filter Status:</span>
        <select wire:model.live="statusFilter" class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu (Pending)</option>
            <option value="approved">Disetujui (Approved)</option>
            <option value="rejected">Ditolak (Rejected)</option>
        </select>
    </x-ui.card>

    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Murid</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Alasan</th>
                        <th class="px-6 py-3.5">Bukti</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                    @forelse($requests as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $item->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold whitespace-nowrap">{{ $item->student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><x-ui.badge type="info" :value="$item->student->classRoom->name" /></td>
                            <td class="px-6 py-4 font-semibold text-cyan-600 dark:text-cyan-400 whitespace-nowrap">{{ $item->type->label() }}</td>
                            <td class="px-6 py-4 max-w-xs cursor-help" title="{{ $item->reason }}">
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
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($item->status->value === 'pending')
                                        <button wire:click="approve({{ $item->id }})" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-700">
                                            Approve
                                        </button>
                                        <button wire:click="reject({{ $item->id }})" type="button" class="px-3 py-1.5 rounded-xl bg-amber-600 text-white font-semibold text-xs hover:bg-amber-700">
                                            Reject
                                        </button>
                                    @elseif($item->status->value === 'approved')
                                        <div title="Selesai (Disetujui oleh {{ $item->reviewer->name ?? 'Admin' }})" class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center cursor-default">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    @else
                                        <div title="Selesai (Ditolak oleh {{ $item->reviewer->name ?? 'Admin' }})" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center cursor-default">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </div>
                                    @endif

                                    <button wire:click="deleteLeaveRequest({{ $item->id }})" wire:confirm="Hapus pengajuan izin ini?" type="button" title="Hapus Pengajuan" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-[var(--color-text-muted)]">Belum ada pengajuan izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-border)]">
            {{ $requests->links() }}
        </div>
    </div>
</div>
