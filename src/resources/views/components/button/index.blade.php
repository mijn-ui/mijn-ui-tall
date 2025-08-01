@props([
    'color' => 'default',
    'variant' => 'default',
    'size' => 'sm',
    'hasLoading' => false,
    'rounded' => 'md',
    'disabled' => false,
    'mijnuiSidebarParent' => '',
    'justify' => 'center',
    'items' => 'center',
])

@php
    // Base styles for the button
    $base =
        'flex flex-wrap items-center gap-2 justify-center text-sm transition-colors duration-200 ease-in-out active:brightness-90 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main';

    $colorClasses = [
        'default' => [
            'default' => 'constant bg-inverse text-inverse-foreground hover:bg-inverse/80',
            'outline' => 'border border-inverse-outline text-inverse-foreground hover:bg-inverse/10',
            'subtle' => 'bg-inverse-subtle hover:bg-inverse/20 text-inverse-foreground-subtle',
            'ghost' => 'text-inverse-foreground hover:bg-inverse-subtle dark:hover:bg-inverse-subtle/30',
        ],
        'primary' => [
            'default' => 'constant bg-primary text-primary-foreground hover:bg-primary/80',
            'outline' => 'border border-primary-outline text-primary-foreground hover:bg-primary/10',
            'subtle' => 'bg-primary-subtle hover:bg-primary/20 text-primary-foreground-subtle',
            'ghost' => 'text-primary-foreground hover:bg-primary-subtle dark:hover:bg-primary-subtle/30',
        ],
        'secondary' => [
            'default' => 'constant bg-secondary text-secondary-foreground hover:bg-secondary/80',
            'outline' => 'border border-secondary-outline text-secondary-foreground hover:bg-secondary/10',
            'subtle' => 'bg-secondary-subtle hover:bg-secondary/20 text-secondary-foreground-subtle',
            'ghost' => 'text-secondary-foreground hover:bg-secondary-subtle dark:hover:bg-secondary-subtle/30',
        ],
        'success' => [
            'default' => 'constant bg-success text-success-foreground hover:bg-success/80',
            'outline' => 'border border-success-outline text-success-foreground hover:bg-success/10',
            'subtle' => 'bg-success-subtle hover:bg-success/20 text-success-foreground-subtle',
            'ghost' => 'text-success-foreground hover:bg-success-subtle dark:hover:bg-success-subtle/30',
        ],
        'info' => [
            'default' => 'constant bg-info text-info-foreground hover:bg-info/80',
            'outline' => 'border border-info-outline text-info-foreground hover:bg-info/10',
            'subtle' => 'bg-info-subtle hover:bg-info/20 text-info-foreground-subtle',
            'ghost' => 'text-info-foreground hover:bg-info-subtle dark:hover:bg-info-subtle/30',
        ],
        'warning' => [
            'default' => 'constant bg-warning text-warning-foreground hover:bg-warning/80',
            'outline' => 'border border-warning-outline text-warning-foreground hover:bg-warning/10',
            'subtle' => 'bg-warning-subtle hover:bg-warning/20 text-warning-foreground-subtle',
            'ghost' => 'text-warning-foreground hover:bg-warning-subtle dark:hover:bg-warning-subtle/30',
        ],
        'danger' => [
            'default' => 'constant bg-danger text-danger-foreground hover:bg-danger/80',
            'outline' => 'border border-danger-outline text-danger-foreground hover:bg-danger/10',
            'subtle' => 'bg-danger-subtle hover:bg-danger/20 text-danger-foreground-subtle',
            'ghost' => 'text-danger-foreground hover:bg-danger-subtle dark:hover:bg-danger-subtle/30',
        ],
    ][$color][$variant];

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
    @if ($disabled) disabled @endif
    @if ($mijnuiSidebarParent) x-foreground:click="$store.sidebar.setActiveContent('{{ $mijnuiSidebarParent }}')" @endif>
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
