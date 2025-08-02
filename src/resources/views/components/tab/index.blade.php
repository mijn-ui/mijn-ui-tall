@props([
    'defaultValue' => null,
])

<div x-data="{ currentValue: '{{ $defaultValue }}' }" {{ $attributes }}>
    @isset($list)
        {{ $list }}
    @endisset
    <div>
        {{ $slot }}
    </div>
</div>
