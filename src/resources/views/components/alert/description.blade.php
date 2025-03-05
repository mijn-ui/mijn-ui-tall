@php
    $base = 'pl-8 mt-1 text-sm text-muted-text';
@endphp

<p {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</p>
