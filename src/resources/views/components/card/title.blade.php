@props([
    'class' => '',
])
@php
    $base = "text-2xl font-semibold leading-none tracking-tight";
@endphp

<h3 {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</h3>
