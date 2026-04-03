@php
    $base = 'flex items-start justify-between gap-4';
@endphp

<div {{ $attributes->merge(['class' => "p-4 pb-0 $base"]) }}>
    <div class="flex flex-col gap-y-1">
        {{ $slot }}
    </div>
    <button type="button" x-on:click="open = false"
        class="shrink-0 rounded-md p-1 text-muted-foreground hover:text-foreground transition-colors"
        aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>
