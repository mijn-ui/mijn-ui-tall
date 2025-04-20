@props(['description'])

@php
    $base = 'text-sm text-gray-600';
@endphp

<p id="-description" {{$attributes->merge([
    'class' => $base
])}}>
    {{ $description ?? $slot }}
</p>
