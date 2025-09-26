@props([
    'href' => config('app.url'),
])

<x-slot:logo>
    <a href="{{ $href }}" class="flex items-center justify-center py-3 w-full h-8 box-content border-b border-accent">
        {{ $slot }}
    </a>
</x-slot:logo>
