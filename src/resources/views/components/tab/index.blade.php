@props([
    'defaultValue' => null,
])

<div x-data="{ currentValue: @js($defaultValue) }" {{ $attributes }}>
    @isset($list)
        {{ $list }}
    @endisset
    <div>
        {{ $slot }}
    </div>
</div>
