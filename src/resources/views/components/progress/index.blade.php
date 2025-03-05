@props([
    'label' => '',
    'value' => 75,
    'minLabel' => '',
    'maxLabel' => '',
])

@php
    $progressWidth = $value . '%';
@endphp

<div class="w-80 space-y-1">
    <?php if(is_string($label) && $label !== ''): ?>
    <div class="flex items-center justify-between text-sm font-medium text-main-text">
        <h5>{{ $label }}</h5>
        <p>{{ $value }}%</p>
    </div>
    <?php endif; ?>
    <div class="relative h-2 w-full overflow-hidden rounded-full bg-gray-200">
        <div
            class="h-full bg-primary"
            aria-valuemin="0"
            aria-valuemax="100"
            aria-valuenow="{{ $value }}"
            role="progressbar"
            style="transform: scaleX({{ $value / 100 }}); transform-origin: left center;">
        </div>
    </div>
    <?php if ((is_string($minLabel) && $minLabel !== '') || (is_string($maxLabel) && $maxLabel !== '')): ?>
    <div class="flex items-center justify-between text-xs text-muted-text">
        <p>{{ $minLabel }}</p>
        <p>{{ $maxLabel }}</p>
    </div>
    <?php endif; ?>
</div>

