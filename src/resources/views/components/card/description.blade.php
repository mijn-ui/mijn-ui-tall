@php
    $base = 'text-sm text-muted-text';
@endphp
<x-slot:description>
    <p {{ $attributes->merge(['class' => "$base"]) }}>
        {{ $slot }}
    </p>
</x-slot:description>
