@php
    $base = 'flex flex-col items-center justify-start whitespace-nowrap rounded-lg bg-accent p-1 sm:flex-row';
@endphp

<x-slot:list>
    <div {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </div>
</x-slot:list>
