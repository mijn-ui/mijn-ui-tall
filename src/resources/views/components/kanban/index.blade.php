@props([
    // Width options
    'width' => null,
    'maxWidth' => '352px',
])

@php
    // Handle width/maxWidth
    $widthStyle = '';
    if ($width) {
        $widthValue = is_numeric($width) ? "{$width}px" : $width;
        $widthStyle = "width: {$widthValue}";
    } else {
        $maxWidthValue = is_numeric($maxWidth) ? "{$maxWidth}px" : $maxWidth;
        $widthStyle = "max-width: {$maxWidthValue}; width: 100%;";
    }

    $containerClasses = "relative overflow-auto rounded-2xl bg-muted py-2";
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }} style="{{ $widthStyle }}">
    {{ $slot }}
</div>
