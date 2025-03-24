@props([
   'class' => '',
    'size' => 'md',
    'radius' => 'full',
    'src' => null,
    'alt' => '',
    'fallback' => null,
])

@php
    $base = 'relative flex items-center justify-center shrink-0 overflow-hidden bg-muted rounded-full';
    $imageClasses = 'h-full w-full object-cover';
    $fallbackClasses = 'bg-muted flex size-full items-center justify-center';


      $sizeClasses = [
          'xxl' => 'h-16 w-16 text-sm',
          'xl' => 'h-14 w-14 text-sm',
          'lg' => 'h-12 w-12 text-sm',
          'md' => 'h-10 w-10 text-sm',
          'sm' => 'h-8 w-8 text-xs',
          'xs' => 'h-6 w-6 text-xm',
      ][$size];

      $radiusClasses = [
          'none' => 'rounded-none',
          'full' => 'rounded-full',
      ][$radius];

@endphp

<div {{ $attributes->merge(['class' => "$base $sizeClasses $radiusClasses $class"]) }}>
    <?php ($src)
        <img src="{{ $src }}" alt="{{ $alt }}" class="{{ $imageClasses }}">
    <?php else: ?> if ($fallback)
        <span class="{{ $fallbackClasses }}">
            {{ $fallback }}
        </span>
    ?>
</div>
