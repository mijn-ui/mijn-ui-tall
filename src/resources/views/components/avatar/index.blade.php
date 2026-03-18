@props([
    'size' => 'md',
    'radius' => 'full',
    'src' => null,
    'alt' => '',
    'fallback' => null,
])

@php
    $base = 'relative flex items-center justify-center shrink-0 overflow-hidden bg-muted';
    $imageClasses = 'h-full w-full object-cover';
    $fallbackClasses = 'bg-muted flex size-full items-center justify-center';

      $sizeClasses = [
          'xxl' => 'h-16 w-16 text-sm',
          'xl' => 'h-14 w-14 text-sm',
          'lg' => 'h-12 w-12 text-sm',
          'md' => 'h-10 w-10 text-sm',
          'sm' => 'h-8 w-8 text-xs',
          'xs' => 'h-6 w-6 text-xs',
      ][$size] ?? 'h-10 w-10 text-sm';

      $radiusClasses = [
          'none' => 'rounded-none',
          'sm' => 'rounded-sm',
          'md' => 'rounded-md',
          'lg' => 'rounded-lg',
          'full' => 'rounded-full',
      ][$radius] ?? 'rounded-full';

@endphp

<div {{ $attributes->merge(['class' => "$base $sizeClasses $radiusClasses"]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" class="{{ $imageClasses }}">
    @elseif($fallback)
        <span class="{{ $fallbackClasses }}">
            {{ $fallback }}
        </span>
    @endif
</div>
