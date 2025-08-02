@php
    $base = 'text-2xl font-semibold leading-none tracking-tight';
@endphp

<x-slot:title>
    <h3 {{ $attributes->merge(['class' => "$base"]) }}>
        {{ $slot }}
    </h3>
</x-slot:title>
