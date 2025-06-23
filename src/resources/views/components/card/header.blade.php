@php
    $base = 'flex flex-col space-y-1.5';
@endphp
<div {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</div>
