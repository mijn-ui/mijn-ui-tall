@aware(['color', 'variant'])

@props([
    'id' => 'alert-dialog',
    'color' => 'default',
    'variant' => 'default',
])

@php

    $base = 'relative w-full max-w-lg rounded-xl border p-6 shadow-lg';

    $variantClass = [
        'default' => 'border',
        'outlined' => 'border',
        'filled' => 'border-0',
    ][$variant];

    $colorClasses = [
        'default' => [
            'default' => 'border-default-text/20 text-main-text bg-accent',
            'outlined' => 'border-foreground text-foreground',
            'filled' => 'bg-foreground text-white',
        ],
        'primary' => [
            'default' => 'border-primary-text/20 text-main-text bg-accent',
            'outlined' => 'border-foreground text-foreground',
            'filled' => 'bg-foreground text-white',
        ],
        'secondary' => [
            'default' => 'border-secondary-text/20 text-main-text bg-accent',
            'outlined' => 'border-foreground text-foreground',
            'filled' => 'bg-foreground text-white',
        ],
        'success' => [
            'default' => 'border-success text-success bg-success/70',
            'outlined' => 'border-success text-success',
            'filled' => 'bg-success text-white',
        ],
        'info' => [
            'default' => 'border-info text-info bg-info/20 dark:bg-info/10',
            'outlined' => 'border-info text-info',
            'filled' => 'bg-info text-white',
        ],
        'warning' => [
            'default' => 'border-warning text-warning bg-warning/20 dark:bg-warning/10',
            'outlined' => 'border-warning text-warning',
            'filled' => 'bg-warning text-warning-foreground',
        ],
        'error' => [
            'default' => 'border-danger text-danger bg-danger/20 dark:bg-danger/10',
            'outlined' => 'border-danger text-danger',
            'filled' => 'bg-danger text-white',
        ],
    ][$color][$variant];

@endphp
<div x-cloak x-on:keydown.escape.window="open = false" role="dialog" aria-labelledby="{{ $id }}-title"
    aria-describedby="{{ $id }}-description"
    x-bind:class="open ? 'opacity-100' : 'opacity-0 pointer-events-none'"
    class="transition fixed inset-0 flex items-center justify-center bg-black/80 z-50">
    <div x-show="open" x-on:click.outside="open = false"
        {{ $attributes->merge(['class' => "$base $colorClasses $variantClass"]) }}>
        <button x-on:click="open = false" aria-label="Close" class="absolute top-2 right-2 size-8">
            <mijnui:icon class="hgi hgi-stroke hgi-cancel-01 text-gray-500 hover:text-gray-700">
            </mijnui:icon>
        </button>

        @isset($header)
            {{ $header }}
        @endisset

        @isset($body)
            {{ $body }}
        @endisset

        @isset($footer)
            {{ $footer }}
        @endisset
    </div>
</div>
