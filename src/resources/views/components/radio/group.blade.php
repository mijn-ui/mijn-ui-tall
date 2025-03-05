@props([
    'class' => '',
    'name' => $attributes->whereStartsWith('wire:model')->first(),
])

@php
    // Base styles for the radio group
    $base = 'space-y-3';
@endphp

    <!-- RadioGroup -->
<div {{ $attributes->merge(['class' => "$base $class"]) }}>
    @if($name)
        <input
            type="hidden"
            {{ $attributes->whereStartsWith('wire:model') }}
            name="{{ $name }}"
        />
    @endif

    {{ $slot }}
</div>
