<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-[var(--color-text)]">{{ $studentId ? 'Edit Data Murid' : 'Tambah Murid Baru' }}</h2>
        <a href="{{ route('admin.students.index') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]">← Kembali</a>
    </div>

    <x-ui.card>
        <form wire:submit="save" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Email Akun</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">NIS (Nomor Induk Siswa)</label>
                    <input type="text" wire:model="nis" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                    @error('nis') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Password {{ $studentId ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                <input type="password" wire:model="password" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Kelas</label>
                    <select wire:model="classRoomId" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                        <option value="0">Pilih Kelas</option>
                        @foreach($classRooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                    @error('classRoomId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                    <select wire:model="gender" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                    <input type="date" wire:model="birthDate" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
                    @error('birthDate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider mb-1.5">Nomor Telepon / WA (Opsional)</label>
                <input type="text" wire:model="phone" class="w-full px-4 py-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-primary)]">
            </div>

            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                Simpan Data Murid
            </x-ui.button>
        </form>
    </x-ui.card>
</div>
