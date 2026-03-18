<x-slot name="trigger">
    <button type="button" x-on:click="open = !open" :aria-expanded="open" aria-haspopup="dialog" {{ $attributes }}>
        {{ $slot }}
    </button>
</x-slot>
