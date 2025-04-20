@props([
    'noSelect' => false,
    'label' => 'Select All',
])

<div data-mijn-select-all {{ $attributes }}>
    <?php if(!$noSelect): ?>
        <mijnui:checkbox :$label id="trigger"
            x-on:click="Array.from(($el.closest('[data-mijn-select-all]')).querySelectorAll('input')).filter(i => i.id !== 'trigger').forEach(i => i.checked !== $el.checked && i.click());" />
    <?php endif;?>
    {{ $slot }}
</div>
