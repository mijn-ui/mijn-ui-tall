@props([
    'class' => '',
])
@php
    $base = "p-4 pt-0";
@endphp
<div {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</div>
