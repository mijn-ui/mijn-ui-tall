@props(['variant' => 'single'])

@php
    $paddingOpen = $variant === 'single' ? 'pl-52' : 'pl-80';
    $paddingClosed = $variant === 'single' ? 'pl-0' : 'pl-20';
@endphp

<main x-data :class="$store.sidebar.isOpen ? '{{ $paddingOpen }}' : '{{ $paddingClosed }}'"
      class="pt-14 transition-all duration-300">
    {{ $slot }}
</main>
