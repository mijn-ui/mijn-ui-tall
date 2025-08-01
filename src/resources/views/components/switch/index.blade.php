@props([
    'checked' => false,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex cursor-pointer items-center';
    $switchClasses =
        "peer relative h-6 w-11 rounded-full bg-muted after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-background after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full peer-focus:outline-none peer-disabled:cursor-not-allowed peer-disabled:opacity-50 peer-disabled:after:opacity-50 rtl:peer-checked:after:-translate-x-full";
@endphp

<label {{ $attributes->class([$baseClasses]) }}>
    <input type="checkbox" class="peer sr-only" {{ $checked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->whereStartsWith('wire:model') }} />
    <div class="{{ $switchClasses }}"></div>
</label>
