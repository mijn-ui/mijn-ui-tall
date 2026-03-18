@aware([
    'color' => 'default',
    'variant' => 'default',
])

@php
    use Mijnui\Mijnui\Support\ColorHelper;

    $variant = ColorHelper::normalizeVariant($variant);
    $base = 'w-full text-base font-semibold leading-none mb-1';
    $colorClass = ColorHelper::alertTextColor($color, $variant);
@endphp

<h5 {{ $attributes->merge(['class' => "$base $colorClass"]) }}>
    {{ $slot }}
</h5>
