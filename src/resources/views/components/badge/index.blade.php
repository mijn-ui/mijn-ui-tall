@props([
    'color' => 'default',
    'size' => 'md',
    'rounded' => 'full',
    'outline' => false,
    'ghost' => false,
])

@php
    // Base styles for the badge
    $base = 'inline-flex items-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring';

    $colorClasses = [
        'default' => $outline ? 'border border-main-border bg-transparent text-default-text hover:bg-accent hover:text-accent-text' : ($ghost ? 'bg-transparent text-default-text hover:bg-accent hover:text-accent-text' : 'bg-default text-default-text hover:bg-accent hover:text-accent-text shadow-sm'),
        'primary' => $outline ? 'border border-primary bg-transparent text-primary hover:bg-primary hover:text-primary-text' : ($ghost ? 'bg-transparent text-primary hover:bg-primary hover:text-primary-text' : 'bg-primary text-primary-text hover:opacity-hover'),
        'secondary' => $outline ? 'border border-secondary bg-transparent text-secondary hover:bg-secondary hover:text-secondary-text' : ($ghost ? 'bg-transparent text-secondary hover:bg-secondary hover:text-secondary-text' : 'bg-secondary text-secondary-text hover:opacity-hover'),
        'success' => $outline ? 'border border-success bg-transparent text-success hover:bg-success hover:text-success-filled-text' : ($ghost ? 'bg-transparent text-success hover:bg-success hover:text-success-filled-text' : 'bg-success text-success-filled-text hover:opacity-hover'),
        'info' => $outline ? 'border border-info bg-transparent text-info hover:bg-info hover:text-info-filled-text' : ($ghost ? 'bg-transparent text-info hover:bg-info hover:text-info-filled-text' : 'bg-info text-info-filled-text hover:opacity-hover'),
        'warning' => $outline ? 'border border-warning bg-transparent text-warning hover:bg-warning hover:text-warning-filled-text' : ($ghost ? 'bg-transparent text-warning hover:bg-warning hover:text-warning-filled-text' : 'bg-warning text-warning-filled-text hover:opacity-hover'),
        'danger' => $outline ? 'border border-danger bg-transparent text-danger hover:bg-danger hover:text-danger-filled-text' : ($ghost ? 'bg-transparent text-danger hover:bg-danger hover:text-danger-filled-text' : 'bg-danger text-danger-filled-text hover:opacity-hover'),
    ][$color];

    $sizeClasses = [
        'xs' => 'px-1.5 py-0.5 text-xs',
        'sm' => 'px-2 py-0.5 text-sm',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1 text-medium',
    ][$size];

    $roundedClasses = [
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ][$rounded];
@endphp

<div {{ $attributes->merge(['class' => "$base $colorClasses $sizeClasses $roundedClasses"]) }}>
    {{ $slot }}
</div>
