@props([
    'size' => 'md',
])

@php
    $base = '';

    $sizeClass = [
        'sm' => 'min-w-32',
        'md' => 'min-w-48',
        'lg' => 'min-w-56',
        'xl' => 'min-w-64',
    ][$size] ?? 'min-w-40';
@endphp

<x-slot:body>
    <div {{ $attributes->merge(['class' => "$base $sizeClass"]) }}>
        {{ $slot }}
    </div>
</x-slot:body>
