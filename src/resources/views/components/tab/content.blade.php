@props([
    'value' => null,
])

<div x-cloak x-show="currentValue == '{{ $value }}'">
    {{ $slot }}
</div>
