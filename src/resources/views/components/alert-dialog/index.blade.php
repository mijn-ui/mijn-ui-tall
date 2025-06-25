@props([
    'color' => 'default',
    'variant' => 'default',
])
@php
    $base = 'space-y-2';
@endphp

<div x-data="{ open: false }" {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</div>
