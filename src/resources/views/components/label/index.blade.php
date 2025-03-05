@props([
    'for' => '',
    'class' => '',
])
@php
    $base = 'text-small font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-disabled';
@endphp
<label {{ $attributes->merge(['class' => "$base $class"]) }} for="{{ $for }}">
    {{ $slot }}
</label>
