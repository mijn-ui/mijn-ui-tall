@props([
    'class' => '',
])
@php
    $base = 'flex items-center gap-1 text-main-text  font-extrabold';
@endphp
<div class="flex h-10 items-center gap-2 px-2">
    <h5 {{ $attributes->merge(['class' => "$base $class"]) }}>
        {{ $slot }}
    </h5>
</div>
