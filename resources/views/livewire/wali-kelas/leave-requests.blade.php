<div class="space-y-6" x-data="{ confirmModalOpen: false, confirmAction: '', confirmId: null, confirmStudentName: '' }">
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
        <x-ui.card class="p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase">Filter Status:</span>
                <select wire:model.live="statusFilter" class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu (Pending)</option>
                    <option value="approved">Disetujui (Approved)</option>
                    <option value="rejected">Ditolak (Rejected)</option>
                </select>
            </div>
            <div class="text-xs text-[var(--color-text-muted)] italic">
                *Wali Kelas & Admin dapat memberikan persetujuan
            </div>
        </x-ui.card>

        <x-ui.card class="overflow-hidden p-0 relative">
            <!-- Loading Overlay -->
            <div wire:loading.flex wire:target="approve, reject" class="absolute inset-0 bg-slate-900/20 backdrop-blur-[1px] z-10 items-center justify-center">
                <div class="px-4 py-2 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] shadow-lg flex items-center space-x-2 text-xs font-semibold">
                    <svg class="w-4 h-4 animate-spin text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memproses persetujuan...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Alasan</th>
                            <th class="px-6 py-4">Bukti Lampiran</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Ditinjau Oleh</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                        @forelse($requests as $item)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition-all">
                                <td class="px-6 py-4 font-semibold whitespace-nowrap">{{ $item->date->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 font-bold whitespace-nowrap">
                                    <p>{{ $item->student->user->name ?? '-' }}</p>
                                    <p class="text-[10px] font-mono text-[var(--color-text-muted)]">NIS: {{ $item->student->nis ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4 font-semibold text-cyan-600 dark:text-cyan-400 whitespace-nowrap">{{ $item->type->label() }}</td>
                                <td class="px-6 py-4 max-w-xs" title="{{ $item->reason }}">
                                    <p class="line-clamp-2 italic text-[var(--color-text-muted)]">"{{ $item->reason }}"</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->attachment_path)
                                        <a href="{{ asset('storage/' . $item->attachment_path) }}" target="_blank" class="inline-flex items-center space-x-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                            <span>📎 Lihat File</span>
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
                                        <div class="font-semibold text-[var(--color-text)]">{{ $item->reviewer->name }}</div>
                                        <div class="text-[10px] inline-block px-2 py-0.5 rounded-full font-semibold {{ $item->reviewer->isWaliKelas() ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300' }}">
                                            {{ $item->reviewer->role->label() }}
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->status->value === 'pending')
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <button @click="confirmAction = 'approve'; confirmId = {{ $item->id }}; confirmStudentName = '{{ addslashes($item->student->user->name ?? 'Murid') }}'; confirmModalOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-sm transition-all whitespace-nowrap">
                                                ✓ Setujui
                                            </button>
                                            <button @click="confirmAction = 'reject'; confirmId = {{ $item->id }}; confirmStudentName = '{{ addslashes($item->student->user->name ?? 'Murid') }}'; confirmModalOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs shadow-sm transition-all whitespace-nowrap">
                                                ✕ Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-[var(--color-text-muted)] italic">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-[var(--color-text-muted)] space-y-2">
                                    <span class="text-3xl block opacity-50">📩</span>
                                    <p class="font-medium">Belum ada pengajuan izin di kelas ini.</p>
                                </td>
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

    <!-- Confirmation Modal -->
    <template x-if="confirmModalOpen">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4"
            @keydown.escape.window="confirmModalOpen = false"
            @click.self="confirmModalOpen = false">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-sm w-full shadow-2xl space-y-4 relative animate-in fade-in zoom-in duration-200">
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center text-2xl" :class="confirmAction === 'approve' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-300'">
                        <span x-text="confirmAction === 'approve' ? '✅' : '❌'"></span>
                    </div>
                    <h3 class="font-bold text-base text-[var(--color-text)]" x-text="confirmAction === 'approve' ? 'Setujui Pengajuan Izin?' : 'Tolak Pengajuan Izin?'"></h3>
                    <p class="text-xs text-[var(--color-text-muted)]">
                        Apakah Anda yakin ingin <span x-text="confirmAction === 'approve' ? 'menyetujui' : 'menolak'"></span> permohonan izin dari <strong class="text-[var(--color-text)]" x-text="confirmStudentName"></strong>?
                    </p>
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <button @click="confirmModalOpen = false" type="button" class="flex-1 py-2.5 rounded-xl border border-[var(--color-border)] text-xs font-semibold text-[var(--color-text)] hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        Batal
                    </button>
                    <button @click="if(confirmAction === 'approve') { $wire.approve(confirmId); } else { $wire.reject(confirmId); } confirmModalOpen = false;" type="button" class="flex-1 py-2.5 rounded-xl text-white text-xs font-semibold shadow-md transition-all flex items-center justify-center space-x-1" :class="confirmAction === 'approve' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-rose-600 hover:bg-rose-500'">
                        <span x-text="confirmAction === 'approve' ? '✓' : '✕'"></span>
                        <span>Ya, Konfirmasi</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
