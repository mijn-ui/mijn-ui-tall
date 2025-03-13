@props([
    'class' => '',
])
@php
    $base = 'w-full rounded-lg bg-surface text-surface-text shadow-sm';
@endphp
<div {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</div>
