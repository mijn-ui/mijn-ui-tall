@props([
    'orientation' => 'horizontal',
])

@php
    $baseClass = 'bg-primary shrink-0';
    $orientationClass = $orientation === 'horizontal' ? 'h-divider w-full' : 'h-full w-divider';
@endphp
<div {{ $attributes->merge(['class' => "$baseClass $orientationClass"]) }} role="separator" aria-orientation="{{ $orientation }}"></div>
