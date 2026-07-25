@props([
    'title' => '',
    'value' => 0,
    'color' => 'primary', // primary, success, warning, danger, info
    'icon' => null
])

@php
$bg = match($color) {
    'success' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400',
    'warning' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
    'danger' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400',
    'info' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400',
    default => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400',
};
@endphp

<div class="bg-[var(--color-surface)] border border-[var(--color-border)] rounded-2xl p-5 shadow-sm flex items-center justify-between">
    <div>
        <p class="text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider mb-1">{{ $title }}</p>
        <h3 class="text-3xl font-bold text-[var(--color-text)]">{{ $value }}</h3>
    </div>
    @if($icon)
        <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $bg }}">
            {!! $icon !!}
        </div>
    @endif
</div>
