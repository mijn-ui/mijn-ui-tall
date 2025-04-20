@props([
    'icon' => null,
    'class' => '',
])
@php
    $base = $icon
        ? 'translate-y-[-3px] [&>i]:absolute [&>i]:left-4 [&>i]:top-3 [&>i]:text-lg'
        : 'translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:size-5';
@endphp

<span {{ $attributes->merge(['class' => "$base $class"]) }}>
    <?php if($icon)
        <i class="{{ $icon }}"></i>
    <?php else: ?> 
        {{ $slot }}
    ?>
</span>
