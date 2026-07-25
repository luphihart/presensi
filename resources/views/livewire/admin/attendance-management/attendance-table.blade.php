<div class="space-y-6" x-data="{ previewPhotoUrl: null, previewPhotoTitle: '' }">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Manajemen Presensi Harian</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Tinjau foto presensi, lokasi GPS, dan koreksi manual data</p>
        </div>
    </div>

    <!-- Filters -->
    <x-ui.card class="p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase mb-1">Tanggal</label>
            <input type="date" wire:model.live="dateFilter" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase mb-1">Kelas</label>
            <select wire:model.live="classFilter" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                <option value="">Semua Kelas</option>
                @foreach($classRooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase mb-1">Status</label>
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                <option value="">Semua Status</option>
                <option value="hadir">Hadir</option>
                <option value="terlambat">Terlambat</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpa">Alpa</option>
            </select>
        </div>
    </x-ui.card>

    <!-- Table -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-[var(--color-border)] text-[var(--color-text-muted)] font-semibold uppercase">
                    <tr>
                        <th class="px-6 py-3.5">Murid</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5">Jam Masuk</th>
                        <th class="px-6 py-3.5">Jam Pulang</th>
                        <th class="px-6 py-3.5">Bukti Foto</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)]">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4">
                                <p class="font-bold text-sm">{{ $item->student->user->name }}</p>
                                <p class="text-[10px] text-[var(--color-text-muted)]">NIS {{ $item->student->nis }}</p>
                            </td>
                            <td class="px-6 py-4"><x-ui.badge type="info" :value="$item->student->classRoom->name" /></td>
                            <td class="px-6 py-4 font-mono">{{ $item->check_in_at ? $item->check_in_at->format('H:i') . ' WIB' : '-' }}</td>
                            <td class="px-6 py-4 font-mono">{{ $item->check_out_at ? $item->check_out_at->format('H:i') . ' WIB' : '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($item->check_in_photo_path)
                                        <div class="text-center group">
                                            <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $item->check_in_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Masuk - {{ addslashes($item->student->user->name) }} ({{ $item->check_in_at?->format('H:i') }} WIB)'" class="w-10 h-10 rounded-xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all">
                                                <img src="{{ asset('storage/' . $item->check_in_photo_path) }}" class="w-full h-full object-cover">
                                            </button>
                                            <span class="text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 block mt-0.5">Masuk</span>
                                        </div>
                                    @endif

                                    @if($item->check_out_photo_path)
                                        <div class="text-center group">
                                            <button type="button" @click="previewPhotoUrl = '{{ asset('storage/' . $item->check_out_photo_path) }}'; previewPhotoTitle = 'Foto Presensi Pulang - {{ addslashes($item->student->user->name) }} ({{ $item->check_out_at?->format('H:i') }} WIB)'" class="w-10 h-10 rounded-xl overflow-hidden block border border-[var(--color-border)] shadow-sm hover:scale-105 transition-all">
                                                <img src="{{ asset('storage/' . $item->check_out_photo_path) }}" class="w-full h-full object-cover">
                                            </button>
                                            <span class="text-[9px] font-semibold text-amber-600 dark:text-amber-400 block mt-0.5">Pulang</span>
                                        </div>
                                    @endif

                                    @if(!$item->check_in_photo_path && !$item->check_out_photo_path)
                                        <span class="text-[var(--color-text-muted)]">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.badge :type="strtolower($item->status->value)" :value="$item->status->label()" />
                                @if($item->is_manual_correction)
                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-medium mt-0.5">*Koreksi Admin</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="openCorrection({{ $item->id }})" type="button" title="Koreksi Manual" class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-[var(--color-primary)] hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteAttendance({{ $item->id }})" wire:confirm="Hapus data presensi ini?" type="button" title="Hapus Presensi" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-[var(--color-text-muted)]">Belum ada data presensi pada filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-border)]">
            {{ $attendances->links() }}
        </div>
    </div>

    <!-- Photo Preview Pop-up Modal -->
    <template x-if="previewPhotoUrl">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4"
            @keydown.escape.window="previewPhotoUrl = null"
            @click.self="previewPhotoUrl = null">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-5 max-w-md w-full shadow-2xl space-y-4 relative animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-sm text-[var(--color-text)]" x-text="previewPhotoTitle"></h3>
                    <button @click="previewPhotoUrl = null" type="button" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-[var(--color-text)] flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <div class="w-full aspect-[3/4] max-h-[65vh] rounded-2xl bg-slate-900 overflow-hidden flex items-center justify-center shadow-inner">
                    <img :src="previewPhotoUrl" class="w-full h-full object-contain">
                </div>

                <div class="text-center">
                    <button @click="previewPhotoUrl = null" type="button" class="px-6 py-2.5 rounded-xl bg-[var(--color-primary)] text-white font-semibold text-xs shadow-md">
                        Tutup Pratinjau
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Correction Modal -->
    @if($showCorrectionModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="font-bold text-lg text-[var(--color-text)]">Koreksi Manual Presensi</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Status Baru</label>
                    <select wire:model="correctionStatus" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpa">Alpa</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Alasan Koreksi (Audit Log)</label>
                    <textarea wire:model="correctionReason" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Wajib diisi alasan mengubah status..."></textarea>
                    @error('correctionReason') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex space-x-3 pt-2">
                    <button wire:click="$set('showCorrectionModal', false)" type="button" class="w-1/2 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-[var(--color-text)]">
                        Batal
                    </button>
                    <button wire:click="saveCorrection" type="button" class="w-1/2 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-xs font-semibold shadow-md">
                        Simpan Koreksi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
