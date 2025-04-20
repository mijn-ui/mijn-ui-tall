@props([
    'variant' => 'default',
])

@php
    $base = 'flex flex-col items-center justify-center gap-1 whitespace-nowrap rounded-lg bg-accent p-1 sm:flex-row';
@endphp

<div {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</div>