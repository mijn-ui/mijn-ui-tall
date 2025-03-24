@php
    $base = "flex w-full max-w-xl flex-col items-center justify-center gap-2 sm:flex-row";
@endphp

<div {{$attributes->merge(['class' => $base])}}>
    {{ $slot }}
</div>
