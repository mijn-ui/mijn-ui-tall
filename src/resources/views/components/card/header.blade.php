@props([
    'class' => '',
])
@php
    $base = "flex flex-col space-y-1.5 p-4";
@endphp
<div {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</div>
