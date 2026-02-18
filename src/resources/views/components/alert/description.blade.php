@aware([
    'color' => 'default',
    'variant' => 'default',
])

@php
    $variant = $variant === 'outlined' ? 'outline' : ($variant === 'filled' ? 'default' : $variant);

    $base = 'text-sm opacity-90';

    $colorClass = [
        'default' => [
            'default' => 'text-inverse-foreground',
            'outline' => 'text-inverse',
            'subtle' => 'text-inverse',
            'ghost' => 'text-inverse',
        ],
        'primary' => [
            'default' => 'text-primary-foreground',
            'outline' => 'text-primary',
            'subtle' => 'text-primary',
            'ghost' => 'text-primary',
        ],
        'secondary' => [
            'default' => 'text-secondary-foreground',
            'outline' => 'text-secondary-foreground',
            'subtle' => 'text-secondary-foreground',
            'ghost' => 'text-secondary-foreground',
        ],
        'success' => [
            'default' => 'text-success-foreground',
            'outline' => 'text-success',
            'subtle' => 'text-success',
            'ghost' => 'text-success',
        ],
        'info' => [
            'default' => 'text-info-foreground',
            'outline' => 'text-info',
            'subtle' => 'text-info',
            'ghost' => 'text-info',
        ],
        'warning' => [
            'default' => 'text-warning-foreground',
            'outline' => 'text-warning',
            'subtle' => 'text-warning',
            'ghost' => 'text-warning',
        ],
        'danger' => [
            'default' => 'text-danger-foreground',
            'outline' => 'text-danger',
            'subtle' => 'text-danger',
            'ghost' => 'text-danger',
        ],
    ][$color][$variant];
@endphp

<p {{ $attributes->merge(['class' => "$base $colorClass"]) }}>
    {{ $slot }}
</p>
