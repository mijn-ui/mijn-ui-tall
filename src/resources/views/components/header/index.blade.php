@props([
    'class' => '',
])
@php
    $base = 'fixed left-0 right-0 top-0 z-10 flex h-14 items-center justify-center border-b bg-surface';
@endphp

<header x-data :class="$store.sidebar.isOpen ? 'sm:left-52' : 'sm:left-0'" {{ $attributes->class([$base, $class]) }}>
    {{ $slot }}
</header>
