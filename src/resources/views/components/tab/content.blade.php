@props([
    'value' => null,
])

<div x-cloak x-show="currentValue == @js($value)" role="tabpanel" tabindex="0">
    {{ $slot }}
</div>
