@props(['open' => false])

@php
    $base = 'w-full border-b';
@endphp

<div x-data="{ open: @json($open) }" {{$attributes->merge([
    'class' => $base
])}}>
    {{ $slot }}
</div>
