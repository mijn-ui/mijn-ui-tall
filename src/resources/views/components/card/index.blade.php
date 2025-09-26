@php
    $base = 'w-full rounded-lg bg-background-alt border border-border-secondary text-foreground shadow-sm';
@endphp

<div {{ $attributes->merge(['class' => "$base"]) }}>
    {{$slot}}
</div>
