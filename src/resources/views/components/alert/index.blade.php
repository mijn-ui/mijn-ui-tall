@props([
    'variant' => 'default',
    'color' => 'default',
])

@php
    use Mijnui\Mijnui\Support\ColorHelper;

    $variant = ColorHelper::normalizeVariant($variant);

    $base = 'relative flex gap-3 rounded-lg p-4 w-full';

    $colorClasses = [
        'default' => [
            'default' => 'bg-inverse text-inverse-foreground',
            'outline' => 'border border-inverse text-inverse',
            'subtle' => 'bg-inverse-subtle text-inverse border border-inverse/20',
            'ghost' => 'bg-transparent text-inverse',
        ],
        'primary' => [
            'default' => 'bg-primary text-primary-foreground',
            'outline' => 'border border-primary text-primary',
            'subtle' => 'bg-primary-subtle text-primary border border-primary/20',
            'ghost' => 'bg-transparent text-primary',
        ],
        'secondary' => [
            'default' => 'bg-secondary text-secondary-foreground',
            'outline' => 'border border-border-secondary text-secondary-foreground',
            'subtle' => 'bg-secondary-subtle text-secondary-foreground border border-border-secondary/20',
            'ghost' => 'bg-transparent text-secondary-foreground',
        ],
        'success' => [
            'default' => 'bg-success text-success-foreground',
            'outline' => 'border border-success text-success',
            'subtle' => 'bg-success-subtle text-success border border-success/20',
            'ghost' => 'bg-transparent text-success',
        ],
        'info' => [
            'default' => 'bg-info text-info-foreground',
            'outline' => 'border border-info text-info',
            'subtle' => 'bg-info-subtle text-info border border-info/20',
            'ghost' => 'bg-transparent text-info',
        ],
        'warning' => [
            'default' => 'bg-warning text-warning-foreground',
            'outline' => 'border border-warning text-warning',
            'subtle' => 'bg-warning-subtle text-warning border border-warning/20',
            'ghost' => 'bg-transparent text-warning',
        ],
        'danger' => [
            'default' => 'bg-danger text-danger-foreground',
            'outline' => 'border border-danger text-danger',
            'subtle' => 'bg-danger-subtle text-danger border border-danger/20',
            'ghost' => 'bg-transparent text-danger',
        ],
    ][$color][$variant] ?? 'bg-inverse text-inverse-foreground';
@endphp

<div {{ $attributes->merge(['class' => "$base $colorClasses"]) }} role="alert">
    <div class="flex-1">
        {{ $slot }}
    </div>
</div>
