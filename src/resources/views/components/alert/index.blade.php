@props([
    'variant' => 'default',
    'color' => 'default',
])

@php

    $base = "relative rounded-lg py-4 px-3 border border-main-border pr-9 w-full";

    $variantClass = [
        'default' => "border",
        'outlined' => "border",
        'filled' => "border-0",
    ][$variant];


    $colorClasses = [
        'default' => [
            'default' => "border-main-border text-main-text bg-background",
            'outlined' => "border-foreground text-foreground",
            'filled' => "bg-foreground text-white",
        ],
        'success' => [
            'default' => "border-success text-success bg-success/20 dark:bg-success/10",
            'outlined' => "border-success text-success",
            'filled' => "bg-success text-white",
        ],
        'info' => [
            'default' => "border-info text-info bg-info/20 dark:bg-info/10",
            'outlined' => "border-info text-info",
            'filled' => "bg-info  text-white",
        ],
        'warning' => [
            'default' => "border-warning text-warning bg-warning/20 dark:bg-warning/10",
            'outlined' => "border-warning text-warning",
            'filled' => "bg-warning text-warning-foreground",
        ],
        'error' => [
            'default' => "border-danger text-danger bg-danger/20 dark:bg-danger/10",
            'outlined' => "border-danger text-danger",
            'filled' => "bg-danger text-white",
        ],
    ][$color][$variant];
@endphp

<div {{ $attributes->merge(['class' => "$base $variantClass $colorClasses"]) }}>
    {{ $slot }}
</div>
