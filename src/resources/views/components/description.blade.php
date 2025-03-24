@php
    $base = "text-xs text-muted-text";
@endphp

<p {{$attributes->merge(['class' => $base])}}>
    {{$slot}}
</p>
