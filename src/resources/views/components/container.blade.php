@php
    $base = 'mx-auto w-full [:where(&)]:max-w-7xl px-6 lg:px-8';
@endphp

<div {{ $attributes->class($base) }} data-flux-container>
    {{ $slot }}
</div>
