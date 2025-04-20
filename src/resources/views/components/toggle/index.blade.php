@props([
    'checked' => false, 
    'disabled' => false, 
])

@php
    $baseClasses = 'inline-flex cursor-pointer items-center';

    $switchClasses = 'peer relative h-6 w-11 rounded-full bg-accent shadow-sm after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-primary-text after:transition-all after:content-[""] peer-checked:bg-primary peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none rtl:peer-checked:after:-translate-x-full';

    $disabledClasses = 'peer-disabled:cursor-not-allowed peer-disabled:opacity-80 peer-disabled:after:opacity-80';
@endphp

<label {{ $attributes->merge(['class' => $baseClasses]) }}>
    <input
        {{$attributes->whereStartsWith('wire:model')}}
        type="checkbox"
        class="peer sr-only"
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    />
    <div class="{{ $switchClasses }} {{ $disabled ? $disabledClasses : '' }}"></div>
</label>