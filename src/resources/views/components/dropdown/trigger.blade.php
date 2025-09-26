@props(['disabled' => false])

<x-slot:trigger>
    <button {{ $attributes }} type="button" @disabled($disabled)>
        {{ $slot }}
    </button>
</x-slot:trigger>
