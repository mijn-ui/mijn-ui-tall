@props([
    'active' => false,
    'disabled' => false,
    'href' => null,
    'value' => null
])

@php
    $baseClasses = 'inline-flex h-9 items-center gap-1.5 border-b px-3 text-sm font-normal leading-none text-secondary-foreground outline-none duration-300 ease-in-out cursor-pointer hover:bg-secondary focus-visible:bg-secondary active:bg-secondary/70 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50';

    $activeClasses = 'data-[state=active]:border-b-2 data-[state=active]:border-b-border-primary data-[state=active]:font-medium data-[state=active]:text-primary-emphasis data-[state=active]:hover:bg-transparent data-[state=active]:hover:text-primary-emphasis';

    $dataState = $active ? 'active' : 'inactive';

    $classes = $baseClasses . ' ' . $activeClasses;
@endphp

@if ($href)
    <a href="{{ $href }}"
       role="tab"
       aria-selected="{{ $active ? 'true' : 'false' }}"
       tabindex="{{ $active ? '0' : '-1' }}"
       data-state="{{ $dataState }}"
       {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </a>
@else
    <button x-data="{value : @js($value)}" x-on:click="currentValue = value"
        x-bind:data-state=" (currentValue == value ? 'active' : null) ?? @js($dataState)"
        role="tab"
        :aria-selected="(currentValue == value ? 'true' : null) ?? @js($active ? 'true' : 'false')"
        :tabindex="(currentValue == value || @js($active)) ? '0' : '-1'"
        {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </button>
@endif
