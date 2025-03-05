@props([
    'lastPage' => null,
    'current' => null,
])

<button x-init {{ $current == $lastPage ? 'disabled' : '' }} x-on:click="changePage({{ $current + 1 }})"
    {{ $attributes->merge([
        'class' =>
            'inline-flex px-0 gap-0 size-8 items-center justify-center rounded-md text-sm hover:bg-accent hover:text-accent-text/80',
    ]) }}>
{{--    <span class="hidden sm:inline">Next</span>--}}
    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
        stroke-linejoin="round" class="w-5 h-5" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
        <path d="m9 18 6-6-6-6"></path>
    </svg>
</button>
