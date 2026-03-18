@props([
    'value',
    'state' => 'default', // default, current, active
    'isLast' => false,
])

@php
    $baseBtnClass = "flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border";

    $stateClass = [
        'default' => "text-muted-text text-sm",
        'current' => "border-main-text bg-surface text-sm",
        'active' => "border-main-text bg-main-text text-sm text-main",
    ][$state] ?? 'text-muted-text text-sm';

@endphp

<div class="flex w-full flex-col items-center gap-2 sm:flex-row">
    <button type="button" aria-label="Step {{ $value }}" {{ $attributes->merge(['class' => "$baseBtnClass $stateClass"]) }}>
    {{ $value }}
    </button>
    @if(!$isLast)
    <div class="h-8 w-px bg-main-border sm:h-px sm:w-full sm:max-w-40"></div>
    @endif
</div>
