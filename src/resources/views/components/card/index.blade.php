@php
    $base = 'w-full rounded-lg overflow-hidden bg-surface text-surface-text shadow-sm';
@endphp
<div {{ $attributes->merge(['class' => "$base"]) }}>
    {{ $slot }}
</div>
