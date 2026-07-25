<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Pengaturan Geofencing & Lokasi Sekolah</h2>
            <p class="text-sm text-[var(--color-text-muted)]">Atur koordinat lokasi sekolah (latitude, longitude) & radius jangkauan presensi</p>
        </div>

        <button wire:click="openCreate" type="button" class="px-4 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-sm font-semibold shadow-md">
            + Tambah Titik Lokasi
        </button>
    </div>

    @if($successMessage)
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
            ✓ {{ $successMessage }}
        </div>
    @endif

    <!-- Locations List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($locations as $loc)
            <x-ui.card class="space-y-4 relative">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl {{ $loc->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-bold text-lg">
                            📍
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-[var(--color-text)]">{{ $loc->name }}</h3>
                            <x-ui.badge :type="$loc->is_active ? 'success' : 'neutral'" :value="$loc->is_active ? 'Aktif' : 'Non-Aktif'" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button wire:click="openEdit({{ $loc->id }})" type="button" class="text-xs font-semibold text-[var(--color-primary)] hover:underline">
                            Edit
                        </button>
                        <button wire:click="delete({{ $loc->id }})" wire:confirm="Hapus lokasi ini?" type="button" class="text-xs font-semibold text-rose-600 hover:underline">
                            Hapus
                        </button>
                    </div>
                </div>

                <div class="bg-[var(--color-bg)] rounded-xl p-3 text-xs space-y-1 font-mono">
                    <p><span class="text-[var(--color-text-muted)] font-sans">Latitude:</span> {{ $loc->latitude }}</p>
                    <p><span class="text-[var(--color-text-muted)] font-sans">Longitude:</span> {{ $loc->longitude }}</p>
                    <p><span class="text-[var(--color-text-muted)] font-sans">Radius Geofence:</span> <strong class="text-indigo-600 dark:text-indigo-400 font-sans">{{ $loc->radius_meters }} Meter</strong></p>
                </div>

                <div class="flex items-center justify-between pt-2 text-xs border-t border-[var(--color-border)]">
                    <a href="https://maps.google.com/?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" class="text-[var(--color-primary)] font-semibold hover:underline">
                        🗺️ Buka di Google Maps ↗
                    </a>

                    <button wire:click="toggleActive({{ $loc->id }})" type="button" class="text-xs font-medium text-[var(--color-text-muted)] hover:text-[var(--color-text)]">
                        Toggle Status Aktif
                    </button>
                </div>
            </x-ui.card>
        @empty
            <x-ui.card class="md:col-span-2 text-center py-12">
                <p class="text-sm text-[var(--color-text-muted)]">Belum ada titik lokasi sekolah yang terdaftar. Klik "+ Tambah Titik Lokasi" untuk mengatur geofence.</p>
            </x-ui.card>
        @endforelse
    </div>

    <!-- Create / Edit Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg text-[var(--color-text)]">{{ $locationId ? 'Edit Titik Lokasi' : 'Tambah Titik Lokasi Baru' }}</h3>
                    <button wire:click="$set('showForm', false)" type="button" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Nama Lokasi / Gerbang</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="Contoh: Gerbang Utama Sekolah">
                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-data="{
                        detectCoords() {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition((pos) => {
                                    $wire.set('latitude', pos.coords.latitude.toString());
                                    $wire.set('longitude', pos.coords.longitude.toString());
                                });
                            }
                        }
                    }">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Latitude</label>
                            <input type="text" wire:model="latitude" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="-6.200000">
                            @error('latitude') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Longitude</label>
                            <input type="text" wire:model="longitude" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]" placeholder="106.816666">
                            @error('longitude') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2 text-right">
                            <button @click="detectCoords()" type="button" class="text-xs font-semibold text-[var(--color-primary)] hover:underline">
                                📍 Ambil Koordinat GPS Saat Ini
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text)] uppercase mb-1">Radius Toleransi Geofence (Meter)</label>
                        <input type="number" wire:model="radiusMeters" min="10" max="5000" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-xs text-[var(--color-text)]">
                        <p class="text-[10px] text-[var(--color-text-muted)] mt-1">Presensi murid hanya valid jika berada dalam radius ini dari titik koordinat.</p>
                        @error('radiusMeters') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" wire:model="isActive" id="is_active_chk" class="rounded text-[var(--color-primary)]">
                        <label for="is_active_chk" class="text-xs font-semibold text-[var(--color-text)] cursor-pointer">Aktifkan Lokasi Ini</label>
                    </div>

                    <div class="flex space-x-3 pt-3">
                        <button wire:click="$set('showForm', false)" type="button" class="w-1/2 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-semibold text-[var(--color-text)]">
                            Batal
                        </button>
                        <x-ui.button type="submit" variant="primary" size="md" class="w-1/2">
                            Simpan Lokasi
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
