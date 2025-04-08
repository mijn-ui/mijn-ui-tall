@props(['name'])

<x-slot name="trigger">
    <div x-on:click="open = true;console.log(open)" {{ $attributes }}>
        {{ $slot }}
    </div>
</x-slot>
