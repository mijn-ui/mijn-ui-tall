@php
    $base = 'group flex w-full items-center justify-between py-3';
@endphp

<button
    type="button"
    x-on:click="open = !open"
    {{$attributes->merge([
        'class' => $base
    ])}}
    x-transition
>
    <span>{{ $slot }}</span>
    <svg
        stroke="currentColor"
        fill="none"
        stroke-width="2"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="h-4 w-4 shrink-0 text-muted-text transition-transform duration-300"
        :class="open ? 'rotate-180' : ''"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path d="m6 9 6 6 6-6"></path>
    </svg>
</button>
