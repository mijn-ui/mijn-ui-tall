@php
    $base = 'p-6 pt-0';
@endphp

<div {{ $attributes->merge(['class' => "$base"]) }}>
    {{ $slot }}
</div>
