@props([
    'size' => null,
])

@php
    $sizeClass = match($size) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        'full' => 'max-w-full',
        default => 'max-w-md',
    };
@endphp

<div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-9999 flex items-center justify-center p-4 bg-black/50"
        aria-hidden="true"
        x-cloak>

        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="if (!persistent) open = false"
            @keydown.escape.window="if (!persistent) open = false"
            x-trap.noscroll="open"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="modalId + '-title'"
            {{ $attributes->merge(['class' => "bg-background-alt text-foreground rounded-lg shadow-lg w-full $sizeClass"]) }}>
            {{ $slot }}
        </div>
    </div>
