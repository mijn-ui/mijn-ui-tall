@props([
    'color' => 'default',
    'class' => '',
])

@php
    $base = 'bg-main-text text-main';
@endphp

<div x-on:click="open = false">
    <mijnui:button class="{{ $base . ' ' . $class }}">
        {{ $slot }}
    </mijnui:button>
</div>
