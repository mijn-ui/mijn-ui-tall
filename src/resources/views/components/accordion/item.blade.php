@props(['open' => false])

@php
    $base = 'w-full border-b';
@endphp

<div x-data="{ open: @json($open) }" {{ $attributes->merge(['class' => $base]) }}>
    @isset($header)
        {{ $header }}
    @endisset

    @isset($content)
        {{ $content }}
    @endisset
</div>
