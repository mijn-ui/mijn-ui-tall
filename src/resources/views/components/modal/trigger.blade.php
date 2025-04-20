@props(['name'])

<x-slot name="trigger">
    <div x-on:click="open = true" {{ $attributes }}>
        {{ $slot }}
    </div>
</x-slot>
