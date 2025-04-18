@props([
    'for' => '',
    'class' => '',
])

@php
    $base = "text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-disabled";
@endphp

<label {{ $attributes->merge(['class' => $base]) }} for="{{ $for }}">
    {{ $slot }}
</label>
