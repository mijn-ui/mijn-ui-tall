@php
    $base = 'mt-4 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2';
@endphp

<x-slot:footer>
    <div {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </div>
</x-slot:footer>
