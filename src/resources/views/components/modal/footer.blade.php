@php
    $base = 'flex flex-col-reverse gap-2 sm:flex-row sm:justify-end';
@endphp

<div {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</div>