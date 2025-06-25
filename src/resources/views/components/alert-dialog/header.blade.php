@php
    $base = 'flex flex-col gap-y-1 text-center sm:text-left';
@endphp

<x-slot:header>
    <div {{ $attributes->merge(['class' => $base]) }}>
        @isset($title)
            {{ $title }}
        @endisset

        @isset($description)
            {{ $description }}
        @endisset
    </div>
</x-slot:header>
