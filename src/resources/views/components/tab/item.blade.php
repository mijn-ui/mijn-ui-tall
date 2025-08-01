

@props([
    'active' => false,
    'disabled' => false,
    'href' => null,
    'value' => null
])

@php
    $baseClasses = 'inline-flex h-9 items-center gap-1.5 border-b px-3 text-sm font-normal leading-none text-secondary-foreground outline-none duration-300 ease-in-out hover:bg-secondary focus-visible:bg-secondary active:bg-secondary/70 disabled:pointer-events-none disabled:opacity-50';

    $activeClasses = 'data-[state=active]:border-b-2 data-[state=active]:border-b-border-primary data-[state=active]:font-medium data-[state=active]:text-primary-emphasis data-[state=active]:hover:bg-transparent data-[state=active]:hover:text-primary-emphasis';

    $dataState = $active ? 'active' : 'inactive';

    $classes = $baseClasses . ' ' . $activeClasses;
@endphp

@if ($href)
    <a href="{{ $href }}"
       data-state="{{ $dataState }}"
       {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </a>
@else
    <button x-data="{value : '{{$value}}'}" x-on:click="currentValue = value"
        x-bind:data-state=" (currentValue == value ? 'active' : null) ?? '{{ $dataState }}'"
        {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </button>
@endif
