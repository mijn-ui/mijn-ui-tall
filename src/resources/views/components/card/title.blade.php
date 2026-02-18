@php
    $base = 'text-base font-medium leading-tight tracking-tight mb-1.5';
@endphp

<h3 {{ $attributes->merge(['class' => "$base"]) }}>
    {{ $slot }}
</h3>