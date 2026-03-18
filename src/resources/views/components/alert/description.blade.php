@aware([
    'color' => 'default',
    'variant' => 'default',
])

@php
    use Mijnui\Mijnui\Support\ColorHelper;

    $variant = ColorHelper::normalizeVariant($variant);
    $base = 'text-sm opacity-90';
    $colorClass = ColorHelper::alertTextColor($color, $variant);
@endphp

<p {{ $attributes->merge(['class' => "$base $colorClass"]) }}>
    {{ $slot }}
</p>
