@props([
    'href' => null,
    'class' => '',
    'active' => false,
])

@php
    $base = 'text-default-text inline-flex h-10 w-full items-center justify-start gap-1 rounded-md px-3.5 text-sm transition-colors duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90 disabled:pointer-events-none disabled:opacity-50';
    $activeClass = 'bg-primary/20 text-primary';
    $hoverClass = 'hover:bg-accent hover:text-accent-text';
    $activeHoverClass = 'hover:bg-primary/30';
@endphp

@if($href)
    <a {{ $attributes->merge(['class' => "$base $class " . ($active ? "$activeClass $activeHoverClass" : $hoverClass), 'href' => $href]) }}>
        {{ $slot }}
    </a>
@else
<button {{ $attributes->merge(['class' => "$base $class " . ($active ? "$activeClass $activeHoverClass" : $hoverClass)]) }}>
    {{ $slot }}
</button>
@endif

