@props([
    'color' => 'primary',
    'variant' => 'default',
    'size' => 'sm',
    'hasLoading' => false,
    'rounded' => 'md',
    'disabled' => false,
    'justify' => 'center',
    'items' => 'center',
])

@php
    // Base styles for the button
    $base =
        'cursor-pointer inline-flex flex-wrap items-center gap-2 justify-center text-sm font-medium transition-all duration-200 ease-in-out active:brightness-90 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main';

    $colorClasses = [
        'default' => [
            'default' => 'shadow-xs bg-inverse text-inverse-foreground hover:bg-inverse/80',
            'outline' => 'border border-inverse text-inverse-foreground hover:bg-inverse/10',
            'subtle' => 'border border-inverse bg-inverse-subtle hover:bg-inverse/20 text-inverse-foreground-subtle',
            'ghost' => 'text-inverse-foreground hover:bg-inverse-subtle dark:hover:bg-inverse-subtle/30',
        ],
        'primary' => [
            'default' => 'shadow-xs bg-primary text-primary-foreground hover:bg-primary/80',
            'outline' => 'border border-primary text-primary hover:bg-primary/10',
            'subtle' => 'border border-primary bg-primary-subtle hover:bg-primary/20 text-primary',
            'ghost' => 'text-primary hover:bg-primary-subtle dark:hover:bg-primary-subtle/30',
        ],
        'secondary' => [
            'default' => 'shadow-xs bg-secondary text-secondary-foreground hover:bg-secondary/80',
            'outline' => 'border border-border-secondary text-secondary-foreground hover:bg-secondary/10',
            'subtle' => 'border border-border-secondary bg-secondary-subtle hover:bg-secondary/20 text-secondary-foreground',
            'ghost' => 'text-secondary-foreground hover:bg-secondary-subtle dark:hover:bg-secondary-subtle/30',
        ],
        'success' => [
            'default' => 'shadow-xs bg-success text-success-foreground hover:bg-success/80',
            'outline' => 'border border-success text-success hover:bg-success/10',
            'subtle' => 'border border-success bg-success-subtle hover:bg-success/20 text-success',
            'ghost' => 'text-success hover:bg-success-subtle dark:hover:bg-success-subtle/30',
        ],
        'info' => [
            'default' => 'shadow-xs bg-info text-info-foreground hover:bg-info/80',
            'outline' => 'border border-info text-info hover:bg-info/10',
            'subtle' => 'border border-info bg-info-subtle hover:bg-info/20 text-info',
            'ghost' => 'text-info hover:bg-info-subtle dark:hover:bg-info-subtle/30',
        ],
        'warning' => [
            'default' => 'shadow-xs bg-warning text-warning-foreground hover:bg-warning/80',
            'outline' => 'border border-warning text-warning hover:bg-warning/10',
            'subtle' => 'border border-warning bg-warning-subtle hover:bg-warning/20 text-warning',
            'ghost' => 'text-warning hover:bg-warning-subtle dark:hover:bg-warning-subtle/30',
        ],
        'danger' => [
            'default' => 'shadow-xs bg-danger text-danger-foreground hover:bg-danger/80',
            'outline' => 'border border-danger text-danger hover:bg-danger/10',
            'subtle' => 'border border-danger bg-danger-subtle hover:bg-danger/20 text-danger',
            'ghost' => 'text-danger-foreground hover:bg-danger-subtle dark:hover:bg-danger-subtle/30',
        ],
    ][$color][$variant] ?? 'shadow-xs bg-primary text-primary-foreground hover:bg-primary/80';

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
    ][$size] ?? 'h-9 px-3';

    $radiusClasses = [
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ][$rounded] ?? 'rounded-md';

    $target = $attributes->whereStartsWith('wire:target')->first();

    $justify =
        [
            'start' => 'justify-start',
            'center' => 'justify-center',
            'end' => 'justify-end',
            'between' => 'justify-between',
            'around' => 'justify-around',
            'evenly' => 'justify-evenly',
        ][$justify] ?? 'justify-center';

    $alignItems =
        [
            'start' => 'items-start',
            'center' => 'items-center',
            'end' => 'items-end',
            'baseline' => 'items-baseline',
            'stretch' => 'items-stretch',
        ][$items] ?? 'items-center';
@endphp

<button {{ $attributes->merge(['class' => "$base $colorClasses $sizeClasses $radiusClasses "]) }}
    @if ($disabled) disabled @endif>
    @if ($hasLoading)
        <div class="w-full flex {{ $justify }} {{ $alignItems }}" wire:loading.remove
            @if ($target) wire:target="{{ $target }}" @endif>
            {{ $slot }}
        </div>
        {{-- Loading spinner or text --}}
        <span wire:loading @if ($target) wire:target="{{ $target }}" @endif>
            <div class="flex items-center gap-px">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    class="animate-spin">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2">
                        <path d="M12 3c4.97 0 9 4.03 9 9" transform="rotate(360 12 12)" />
                        <path stroke-opacity="0.3"
                            d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z" />
                    </g>
                </svg>
                <p>Loading</p>
            </div>
        </span>
    @else
        <div class="w-full flex px-1 {{ $justify }} {{ $alignItems }}">
            {{ $slot }}
        </div>
    @endif
</button>
