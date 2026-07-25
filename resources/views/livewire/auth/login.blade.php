<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[var(--color-bg)]">
    
    <!-- LEFT PANEL: Hero Banner (Desktop Only) -->
    <div class="hidden lg:flex lg:w-7/12 xl:w-2/3 relative bg-gradient-to-br from-[#0F0C29] via-[#1E1B4B] to-[#2E1065] text-white p-12 xl:p-16 flex-col justify-between overflow-hidden select-none">
        
        <!-- Glowing Ambient Background Blobs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/3 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Decorative Grid Pattern -->
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

        <!-- Hero Header -->
        <div class="relative z-10">
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold tracking-wide uppercase text-indigo-200">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Presensi Digital Sekolah</span>
            </div>
        </div>

        <!-- Hero Main Content -->
        <div class="relative z-10 my-auto max-w-xl space-y-6">
            <h1 class="text-4xl xl:text-5xl font-extrabold tracking-tight leading-tight">
                Pencatatan Kehadiran <br>
                <span class="bg-gradient-to-r from-indigo-300 via-purple-200 to-pink-300 bg-clip-text text-transparent">Cerdas, Cepat & Akurat</span>
            </h1>
            <p class="text-indigo-200/80 text-base leading-relaxed">
                Kelola data presensi harian murid dan guru secara otomatis dengan verifikasi lokasi GPS dan swafoto.
            </p>

            <!-- Floating Feature Glass Cards -->
            <div class="pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-2 transform hover:-translate-y-1 transition-all duration-300">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/30 flex items-center justify-center text-lg">
                        📍
                    </div>
                    <h4 class="font-bold text-xs">Akurat GPS</h4>
                    <p class="text-[11px] text-indigo-200/70">Radius sekolah terpantau otomatis</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-2 transform hover:-translate-y-1 transition-all duration-300">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/30 flex items-center justify-center text-lg">
                        📸
                    </div>
                    <h4 class="font-bold text-xs">Selfie Realtime</h4>
                    <p class="text-[11px] text-purple-200/70">Kamera langsung aktif otomatis</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-2 transform hover:-translate-y-1 transition-all duration-300">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/30 flex items-center justify-center text-lg">
                        📊
                    </div>
                    <h4 class="font-bold text-xs">Rekap Matriks</h4>
                    <p class="text-[11px] text-emerald-200/70">Laporan bulanan Excel instan</p>
                </div>
            </div>
        </div>

        <!-- Hero Footer -->
        <div class="relative z-10 flex items-center justify-between text-xs text-indigo-300/60 border-t border-white/10 pt-6">
            <p>© {{ date('Y') }} Presensi Digital. Hak Cipta Dilindungi.</p>
            <div class="flex items-center space-x-4">
                <span class="flex items-center space-x-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> <span>Sistem Online</span></span>
            </div>
        </div>
    </div>


    <!-- RIGHT PANEL: Login Form Container -->
    <div class="w-full lg:w-5/12 xl:w-1/3 min-h-screen flex flex-col justify-between p-6 sm:p-12 relative bg-[var(--color-bg)]">
        
        <!-- Mobile Top Header -->
        <div class="lg:hidden flex items-center justify-between pb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2A10 10 0 002 12a10 10 0 0010 10 10 10 0 0010-10A10 10 0 0012 2zm0 18a8 8 0 110-16 8 8 0 010 16zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-[var(--color-text)]">Presensi Digital</h3>
                    <p class="text-[10px] text-[var(--color-text-muted)]">Aplikasi Sekolah Modern</p>
                </div>
            </div>
        </div>

        <!-- Main Form Card Section -->
        <div class="my-auto max-w-md w-full mx-auto space-y-8">
            
            <!-- Logo & Title Section -->
            <div class="text-center space-y-3">
                <!-- Pure Icon Logo Badge -->
                <div class="relative w-16 h-16 mx-auto">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white flex items-center justify-center shadow-xl shadow-indigo-500/30 transform hover:scale-105 transition-all duration-300">
                        <!-- Fingerprint SVG Icon -->
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-1.657 0-3 1.343-3 3v2m6-2c0-1.657-1.343-3-3-3m0 0C9.243 11 7 13.243 7 16v3m10-3c0-2.757-2.243-5-5-5m0 0C7.582 8 4 11.582 4 16v4m16-4c0-4.418-3.582-8-8-8s-8 3.582-8 8v4" />
                        </svg>
                    </div>
                    <!-- Badge Icon Checkmark Overlay -->
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 text-white border-2 border-[var(--color-bg)] flex items-center justify-center text-[10px] font-bold shadow-md">
                        ✓
                    </div>
                </div>

                <div class="space-y-1">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[var(--color-text)] tracking-tight">Selamat Datang 👋</h2>
                    <p class="text-xs sm:text-sm text-[var(--color-text-muted)]">Masukkan kredensial akun sekolah Anda</p>
                </div>
            </div>

            <!-- Error Message Alert -->
            @if($errorMessage)
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs sm:text-sm flex items-start space-x-3 shadow-sm animate-bounce-short">
                    <svg class="w-5 h-5 shrink-0 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <!-- Form -->
            <form wire:submit="login" class="space-y-5">
                
                <!-- Email Field -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider">Email Akun</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="email" wire:model="email" autofocus required
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent focus:outline-none transition-all duration-200 placeholder:text-slate-400"
                            placeholder="nama@sekolah.sch.id">
                    </div>
                    @error('email') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-[var(--color-text)] uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        
                        <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" required
                            class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent focus:outline-none transition-all duration-200 placeholder:text-slate-400"
                            placeholder="••••••••">

                        <!-- Show/Hide Password Toggle Button -->
                        <button type="button" wire:click="togglePassword" tabIndex="-1" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-[var(--color-text)] transition-colors">
                            @if($showPassword)
                                <!-- Eye Off Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.972 8.972 0 013.122-.563c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-3.32-3.32a3 3 0 01-4.242-4.242M9.88 9.88l4.24 4.24M3 3l18 18"/>
                                </svg>
                            @else
                                <!-- Eye Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password') <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between text-xs sm:text-sm pt-1">
                    <label class="flex items-center space-x-2 text-[var(--color-text-muted)] cursor-pointer select-none">
                        <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                        <span class="font-medium">Ingat Sesi Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 hover:from-indigo-500 hover:to-purple-600 text-white font-bold text-sm shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/40 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/30">
                    <span wire:loading.remove class="flex items-center justify-center space-x-2">
                        <span>Masuk Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                    <span wire:loading class="flex items-center justify-center space-x-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memproses Masuk...</span>
                    </span>
                </button>
            </form>
        </div>

        <!-- Footer Notice -->
        <div class="pt-6 text-center text-[11px] text-[var(--color-text-muted)]">
            <p>Platform Terintegrasi untuk Murid & Pengajar Sekolah</p>
        </div>
    </div>

</div>
