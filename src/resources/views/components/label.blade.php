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
    ][$size] ?? 'text-sm';

@endphp

<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => $sizeClass]) }}>
    {{ $slot }}
</label>
