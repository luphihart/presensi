<div class="space-y-4" x-data="cameraCapture($wire)">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-[var(--color-text)]">Presensi Masuk</h2>
        <a href="{{ route('student.dashboard') }}" class="text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]">← Kembali</a>
    </div>

    @if($successMessage)
        <div class="bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 rounded-3xl p-6 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto text-3xl font-bold shadow-lg shadow-emerald-500/30 animate-bounce">
                ✓
            </div>
            <h3 class="text-xl font-bold text-emerald-800 dark:text-emerald-200">Presensi Berhasil!</h3>
            <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ $successMessage }}</p>
            <a href="{{ route('student.dashboard') }}" class="inline-block px-6 py-3 rounded-2xl bg-emerald-600 text-white font-semibold text-sm shadow-md">
                Kembali ke Beranda
            </a>
        </div>
    @else
        <!-- Location Indicator -->
        <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-4 flex items-center justify-between shadow-sm" x-data="{
            init() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            $wire.setLocation(pos.coords.latitude, pos.coords.longitude);
                        },
                        (err) => {
                            console.error(err);
                        },
                        { enableHighAccuracy: true }
                    );
                }
            }
        }">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl {{ $isWithinGeofence ? 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400' }} flex items-center justify-center font-bold">
                    📍
                </div>
                <div>
                    <p class="text-xs font-semibold text-[var(--color-text)]">
                        {{ $isWithinGeofence ? 'Dalam Radius Sekolah' : 'Mendeteksi Lokasi...' }}
                    </p>
                    <p class="text-xs text-[var(--color-text-muted)]">
                        @if($distanceMeters !== null)
                            Jarak: {{ round($distanceMeters) }} meter dari sekolah
                        @else
                            Mohon izinkan akses GPS lokasi Anda
                        @endif
                    </p>
                </div>
            </div>
            @if($isWithinGeofence)
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
            @endif
        </div>

        @if($errorMessage)
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm">
                {{ $errorMessage }}
            </div>
        @endif

        <!-- Camera Section -->
        <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl p-4 shadow-sm text-center">
            <template x-if="!capturedImage">
                <div class="relative w-full aspect-[3/4] bg-slate-900 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center">
                    <video x-ref="video" autoplay playsinline class="w-full h-full object-cover transform -scale-x-100"></video>
                    
                    <template x-if="!isCameraActive">
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-4 bg-slate-900/90 text-white">
                            <svg class="w-12 h-12 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h0.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="text-sm font-medium mb-4">Kamera belum aktif</p>
                            <button @click="startCamera()" type="button" class="px-5 py-2.5 rounded-xl bg-[var(--color-primary)] text-white text-sm font-semibold shadow-md">
                                Buka Kamera Selfie
                            </button>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="capturedImage">
                <div class="relative w-full aspect-[3/4] bg-slate-900 rounded-2xl overflow-hidden shadow-md">
                    <img :src="capturedImage" class="w-full h-full object-cover">
                </div>
            </template>

            <div class="mt-4">
                <template x-if="isCameraActive && !capturedImage">
                    <button @click="capture()" type="button" @if($errorMessage) disabled class="w-full py-3.5 rounded-2xl bg-slate-300 dark:bg-slate-700 text-slate-400 font-bold text-base cursor-not-allowed" @else class="w-full py-3.5 rounded-2xl bg-[var(--color-primary)] text-white font-bold text-base shadow-lg shadow-indigo-500/30" @endif>
                        📸 Ambil Foto Selfie
                    </button>
                </template>

                <template x-if="capturedImage">
                    <div class="w-full flex flex-col sm:flex-row gap-3">
                        <button @click="retake()" type="button" class="w-full sm:w-1/2 py-3 rounded-xl bg-slate-200 dark:bg-slate-700 text-[var(--color-text)] font-semibold text-sm">
                            Ulangi Foto
                        </button>
                        <button wire:click="submitCheckIn" type="button" @if($errorMessage) disabled class="w-full sm:w-1/2 py-3 rounded-xl bg-slate-300 dark:bg-slate-700 text-slate-400 font-bold text-sm cursor-not-allowed" @else class="w-full sm:w-1/2 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md" @endif>
                            Kirim Presensi
                        </button>
                    </div>
                </template>
            </div>
        </div>
    @endif
</div>
