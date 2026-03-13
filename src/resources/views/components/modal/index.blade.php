@props([
    'size' => 'md',
    'persistent' => false,
    'name' => null,
    'open' => false,
])

<div x-data="{ open: @js(filter_var($open, FILTER_VALIDATE_BOOLEAN)) }"
    @if($name)
        x-on:modal-open.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}') open = true"
        x-on:modal-close.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}') open = false"
        x-on:modal-toggle.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}') open = ! open"
    @endif
    {{ $attributes }}>
    {{ $slot }}
</div>
