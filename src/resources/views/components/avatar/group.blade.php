@props([
    'remainChildren' => null,
])

@php
    $groupClasses = 'flex items-center justify-center -space-x-2';
    $remainChildrenClasses = 'text-muted-foreground !ml-1.5 flex items-center justify-center text-tiny';
@endphp

<div {{ $attributes->merge(['class' => $groupClasses]) }}>
    {{ $slot }}

    <?php if($remainChildren): ?>
        <div class="{{ $remainChildrenClasses }}">
            {{ $remainChildren }}
        </div>
    <?php endif;?>
</div>
