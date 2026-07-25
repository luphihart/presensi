@props([
    'variant' => 'primary', // primary, secondary, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
])

@php
$base = 'inline-flex items-center justify-center font-medium rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-[var(--color-primary)] text-white hover:opacity-90 focus:ring-indigo-500 shadow-sm',
    'secondary' => 'bg-slate-200 dark:bg-slate-700 text-[var(--color-text)] hover:bg-slate-300 dark:hover:bg-slate-600 focus:ring-slate-400',
    'danger' => 'bg-[var(--color-danger)] text-white hover:opacity-90 focus:ring-red-500 shadow-sm',
    'ghost' => 'text-[var(--color-text-muted)] hover:bg-slate-100 dark:hover:bg-slate-800 focus:ring-slate-400',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-6 py-3.5 text-base font-semibold',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
