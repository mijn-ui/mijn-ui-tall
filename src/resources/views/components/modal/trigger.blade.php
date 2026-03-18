@props([])

<button type="button" x-on:click="open = true" :aria-expanded="open" aria-haspopup="dialog" {{ $attributes }}>
    {{ $slot }}
</button>
