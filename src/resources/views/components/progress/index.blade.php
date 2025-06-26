@props([
    'label' => null,
    'value' => 0,
    'minLabel' => null,
    'maxLabel' => null,
    'maxValue' => 100,
])

@php
    $progressWidth = $value . '%';
@endphp

<div class="space-y-1">
    <?php if($label && is_string($label)): ?>
    <div class="flex items-center justify-between text-sm font-medium text-main-text">
        <h5>{{ $label }}</h5>
        <p>{{ number_format(($value / $maxValue) * 100)  }}%</p>
    </div>
    <?php endif; ?>
    <div {{$attributes->merge(['class' =>"relative h-2 w-80 overflow-hidden rounded-full bg-gray-200"])}}>
        <div
            class="h-full bg-primary transition-all duration-300 ease-in-out"
            aria-valuemin="0"
            aria-valuemax="{{$maxValue}}"
            aria-valuenow="{{ $value }}"
            role="progressbar"
            style="transform: scaleX({{ $value / $maxValue }}); transform-origin: left center;">
        </div>
    </div>
    <?php if (($minLabel && is_string($minLabel)) || ($maxLabel && is_string($maxLabel))): ?>
    <div class="flex items-center justify-between text-xs text-muted-text">
        <p>{{ $minLabel }}</p>
        <p>{{ $maxLabel }}</p>
    </div>
    <?php endif; ?>
</div>

