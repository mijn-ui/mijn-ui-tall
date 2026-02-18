@props(['disabled' => false])

<button {{ $attributes }} type="button" @disabled($disabled)>
    {{ $slot }}
</button>