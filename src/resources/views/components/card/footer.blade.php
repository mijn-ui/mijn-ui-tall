@php
    $base = "flex items-center";
@endphp
<div {{ $attributes->merge(['class' => $base ]) }}>
    {{ $slot }}
</div>
