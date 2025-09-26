@props([
    'ariaLabel' => 'breadcrumb',
])

@php
    $base = 'flex flex-wrap items-center gap-1.5 break-words text-sm text-muted-text sm:gap-2.5';
@endphp

<nav>
    <ol {{ $attributes->merge(['class' => $base]) }} aria-label="{{ $ariaLabel }}">
        {{ $slot }}
    </ol>
</nav>
