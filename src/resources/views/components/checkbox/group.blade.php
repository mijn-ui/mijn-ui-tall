@props([
    'noSelect' => false,
    'direction' => 'horizontal',
    'label' => 'Select All',
])

@php
    $base = 'flex gap-2';
    $directionClass =
        [
            'horizontal' => 'flex-row items-center',
            'vertical' => 'flex-col items-start',
        ][$direction] ?? 'flex-row';

@endphp

<div data-mijn-select-all {{ $attributes->merge(['class' => "$base $directionClass"]) }}>
    <?php if(!$noSelect): ?>
    <mijnui:checkbox class="py-3" :$label id="trigger"
        x-on:click="Array.from($el.closest('[data-mijn-select-all]').querySelectorAll('input[type=checkbox]')).filter(i => i.id !== 'trigger').forEach(i => i.checked = $event.target.checked)" />
        <?php endif ?>
        {{ $slot }}
</div>
