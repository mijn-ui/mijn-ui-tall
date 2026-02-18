@php
    $base = 'z-50 sticky bg-background-alt top-0 flex w-full h-14 items-center justify-center border-b';
@endphp

<x-slot:header>

    <header :class="$store.sidebar.isOpen ? 'sm:left-52' : 'sm:left-0'" {{ $attributes->class([$base, 'left-0']) }}>
        {{ $slot }}
    </header>

</x-slot:header>