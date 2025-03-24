@props([
    'class' => '',
])

@php
    $base = 'flex items-center gap-2';
@endphp

<nav x-data class="flex w-full items-center justify-between px-3 py-2">
    <div class="flex items-center gap-4">
        <button @click="$store.sidebar.toggle()"
            class="inline-flex h-8 w-8 items-center justify-center gap-1 rounded-md border border-main-border bg-transparent text-sm text-default-text transition-colors duration-200 ease-in-out hover:bg-accent hover:text-accent-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90 disabled:pointer-events-none disabled:opacity-50 sm:h-10 sm:w-10">
            <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M1.66699 9C1.66699 5.92572 1.66699 4.38858 2.34517 3.29897C2.59608 2.89585 2.90775 2.54522 3.26608 2.26295C4.23462 1.5 5.60097 1.5 8.33366 1.5H11.667C14.3997 1.5 15.766 1.5 16.7346 2.26295C17.0929 2.54522 17.4046 2.89585 17.6555 3.29897C18.3337 4.38858 18.3337 5.92572 18.3337 9C18.3337 12.0743 18.3337 13.6114 17.6555 14.701C17.4046 15.1041 17.0929 15.4548 16.7346 15.737C15.766 16.5 14.3997 16.5 11.667 16.5H8.33366C5.60097 16.5 4.23462 16.5 3.26608 15.737C2.90775 15.4548 2.59608 15.1041 2.34517 14.701C1.66699 13.6114 1.66699 12.0743 1.66699 9Z"
                    stroke="#737373"
                    stroke-width="1.5" />
                <path d="M7.91699 1.5L7.91699 16.5" stroke="#737373" stroke-width="1.5" stroke-linejoin="round" />
                <path
                    d="M4.16699 4.83337H5.00033M4.16699 7.33337H5.00033"
                    stroke="#737373"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

    </div>

    <div {{ $attributes->merge(['class' => "$base $class"]) }}>
       {{$slot}}
    </div>
</nav>
