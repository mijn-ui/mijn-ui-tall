@props(['disabled' => false])

@php
    $triggerClasses =
        'inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';
@endphp

<x-slot:trigger>
    <button {{ $attributes->merge(['class' => $triggerClasses]) }} type="button" @disabled($disabled)>
        {{ $slot }}
    </button>
</x-slot:trigger>
