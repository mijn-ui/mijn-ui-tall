@php
    $base = 'p-6 pt-0';
@endphp

<div {{ $attributes->merge(['class' => "$base"]) }}>
    @isset($title)
        {{ $title }}
    @endisset

    @isset($description)
        {{ $description }}
    @endisset
    
    {{ $slot }}
</div>
