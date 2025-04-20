@php
    $base = 'w-full max-w-80';
@endphp

<div {{$attributes->merge([
    'class'=> $base
])}}>
    {{ $slot }}
</div>
