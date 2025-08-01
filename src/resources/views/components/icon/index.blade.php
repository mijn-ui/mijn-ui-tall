@props([
    'name' => null,
    'class' => null,
    'size' => 'md',
])

@php
    $wrapperClasses = 'inline-flex items-center justify-center';

    $iconSizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-lg',
        'lg' => 'text-xl',
    ];

    $svgSizes = [
        'xs' => 'size-3',
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
    ];

    $iconClasses = $iconSizes[$size] ?? 'text-lg';
    $svgClasses = $svgSizes[$size] ?? 'size-5';

@endphp

<?php if($name && !empty(trim($name))): ?>
<i {{ $attributes->merge(['class' => "$name $iconClasses $class"]) }}></i>
<?php else: ?>
<span {{ $attributes->merge(['class' => "$wrapperClasses $svgClasses $class"]) }}>
        {{ $slot }}
</span>
<?php endif; ?>
