<x-slot:trigger>
    <button type="button" x-on:click='open = !open' x-ref="trigger" :aria-expanded="open" aria-haspopup="dialog" {{ $attributes }}>
        {{ $slot }}
    </button>
</x-slot:trigger>
