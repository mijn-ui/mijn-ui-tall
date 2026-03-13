@aware(['size', 'persistent'])

@props([
    'size' => 'md',
    'persistent' => false,
])

@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-[calc(100vw-2rem)]',
        default => 'max-w-lg',
    };

    $isPersistent = filter_var($persistent, FILTER_VALIDATE_BOOLEAN);
@endphp

<div x-cloak x-show="open"
    x-on:keydown.escape.window="{{ $isPersistent ? '' : 'open = false' }}"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center bg-black justify-center z-50 p-4">
    <div @unless($isPersistent) x-on:click.outside="open = false" @endunless
        {{ $attributes->merge(['class' => "relative w-full $sizeClass rounded-xl border border-border bg-white z-50 p-6 shadow-lg"]) }}>
        {{ $slot }}
    </div>
</div>
