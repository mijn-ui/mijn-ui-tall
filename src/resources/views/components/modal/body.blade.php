@php
    $base = 'py-4 text-sm text-muted-foreground';
@endphp

<div {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</div>
