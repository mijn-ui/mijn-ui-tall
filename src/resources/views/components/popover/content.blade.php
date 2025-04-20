@props([
    'align' => 'center',
])

@php
    $base =
        'absolute p-1 bottom-full text-white border border-white rounded-md cursor-pointer select-none bg-primary hover:bg-primary/90';
    $alignClass = [
        'left' => 'left-0',
        'center' => 'bottom-full left-1/2 -translate-x-1/2 ',
        'right' => 'right-0',
    ][$align];

@endphp

<x-slot name="content">
    <div {{ $attributes->merge([
        'class' => "$base $alignClass",
    ]) }}>
        {{ $slot }}

    </div>
</x-slot>
