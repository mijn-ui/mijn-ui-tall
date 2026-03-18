@props(['variant' => 'single'])

@php
    $paddingOpen = $variant === 'single' ? 'sm:pl-52' : 'sm:pl-80';
    $paddingClosed = $variant === 'single' ? 'sm:pl-0' : 'sm:pl-20';
@endphp

<main x-data :class="$store.sidebar.isOpen ? @js($paddingOpen) : @js($paddingClosed)"
    class="pt-14 transition-all duration-300 pl-0">
    {{ $slot }}
</main>