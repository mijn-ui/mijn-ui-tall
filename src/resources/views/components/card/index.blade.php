@props([
    'class' => '',
])
@php
    $base = 'relative space-y-2 rounded-lg bg-surface p-4 shadow-sm';
@endphp
<div {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</div>
