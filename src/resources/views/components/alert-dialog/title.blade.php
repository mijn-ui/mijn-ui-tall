@php
    $base = 'text-lg font-semibold';
@endphp

<x-slot:title>
    <h2 {{ $attributes->merge(['class' => $base]) }}> {{ $slot }} </h2>
</x-slot:title>
