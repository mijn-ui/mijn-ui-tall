@props([
    'class' => '',
])
@php
    $base = "text-sm text-muted-text";
@endphp
<p {{ $attributes->merge(['class' => "$base $class"]) }}>
    {{ $slot }}
</p>
