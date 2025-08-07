@php
    $base = 'text-base font-semibold leading-none tracking-tight mb-2';
@endphp

<h3 {{ $attributes->merge(['class' => "$base"]) }}>
    {{ $slot }}
</h3>
