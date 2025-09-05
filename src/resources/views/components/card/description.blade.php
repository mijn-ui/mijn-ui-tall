@php
    $base = 'text-sm text-secondary-foreground';
@endphp

<p {{ $attributes->merge(['class' => "$base"]) }}>
    {{ $slot }}
</p>
