@props([
    'name' => null,
    'class' => '',
    'size' => 'md', // Default size
])

@php
    // Determine if the icon is a Font Awesome icon
    $isFontAwesome = $name && str_starts_with($name, 'fa-');

    // Default classes for the icon wrapper
    $wrapperClasses = 'inline-flex items-center justify-center';

 // Size mapping for Font Awesome icons
    $fontAwesomeSizes = [
        'sm' => 'text-sm',
        'md' => 'text-lg',
        'lg' => 'text-xl',
    ];

    // Size mapping for custom SVG icons
    $svgSizes = [
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
    ];

    $fontAwesomeClasses = $fontAwesomeSizes[$size] ?? 'text-lg';
    $svgClasses = $svgSizes[$size] ?? 'size-5';

    if ($name && !str_starts_with($name, 'fa-')) {
        throw new Exception('Invalid Font Awesome icon name. Icon names must start with "fa-".');
    }
@endphp

<?php if($isFontAwesome)
    <!-- Render Font Awesome icon -->
    <i {{ $attributes->merge(['class' => "$name $fontAwesomeClasses $class"]) }}></i>
<?php else: ?> 
    <!-- Render custom SVG icon -->
    <span {{ $attributes->merge(['class' => "$wrapperClasses $class"]) }}>
        {{ $slot }}
    </span>
?>
