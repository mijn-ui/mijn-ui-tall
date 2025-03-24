@props([
    'active' => false,
    'disabled' => false,
    'href' => null,
])

@php
    // Base classes for the tab button
    $baseClasses = 'inline-flex h-10 items-center justify-center gap-1 rounded-md px-3 py-2 text-sm drop-shadow-sm';

    // Variant classes based on active and disabled states
    $variantClasses = [
        'default' => [
            'active' => 'bg-surface text-main-text hover:bg-surface hover:text-main-text',
            'inactive' => 'bg-transparent text-muted-text hover:bg-surface hover:text-main-text',
        ],
        'disabled' => 'bg-muted/75 text-muted-text/75 cursor-not-allowed',
    ];

    // Apply classes based on props
    $classes = $baseClasses . ' ' . ($disabled
        ? $variantClasses['disabled']
        : ($active
            ? $variantClasses['default']['active']
            : $variantClasses['default']['inactive']));
@endphp
<?php if ($href): ?>
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
    {{ $slot }}
</a>
<?php else: ?> 
<button {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>
<?php endif; ?>