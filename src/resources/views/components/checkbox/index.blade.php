@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'color' => 'primary',
    'label' => '',
    'description' => '',
    'disabled' => false,
    'id' => null,
    'checked' => false,
    'value' => null,
])

@php
    $id ??= $name;

    $baseClasses =
        'before:content[""] peer relative h-5 w-5 cursor-pointer appearance-none rounded-[4px] border transition-all';

    $colors = [
        'primary' => [
            'input' => 'border-primary-outline checked:border-primary checked:bg-primary',
            'icon' => 'text-primary-foreground',
        ],
        'success' => [
            'input' => 'border-success-outline checked:border-success checked:bg-success',
            'icon' => 'text-success-foreground',
        ],
        'info' => [
            'input' => 'border-info-outline checked:border-info checked:bg-info',
            'icon' => 'text-info-foreground',
        ],
        'warning' => [
            'input' => 'border-warning-outline checked:border-warning checked:bg-warning',
            'icon' => 'text-warning-foreground',
        ],
        'danger' => [
            'input' => 'border-danger-outline checked:border-danger checked:bg-danger',
            'icon' => 'text-danger-foreground',
        ],
    ];

    $inputClasses = "{$baseClasses} {$colors[$color]['input']}";
    $iconClasses = "{$colors[$color]['icon']} h-4 w-4";
@endphp

<div {{ $attributes->class(['flex items-start gap-2'])->only(['class']) }}>
    <div class="inline-flex items-center gap-2">
        <label for="{{ $id }}" class="relative flex items-center">
            <input id={{ $id }} name="{{ $name }}" type="checkbox" value="{{ $value }}"
                @checked($checked) @disabled($disabled)
                {{ $attributes->except(['class', 'id', 'name', 'value']) }} class="{{ $inputClasses }}" />

            <span
                class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 transition-opacity peer-checked:opacity-100">
                <svg class="{{ $iconClasses }}" stroke="currentColor" fill="none" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </span>
        </label>
    </div>

    <div class="flex flex-col gap-1">
        @if ($label !== '')
            <label for="{{ $id }}" class="text-sm font-medium text-inverse">{{ $label }}</label>
        @endif

        @if ($description !== '')
            <p class="text-sm text-muted-text">{{ $description }}</p>
        @endif
    </div>
</div>
