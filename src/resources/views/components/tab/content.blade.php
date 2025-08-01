@props([
    'value' => null,
])

<div x-show="currentValue == '{{ $value }}'">

    {{ $slot }}
</div>
