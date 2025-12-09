@php
    $attributes->onlyProps(['label', 'description', 'name']);
@endphp

@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'descriptionTrailing' => null,
    'description' => null,
    'label' => null,
    'badge' => null,
])

@if ($label || $description)
    <mijnui:field>
        @if ($label)
            <mijnui:label>{{ $label }}</mijnui:label>
        @endif

        @if ($description)
            <mijnui:description>{{ $description }}</mijnui:description>
        @endif

        {{ $slot }}

        <mijnui:error :$name />

        @if ($descriptionTrailing)
            <mijnui:description>{{ $descriptionTrailing }}</mijnui:description>
        @endif
    </mijnui:field>
@else
    {{ $slot }}
    <mijnui:error :$name />
@endif
