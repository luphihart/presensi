<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Manajemen Data Murid</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Kelola akun dan data identitas murid seluruh kelas</p>
        </div>

        <div class="flex items-center space-x-2.5">
            <a href="{{ route('admin.students.import') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm flex items-center space-x-1.5">
                <span>📥 Import Excel</span>
            </a>
            <button wire:click="openCreate" type="button" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-sm font-semibold shadow-md">
                + Tambah Murid
            </button>
        </div>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <!-- Bulk Action Bar -->
    @if(count($selectedStudents) > 0)
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center space-x-2 text-xs font-bold text-rose-700 dark:text-rose-300">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                <span>Terpilih {{ count($selectedStudents) }} murid</span>
            </div>
            <button wire:click="deleteSelectedStudents" wire:confirm="Yakin ingin menghapus {{ count($selectedStudents) }} data murid terpilih sekaligus?" type="button" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus {{ count($selectedStudents) }} Murid Terpilih</span>
            </button>
        </div>
    @endif

    <!-- Filters & Search Bar -->
    <x-ui.card class="p-4 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, atau NIS..." class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
        </div>

        <div class="w-full md:w-64">
            <select wire:model.live="classFilter" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                <option value="">-- Semua Kelas --</option>
                @foreach($classRooms as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </x-ui.card>

    <!-- Student Table -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--color-border)] text-xs font-semibold text-[var(--color-text-muted)] uppercase bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-4 py-3.5 text-center w-10">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] cursor-pointer">
                        </th>
                        <th wire:click="sortBy('nis')" class="px-6 py-3.5 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700/40 select-none group transition-colors">
                            <div class="flex items-center space-x-1.5">
                                <span>NIS</span>
                                @if($sortColumn === 'nis')
                                    <span class="text-[var(--color-primary)] font-bold text-xs">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 opacity-50 group-hover:opacity-100 text-xs">↕</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('name')" class="px-6 py-3.5 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700/40 select-none group transition-colors">
                            <div class="flex items-center space-x-1.5">
                                <span>Nama Lengkap</span>
                                @if($sortColumn === 'name')
                                    <span class="text-[var(--color-primary)] font-bold text-xs">{{ $sortDirection === 'asc' ? '↑ (A-Z)' : '↓ (Z-A)' }}</span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 opacity-50 group-hover:opacity-100 text-xs">↕</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5">Gender</th>
                        <th class="px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)] text-[var(--color-text)] text-xs">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 {{ in_array((string)$student->id, $selectedStudents, true) ? 'bg-indigo-50/50 dark:bg-indigo-950/20' : '' }}">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" wire:model.live="selectedStudents" value="{{ $student->id }}" class="rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] cursor-pointer">
                            </td>
                            <td class="px-6 py-4 font-mono font-bold">{{ $student->nis }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold flex items-center justify-center">
                                        {{ substr($student->user->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-sm">{{ $student->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[var(--color-text-muted)]">{{ $student->user->email }}</td>
                            <td class="px-6 py-4"><x-ui.badge type="info" :value="$student->classRoom->name ?? '-'" /></td>
                            <td class="px-6 py-4">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="openEdit({{ $student->id }})" type="button" title="Edit Murid" class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-[var(--color-primary)] hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteStudent({{ $student->id }})" wire:confirm="Hapus data murid ini?" type="button" title="Hapus Murid" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-[var(--color-text-muted)]">Belum ada data murid.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-border)]">
            {{ $students->links() }}
        </div>
    </div>

    <!-- Student Edit/Create Modal Pop-up -->
    @if($showFormModal)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-3">
                    <h3 class="font-bold text-lg text-[var(--color-text)]">{{ $studentId ? 'Edit Data Murid' : 'Tambah Murid Baru' }}</h3>
                    <button wire:click="$set('showFormModal', false)" type="button" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form wire:submit="saveStudent" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Contoh: Budi Santoso">
                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">NIS</label>
                            <input type="text" wire:model="nis" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="2026001">
                            @error('nis') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Pilih Kelas</label>
                            <select wire:model="classRoomId" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classRooms as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('classRoomId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Email (untuk login)</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="budi@sekolah.sch.id">
                        @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">
                            Password {{ $studentId ? '(Kosongkan jika tidak ingin mengubah)' : '' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="••••••••">
                        @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Jenis Kelamin</label>
                            <select wire:model="gender" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('gender') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Tanggal Lahir</label>
                            <input type="date" wire:model="birthDate" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                            @error('birthDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex space-x-3 pt-3 border-t border-[var(--color-border)]">
                        <button wire:click="$set('showFormModal', false)" type="button" class="w-1/2 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-[var(--color-text)]">
                            Batal
                        </button>
                        <x-ui.button type="submit" variant="primary" size="md" class="w-1/2">
                            Simpan Murid
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
