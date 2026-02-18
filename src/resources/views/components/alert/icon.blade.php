@props([
    'icon' => null,
    'class' => '',
])
@php
    $base = $icon
        ? '[&>i]:text-lg'
        : '[&>svg]:size-5';
@endphp
<span {{ $attributes->merge(['class' => "shrink-0 $base $class"]) }}>
@if ($icon)
    <i class="{{ $icon }}"></i>
@else
        {{ $slot }}
    @endif
</span>
