@php
    $base = "list-none space-y-1 p-1";
@endphp

<!-- List -->
<div {{$attributes->merge(['class' => "$base"])}}>
    {{ $slot }}
</div>
