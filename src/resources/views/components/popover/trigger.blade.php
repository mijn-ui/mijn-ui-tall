<x-slot:trigger>
    <div x-on:click='open = !open' x-ref="trigger">
        {{ $slot }}
    </div>
</x-slot:trigger>
