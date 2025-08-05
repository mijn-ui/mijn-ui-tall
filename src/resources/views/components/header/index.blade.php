@php
    $base = 'z-50 sticky bg-background-alt top-0 flex w-full h-14 items-center justify-center border-b bg-surface';
@endphp

<x-slot:header>

    <header x-data :class="$store.sidebar.isOpen ? 'sm:left-52' : 'sm:left-0'" {{ $attributes->class([$base]) }}>
        {{ $slot }}
    </header>

</x-slot:header>
