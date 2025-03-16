@props([
    'variant' => 'primary',
    'size' => 'sm',
])

@php

    $base =
        'mb-0.5 inline-flex h-10 items-center justify-center gap-1 rounded-md bg-default px-3.5 text-sm text-default-text transition-colors duration-200 ease-in-out hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90 disabled:pointer-events-none disabled:opacity-50';

    $variantClass = [
        'primary' => 'hover:bg-primary',
    ][$variant];
    $sizeClass = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
    ][$size];

@endphp


<x-slot name="trigger">
    <button x-on:click="dropdownOpen = !dropdownOpen" class="{{ $base . $variantClass }}" >
        {{ $slot }}
    </button>
</x-slot>
