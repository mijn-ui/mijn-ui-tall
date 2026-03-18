@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
])

@php
    $base = 'space-y-3';
@endphp

<div {{ $attributes->merge(['class' => $base]) }} role="radiogroup">
    @if($name)
        <input
            type="hidden"
            {{ $attributes->whereStartsWith('wire:model') }}
            name="{{ $name }}"
        />
    @endif

    {{ $slot }}
</div>
