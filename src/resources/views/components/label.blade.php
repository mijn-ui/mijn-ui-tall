@props([
    'for' => null,
    'size' => 'sm',
])

@php

    $sizeClass = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'default' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
    ][$size];

@endphp

<label for="{{ $for }}" {{ $attributes->merge(['class' => $sizeClass]) }}>
    {{ $slot }}
</label>
