<x-slot name="trigger">
    <div x-on:click="open = !open">
        {{ $slot }}
    </div>
</x-slot>