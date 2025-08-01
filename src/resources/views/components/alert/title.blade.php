@aware([
    'color' => 'default',
    'variant' => 'default',
])

@php
    $base = 'pl-8 w-full text-base font-semibold leading-none';

     $colorClass = [
        'default' => [
            'default' => 'text-inverse-emphasis/70',
            'outlined' => 'text-inverse-emphasis/70',
            'filled' => 'text-inverse-emphasis dark:text-white',
        ],
        'success' => [
            'default' => 'text-success-emphasis/70',
            'outlined' => 'text-success-emphasis/70',
            'filled' => 'text-success-emphasis dark:text-white',
        ],
        'info' => [
            'default' => 'text-info-emphasis/70',
            'outlined' => 'text-info-emphasis/70',
            'filled' => 'text-info-emphasis dark:text-white',
        ],
        'warning' => [
            'default' => 'text-warning-emphasis/70',
            'outlined' => 'text-warning-emphasis/70',
            'filled' => 'text-warning-emphasis dark:text-white',
        ],
        'danger' => [
            'default' => 'text-danger-emphasis/70',
            'outlined' => 'text-danger-emphasis/70',
            'filled' => 'text-danger-emphasis dark:text-white',
        ],
    ][$color][$variant];
@endphp

<h5 {{ $attributes->merge(['class' => "$base $colorClass"]) }}>
    {{ $slot }}
</h5>
