@props([
    // Button Title
    'title' => 'Add a new item'
])

<div class="space-y-4 px-4 py-2">
    {{ $slot }}
    <div class="relative flex items-center justify-between gap-4 px-4 py-2">
        <button class="flex items-center gap-2 text-sm text-muted-text">
            <span>
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" height="1em"
                    width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
            </span>
            {{ $title }}
        </button>
    </div>
</div>
