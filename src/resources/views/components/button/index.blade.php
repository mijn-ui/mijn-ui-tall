@props([
    'color' => 'default',
    'size' => 'md',
    'hasLoading' => false,
    'rounded' => 'md',
    'outline' => false,
    'subtle' => false,
    'ghost' => false,
    'disabled' => false,
    'mijnuiSidebarParent' => '',
])

@php
    // Base styles for the button
    $base =
        'inline-flex items-center justify-center gap-1 text-sm transition-colors duration-200 ease-in-out active:brightness-90 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main';

    $colorClasses = [
        'default' => $outline
            ? 'border bg-transparent border-main-border text-default-text hover:bg-accent hover:text-accent-text'
            : ($subtle
                ? 'bg-default/40 text-default-text'
                : ($ghost
                    ? 'text-default-text hover:bg-accent hover:text-accent-text'
                    : 'bg-default  text-default-text hover:opacity-hover')),
        'primary' => $outline
            ? 'border bg-transparent border-primary text-primary hover:bg-primary hover:text-primary-text'
            : ($subtle
                ? 'bg-primary/20 dark:bg-primary/10 text-primary'
                : ($ghost
                    ? 'text-primary hover:bg-primary hover:text-primary-text'
                    : 'bg-primary text-primary-text hover:opacity-hover')),
        'secondary' => $outline
            ? 'border bg-transparent border-secondary text-secondary hover:bg-secondary hover:text-secondary-text'
            : ($subtle
                ? 'bg-secondary/20 dark:bg-secondary/10 text-secondary'
                : ($ghost
                    ? 'text-secondary hover:bg-secondary hover:text-secondary-text'
                    : 'bg-secondary text-secondary-text hover:opacity-hover')),
        'success' => $outline
            ? 'border bg-transparent border-success text-success hover:bg-success hover:text-success-filled-text'
            : ($subtle
                ? 'bg-success/20 dark:bg-success/10 text-success'
                : ($ghost
                    ? 'text-success hover:bg-success hover:text-success-filled-text'
                    : 'bg-success text-success-filled-text hover:opacity-hover')),
        'info' => $outline
            ? 'border bg-transparent border-info text-info hover:bg-info hover:text-info-filled-text'
            : ($subtle
                ? 'bg-info/20 dark:bg-info/10 text-info'
                : ($ghost
                    ? 'text-info hover:bg-info hover:text-info-filled-text'
                    : 'bg-info text-info-filled-text hover:opacity-hover')),
        'warning' => $outline
            ? 'border bg-transparent border-warning text-warning hover:bg-warning hover:text-warning-filled-text'
            : ($subtle
                ? 'bg-warning/20 dark:bg-warning/10 text-warning'
                : ($ghost
                    ? 'text-warning hover:bg-warning hover:text-warning-filled-text'
                    : 'bg-warning text-warning-filled-text hover:opacity-hover')),
        'danger' => $outline
            ? 'border bg-transparent border-danger text-danger hover:bg-danger hover:text-danger-filled-text'
            : ($subtle
                ? 'bg-danger/20 dark:bg-danger/10 text-danger'
                : ($ghost
                    ? 'text-danger hover:bg-danger hover:text-danger-filled-text'
                    : 'bg-danger text-danger-filled-text hover:opacity-hover')),
    ][$color];

    $sizeClasses = [
        'xs' => 'h-8 px-2',
        'sm' => 'h-9 px-3',
        'md' => 'h-10 px-3.5',
        'lg' => 'text-base h-11 px-5',
        'icon-xs' => 'px-0 gap-0 size-8',
        'icon-sm' => 'px-0 gap-0 size-9',
        'icon-md' => 'px-0 gap-0 size-10',
        'icon-lg' => 'px-0 gap-0 size-11',
        'icon-xl' => 'px-0 gap-0 size-12',
    ][$size];

    $radiusClasses = [
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ][$rounded];

    $target = $attributes->whereStartsWith('wire:target')->first();
@endphp

<button {{ $attributes->merge(['class' => "$base $colorClasses $sizeClasses $radiusClasses"]) }}
    @if ($disabled) disabled @endif
    @if ($mijnuiSidebarParent) x-on:click="$store.sidebar.setActiveContent('{{ $mijnuiSidebarParent }}')" @endif>
    @if ($hasLoading)
        <span wire:loading.remove @if ($target) wire:target="{{ $target }}" @endif>
            {{ $slot }}
        </span>

        {{-- Loading spinner or text --}}
        <span wire:loading @if ($target) wire:target="{{ $target }}" @endif>
            <div class="flex items-center gap-px">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    class="animate-spin">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="M12 3c4.97 0 9 4.03 9 9" transform="rotate(360 12 12)" />
                        <path stroke-opacity="0.3"
                            d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z" />
                    </g>
                </svg>
                <p>Loading</p>
            </div>
        </span>
    @else
        <span>{{ $slot }}</span>
    @endif
</button>
