<div class="space-y-8" x-data="{ previewPhotoUrl: null, previewPhotoTitle: '' }">
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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

    <!-- Live Attendance GPS Map Card -->
    <x-ui.card class="space-y-4" x-data="{
        map: null,
        schoolLat: @js($schoolLocation?->latitude ?? -6.200000),
        schoolLng: @js($schoolLocation?->longitude ?? 106.816666),
        schoolRadius: @js($schoolLocation?->radius_meters ?? 100),
        schoolName: @js($schoolLocation?->name ?? 'Sekolah'),
        students: @js($mapData),

        initMap() {
            if (this.map) return;
            
            // Initialize Leaflet Map
            this.map = L.map(this.$refs.mapContainer).setView([this.schoolLat, this.schoolLng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(this.map);

            // School Geofence Radius Circle
            L.circle([this.schoolLat, this.schoolLng], {
                color: '#4F46E5',
                fillColor: '#6366F1',
                fillOpacity: 0.15,
                radius: this.schoolRadius,
                weight: 2,
                dashArray: '5, 5'
            }).addTo(this.map);

            // School Marker
            const schoolIcon = L.divIcon({
                className: 'custom-school-pin',
                html: `<div class='w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-lg border-2 border-white text-lg animate-pulse'>🏢</div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            L.marker([this.schoolLat, this.schoolLng], { icon: schoolIcon })
                .addTo(this.map)
                .bindPopup(`<div class='p-1 text-center'><strong class='text-sm text-indigo-900'>🏢 ${this.schoolName}</strong><br><span class='text-xs text-slate-500'>Radius Presensi: ${this.schoolRadius} meter</span></div>`);

            // Add Student Attendance Markers
            this.students.forEach(s => {
                const color = s.status_val === 'terlambat' ? 'bg-amber-500' : 'bg-emerald-500';
                const badgeColor = s.status_val === 'terlambat' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800';

                const pinIcon = L.divIcon({
                    className: 'custom-student-pin',
                    html: `<div class='w-8 h-8 rounded-full ${color} text-white flex items-center justify-center font-bold text-xs shadow-md border-2 border-white transform hover:scale-125 transition-all'>📍</div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                let photoHtml = '';
                if (s.photo) {
                    photoHtml = `<div class='mt-2 text-center'><img src='${s.photo}' class='w-16 h-16 rounded-xl object-cover border mx-auto cursor-pointer shadow-sm' onclick="window.showMapPhoto('${s.photo}', 'Foto Presensi ${s.name}')"></div>`;
                }

                const popupContent = `
                    <div class='p-2 max-w-[200px] text-slate-800 text-xs space-y-1.5'>
                        <div class='flex items-center justify-between'>
                            <strong class='text-sm font-bold text-slate-900 block truncate'>${s.name}</strong>
                        </div>
                        <p class='text-[11px] text-slate-500'>Kelas ${s.class} • NIS ${s.nis}</p>
                        <div class='flex items-center justify-between pt-1'>
                            <span class='px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeColor} uppercase'>${s.status}</span>
                            <span class='font-mono text-[11px] text-slate-600'>${s.time}</span>
                        </div>
                        <p class='text-[10px] text-slate-500 italic pt-0.5'>Jarak: ${s.distance} m dari sekolah</p>
                        ${photoHtml}
                    </div>
                `;

                L.marker([s.lat, s.lng], { icon: pinIcon })
                    .addTo(this.map)
                    .bindPopup(popupContent);
            });

            // Adjust bounds if students exist
            if (this.students.length > 0) {
                const group = L.featureGroup([
                    L.marker([this.schoolLat, this.schoolLng]),
                    ...this.students.map(s => L.marker([s.lat, s.lng]))
                ]);
                this.map.fitBounds(group.getBounds().pad(0.2));
            }
        }
    }" x-init="setTimeout(() => initMap(), 300)">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[var(--color-border)] pb-3">
            <div>
                <h3 class="text-base font-bold text-[var(--color-text)] flex items-center space-x-2">
                    <span>🗺️ Peta Lokasi Presensi Murid Realtime</span>
                </h3>
                <p class="text-xs text-[var(--color-text-muted)]">Pantau posisi GPS murid saat melakukan presensi masuk hari ini</p>
            </div>
            
            <div class="flex items-center space-x-3 text-xs">
                <span class="flex items-center space-x-1">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-[var(--color-text-muted)] font-medium">Hadir</span>
                </span>
                <span class="flex items-center space-x-1">
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                    <span class="text-[var(--color-text-muted)] font-medium">Terlambat</span>
                </span>
                <span class="flex items-center space-x-1">
                    <span class="w-3 h-3 rounded-full bg-indigo-600 inline-block"></span>
                    <span class="text-[var(--color-text-muted)] font-medium">Sekolah</span>
                </span>
            </div>
        </div>

        <!-- Map Container -->
        <div class="relative w-full h-[400px] rounded-2xl overflow-hidden border border-[var(--color-border)] bg-slate-900 shadow-inner z-10">
            <div x-ref="mapContainer" class="w-full h-full"></div>
        </div>

        <script>
            window.showMapPhoto = function(url, title) {
                const event = new CustomEvent('open-map-photo', { detail: { url: url, title: title } });
                window.dispatchEvent(event);
            };
        </script>
    </x-ui.card>

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

    <div x-on:open-map-photo.window="previewPhotoUrl = $event.detail.url; previewPhotoTitle = $event.detail.title"></div>
</div>
