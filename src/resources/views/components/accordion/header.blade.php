@php
    $base = 'group flex w-full items-center justify-between py-4';
@endphp

<x-slot:header>
    <button type="button" x-on:click="open = !open" {{ $attributes->merge([
        'class' => $base,
    ]) }}
        x-transition>
        <span>{{ $slot }}</span>
        <mijnui:icon class="transition hgi hgi-stroke hgi-arrow-down-01" x-bind:class="{ 'rotate-180' : open }" />
    </button>
</x-slot:header>
