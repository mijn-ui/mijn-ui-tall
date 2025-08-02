@php
    $base = 'px-3 py-1.5 sm:px-4 sm:py-2';
@endphp

<x-slot:header>
    <div {{$attributes->merge([
        'class' => $base
    ])}}>
        {{ $slot }}
    </div>
</x-slot:header>
