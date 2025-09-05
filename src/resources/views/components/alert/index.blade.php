@props([
    'variant' => 'default',
    'color' => 'default',
])

@php

    $base = 'relative rounded-lg py-4 px-3 pr-9 w-full';

    $variantClass = [
        'default' => 'border',
        'outlined' => 'border',
        'filled' => 'border-0',
    ][$variant];

    $colorClasses = [
        'default' => [
            'default' => 'border-inverse text-inverse bg-inverse-foreground/30',
            'outlined' => 'border-inverse text-inverse',
            'filled' => 'constant border-inverse bg-inverse text-inverse-foreground',
        ],
        'success' => [
            'default' => 'border-success text-success bg-success-foreground/30',
            'outlined' => 'border-success text-success',
            'filled' => 'constant border-success bg-success text-success-foreground',
        ],
        'info' => [
            'default' => 'border-info text-info bg-info-foreground/30',
            'outlined' => 'border-info text-info',
            'filled' => 'constant border-info bg-info text-info-foreground',
        ],
        'warning' => [
            'default' => 'border-warning text-warning bg-warning-foreground/30',
            'outlined' => 'border-warning text-warning',
            'filled' => 'constant border-warning bg-warning text-warning-foreground',
        ],
        'danger' => [
            'default' => 'border-danger text-danger bg-danger-foreground/30',
            'outlined' => 'border-danger text-danger',
            'filled' => 'constant border-danger bg-danger text-danger-foreground',
        ]
    ][$color][$variant];
@endphp

<div {{ $attributes->merge(['class' => "$base $variantClass $colorClasses"]) }}>
    {{ $slot }}
</div>
