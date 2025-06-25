@php
    $base = 'text-sm text-muted-text';
@endphp
<p {{ $attributes->merge(['class' => "$base"]) }}>
    {{ $slot }}
</p>
