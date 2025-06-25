@php
    $base = 'p-6 flex flex-col space-y-1.5';
@endphp
<div {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</div>
