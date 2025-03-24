@props(['title'])

@php
    $base = "text-lg font-semibold";
@endphp

<h2 id="-title" {{$attributes->merge([
    'class' => $base
])}}>
    {{ $title ?? $slot }}
</h2>
