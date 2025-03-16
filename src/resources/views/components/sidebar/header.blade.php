@props([
    'class' => '',
    'variant' => 'double',

])
@php
    $base = 'flex items-center gap-1 text-main-text  font-extrabold';
@endphp

<?php if ($variant === 'single'): ?>
<div class="flex h-10 items-center gap-2 px-2">
    <h5 {{ $attributes->merge(['class' => "$base $class"]) }}>
        {{ $slot }}
    </h5>
</div>
<?php elseif ($variant === 'double'): ?>
<!-- Sidebar Icon Header -->
<div class="flex items-center justify-center py-4">
    <!-- Sidebar Logo -->
    <a href="#" class="inline-flex size-10 items-center justify-center gap-1 text-sm text-default-text">
       {{$slot}}
    </a>
</div>
<?php endif; ?>
