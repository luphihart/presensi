<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[var(--color-text-main)] flex items-center gap-2">
                <span>🏆</span> Kedisiplinan & Prestasi
            </h1>
            <p class="text-sm text-[var(--color-text-muted)] mt-1">
                Peringkat kedisiplinan murid di {{ $classRoom ? 'Kelas ' . $classRoom->full_name : 'Sekolah' }}
            </p>
        </div>
    </div>

    <!-- Student Personal Summary Card -->
    @if($student)
        <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        @if($student->profile_photo_path)
                            <img src="{{ asset('storage/' . $student->profile_photo_path) }}" alt="{{ $student->user->name }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-white/30 shadow-sm">
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center font-bold text-xl text-white border-2 border-white/30">
                                {{ strtoupper(substr($student->user->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-1 -right-1 bg-amber-400 text-slate-900 text-xs font-extrabold px-1.5 py-0.5 rounded-full shadow">
                            #{{ $myRank ?? '-' }}
                        </span>
                    </div>

                    <div>
                        <h2 class="font-bold text-lg leading-tight">{{ $student->user->name }}</h2>
                        <p class="text-xs text-indigo-100 mt-0.5">
                            NIS: {{ $student->nis ?? '-' }} • {{ $classRoom ? $classRoom->full_name : 'Murid' }}
                        </p>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <div class="inline-flex items-center space-x-1 bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/20">
                        <span class="text-amber-300 font-bold">⚡</span>
                        <span class="font-extrabold text-lg text-white">{{ number_format($this->tab === 'monthly' ? $student->monthly_points : $student->total_points) }}</span>
                        <span class="text-xs text-indigo-100 font-medium">Poin</span>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-white/15 text-center text-xs">
                <div>
                    <span class="block text-indigo-200 font-medium">Peringkat</span>
                    <span class="font-bold text-base text-white">#{{ $myRank ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-indigo-200 font-medium">Poin Bulan Ini</span>
                    <span class="font-bold text-base text-amber-300">+{{ number_format($student->monthly_points) }}</span>
                </div>
                <div>
                    <span class="block text-indigo-200 font-medium">Badges Terbuka</span>
                    <span class="font-bold text-base text-white">{{ count($earnedBadges) }} / {{ count($allBadges) }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Leaderboard Section -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-5 shadow-sm space-y-6">
        <!-- Tabs & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[var(--color-border)]">
            <h3 class="font-bold text-base text-[var(--color-text-main)] flex items-center gap-2">
                <span>📊</span> Klasemen Kedisiplinan
            </h3>
            
            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-semibold">
                <button type="button" 
                        wire:click="setTab('monthly')"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab === 'monthly' ? 'bg-white dark:bg-slate-700 text-[var(--color-primary)] shadow-sm font-bold' : 'text-[var(--color-text-muted)] hover:text-[var(--color-text-main)]' }}">
                    Bulan Ini
                </button>
                <button type="button" 
                        wire:click="setTab('all_time')"
                        class="px-4 py-2 rounded-lg transition-all {{ $tab === 'all_time' ? 'bg-white dark:bg-slate-700 text-[var(--color-primary)] shadow-sm font-bold' : 'text-[var(--color-text-muted)] hover:text-[var(--color-text-main)]' }}">
                    Sepanjang Masa
                </button>
            </div>
        </div>

        <!-- Top 3 Podium (If available) -->
        @if($topThree->count() > 0)
            <div class="pt-2 pb-4">
                <div class="flex items-end justify-center gap-3 sm:gap-6 max-w-md mx-auto">
                    <!-- 2nd Place -->
                    @if($topThree->has(1))
                        @php $second = $topThree->get(1); @endphp
                        <div class="flex flex-col items-center text-center w-24 sm:w-28">
                            <div class="relative mb-2">
                                @if($second->profile_photo_path)
                                    <img src="{{ asset('storage/' . $second->profile_photo_path) }}" class="w-12 h-12 rounded-full object-cover border-2 border-slate-300 dark:border-slate-600 shadow">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold flex items-center justify-center border-2 border-slate-300 dark:border-slate-600">
                                        {{ strtoupper(substr($second->user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 bg-slate-300 text-slate-800 font-bold text-xs w-5 h-5 rounded-full flex items-center justify-center border border-white">{{ $second->computed_rank ?? 2 }}</span>
                            </div>
                            <p class="font-semibold text-xs text-[var(--color-text-main)] truncate max-w-full">{{ $second->user->name }}</p>
                            <span class="text-[11px] font-extrabold text-[var(--color-primary)] mt-0.5">
                                {{ number_format($tab === 'monthly' ? $second->monthly_points : $second->total_points) }} pt
                            </span>
                            <div class="w-full bg-slate-200 dark:bg-slate-700/60 rounded-t-xl h-16 mt-2 flex items-center justify-center font-bold text-slate-400 text-sm">
                                🥈
                            </div>
                        </div>
                    @endif

                    <!-- 1st Place (Center - Highest) -->
                    @if($topThree->has(0))
                        @php $first = $topThree->get(0); @endphp
                        <div class="flex flex-col items-center text-center w-28 sm:w-32 -mt-4">
                            <span class="text-xl mb-1 animate-bounce">👑</span>
                            <div class="relative mb-2">
                                @if($first->profile_photo_path)
                                    <img src="{{ asset('storage/' . $first->profile_photo_path) }}" class="w-16 h-16 rounded-full object-cover border-4 border-amber-400 shadow-md">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 font-extrabold text-lg flex items-center justify-center border-4 border-amber-400">
                                        {{ strtoupper(substr($first->user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 bg-amber-400 text-slate-900 font-extrabold text-xs w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow">{{ $first->computed_rank ?? 1 }}</span>
                            </div>
                            <p class="font-bold text-xs text-[var(--color-text-main)] truncate max-w-full">{{ $first->user->name }}</p>
                            <span class="text-xs font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">
                                {{ number_format($tab === 'monthly' ? $first->monthly_points : $first->total_points) }} pt
                            </span>
                            <div class="w-full bg-gradient-to-b from-amber-300 to-amber-400 dark:from-amber-500 dark:to-amber-600 rounded-t-xl h-24 mt-2 flex items-center justify-center font-bold text-white text-xl shadow">
                                🥇
                            </div>
                        </div>
                    @endif

                    <!-- 3rd Place -->
                    @if($topThree->has(2))
                        @php $third = $topThree->get(2); @endphp
                        <div class="flex flex-col items-center text-center w-24 sm:w-28">
                            <div class="relative mb-2">
                                @if($third->profile_photo_path)
                                    <img src="{{ asset('storage/' . $third->profile_photo_path) }}" class="w-12 h-12 rounded-full object-cover border-2 border-amber-700 dark:border-amber-600 shadow">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 font-bold flex items-center justify-center border-2 border-amber-700 dark:border-amber-600">
                                        {{ strtoupper(substr($third->user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 bg-amber-700 text-white font-bold text-xs w-5 h-5 rounded-full flex items-center justify-center border border-white">{{ $third->computed_rank ?? 3 }}</span>
                            </div>
                            <p class="font-semibold text-xs text-[var(--color-text-main)] truncate max-w-full">{{ $third->user->name }}</p>
                            <span class="text-[11px] font-extrabold text-[var(--color-primary)] mt-0.5">
                                {{ number_format($tab === 'monthly' ? $third->monthly_points : $third->total_points) }} pt
                            </span>
                            <div class="w-full bg-amber-200/80 dark:bg-amber-900/30 rounded-t-xl h-12 mt-2 flex items-center justify-center font-bold text-amber-800 dark:text-amber-300 text-sm">
                                🥉
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Full List / Rankings -->
        <div class="space-y-2">
            @forelse($allRankings as $index => $item)
                @php
                    $rankNumber = $item->computed_rank ?? ($index + 1);
                    $isMe = $student && $item->id === $student->id;
                    $pointsVal = $tab === 'monthly' ? $item->monthly_points : $item->total_points;
                @endphp

                <div class="flex items-center justify-between p-3.5 rounded-xl border transition-all {{ $isMe ? 'bg-[var(--color-primary-soft)] border-[var(--color-primary)] shadow-sm' : 'bg-slate-50/50 dark:bg-slate-800/40 border-[var(--color-border)] hover:bg-slate-100/60 dark:hover:bg-slate-800' }}">
                    <div class="flex items-center space-x-3 min-w-0">
                        <!-- Rank Badge -->
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0
                            @if($rankNumber === 1) bg-amber-400 text-slate-900 shadow-sm
                            @elseif($rankNumber === 2) bg-slate-300 dark:bg-slate-600 text-slate-900 dark:text-white
                            @elseif($rankNumber === 3) bg-amber-700 text-white
                            @else text-[var(--color-text-muted)] bg-slate-200/60 dark:bg-slate-700/60 @endif">
                            #{{ $rankNumber }}
                        </div>

                        <!-- User Info -->
                        @if($item->profile_photo_path)
                            <img src="{{ asset('storage/' . $item->profile_photo_path) }}" alt="{{ $item->user->name }}" class="w-9 h-9 rounded-full object-cover shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($item->user->name, 0, 2)) }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <p class="font-semibold text-sm text-[var(--color-text-main)] truncate">
                                {{ $item->user->name }}
                                @if($isMe)
                                    <span class="ml-1 text-xs bg-[var(--color-primary)] text-white px-2 py-0.5 rounded-full font-bold">Kamu</span>
                                @endif
                            </p>
                            <p class="text-xs text-[var(--color-text-muted)] truncate">
                                Streak: {{ $item->current_streak }} hari 🔥
                            </p>
                        </div>
                    </div>

                    <!-- Points -->
                    <div class="text-right shrink-0">
                        <span class="font-extrabold text-sm text-[var(--color-text-main)]">
                            {{ number_format($pointsVal) }}
                        </span>
                        <span class="text-xs text-[var(--color-text-muted)] block">poin</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-[var(--color-text-muted)] text-sm">
                    Belum ada data kedisiplinan murid.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Badges Collection Grid -->
    <div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-5 shadow-sm space-y-4">
        <div>
            <h3 class="font-bold text-base text-[var(--color-text-main)] flex items-center gap-2">
                <span>🏅</span> Koleksi Badges Kehadiran
            </h3>
            <p class="text-xs text-[var(--color-text-muted)] mt-1">
                Kumpulkan seluruh badge dengan rajin presensi dan mempertahankan kedisiplinan.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($allBadges as $key => $badge)
                @php
                    $isUnlocked = isset($earnedBadges[$key]);
                    $earnedDate = $isUnlocked ? \Carbon\Carbon::parse($earnedBadges[$key])->format('d M Y') : null;
                @endphp

                <div class="flex items-start space-x-3.5 p-3.5 rounded-xl border transition-all {{ $isUnlocked ? 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40' : 'bg-slate-50 dark:bg-slate-800/30 border-[var(--color-border)] opacity-60' }}">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 {{ $isUnlocked ? 'bg-amber-100 dark:bg-amber-900/50 shadow-sm' : 'bg-slate-200 dark:bg-slate-700 grayscale' }}">
                        {{ $badge['icon'] }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="font-bold text-sm text-[var(--color-text-main)] truncate">{{ $badge['name'] }}</h4>
                            @if($isUnlocked)
                                <span class="text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 rounded-full shrink-0">
                                    Terbuka
                                </span>
                            @else
                                <span class="text-[10px] font-medium bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 px-2 py-0.5 rounded-full shrink-0">
                                    Terkunci 🔒
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-[var(--color-text-muted)] mt-1 leading-snug">
                            {{ $badge['description'] }}
                        </p>
                        @if($isUnlocked && $earnedDate)
                            <p class="text-[10px] text-amber-700 dark:text-amber-400 font-medium mt-1">
                                Dapatkan: {{ $earnedDate }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
