@props(['open' => false])

@php
    $base = 'w-full border-b';
    $accordionId = 'accordion-' . uniqid();
@endphp

<div x-data="{ open: @json($open), headerId: '{{ $accordionId }}-header', contentId: '{{ $accordionId }}-content' }" {{ $attributes->merge(['class' => $base]) }}>
    @isset($header)
        {{ $header }}
    @endisset

    @isset($content)
        {{ $content }}
    @endisset
</div>