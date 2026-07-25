@props([
    'type' => 'info', // success, warning, danger, info, neutral
    'value' => null
])

@php
$color = match($type) {
    'success', 'hadir', 'approved' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
    'warning', 'terlambat', 'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
    'danger', 'alpa', 'rejected' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
    'info', 'izin', 'sakit' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300',
    default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ' . $color]) }}>
    {{ $slot->isEmpty() ? ucfirst($value ?? $type) : $slot }}
</span>
