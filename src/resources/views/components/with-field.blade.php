@php
    $attributes->onlyProps(['label', 'description', 'name']);
@endphp

@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'descriptionTrailing' => null,
    'description' => null,
    'label' => null,
    'badge' => null,
])

<?php if ($label || $description): ?>
<mijnui:field>
    <?php if ($label): ?>
    <mijnui:label>{{ $label }}</mijnui:label>
    <?php endif; ?>

    <?php if ($description): ?>
    <mijnui:description>{{ $description }}</mijnui:description>
    <?php endif; ?>

    {{ $slot }}

    <mijnui:error :$name />

    <?php if ($descriptionTrailing): ?>
    <mijnui:description>{{ $descriptionTrailing }}</mijnui:description>
    <?php endif; ?>
</mijnui:field>
<?php else: ?> 
{{ $slot }}
<mijnui:error :$name />
<?php endif; ?>
