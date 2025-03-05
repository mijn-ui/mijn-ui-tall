@php
    $base = 'pl-8 w-full text-base font-semibold leading-none';
@endphp

<h5 {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</h5>
