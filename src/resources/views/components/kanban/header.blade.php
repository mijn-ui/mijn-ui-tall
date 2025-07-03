<!-- Kanban Header -->
@props([
    // Count badge options
    'count' => 0,
])

<div class="flex w-full items-center justify-between px-3 py-2">
    <div class="flex items-center gap-2">
        <h3 class="font-medium text-main-text sm:text-lg">{{ $slot }}</h3>
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-surface text-xs font-medium text-muted-text">
            {{ $count }}
        </span>
    </div>
    <button
        class="disabled:text-muted-text/75-text inline-flex h-7 w-7 items-center justify-center gap-1 rounded-full text-sm text-muted-text hover:bg-accent hover:text-main-text">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
            stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="1"></circle>
            <circle cx="12" cy="5" r="1"></circle>
            <circle cx="12" cy="19" r="1"></circle>
        </svg>
    </button>
</div>
