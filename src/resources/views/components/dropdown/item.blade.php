@props([
    'variant' => 'primary',
    'size' => 'sm',
])

@php
    $base = 'relative flex select-none items-center gap-2 rounded-md hover:bg-primary/20 px-3 py-1.5 text-sm text-primary outline-none transition-colors sm:px-4 sm:py-2 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0';

    $variantClass = [
        'primary' => 'hover:bg-primary',
    ][$variant];
    $sizeClass = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
    ][$size];

@endphp

<a {{ $attributes->merge([
    'class' => $base . $variantClass,
]) }} role="menuitem" x-on:click="dropdownOpen  = false">
    {{ $slot }}
</a>
