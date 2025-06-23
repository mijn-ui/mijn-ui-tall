@props([
    'class' => '',
])
@php
    $base = 'p-4 w-full rounded-lg bg-surface text-surface-text shadow-sm space-y-4';
@endphp
<div {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</div>
