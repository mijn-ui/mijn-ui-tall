@props([
    'noSelect' => false,
    'label' => 'Select All',
])

<div data-mijn-select-all {{ $attributes }}>
    @if (!$noSelect)
        <mijnui:checkbox :$label id="trigger"
            x-on:click="Array.from(($el.closest('[data-mijn-select-all]')).querySelectorAll('input')).filter(i => i.id !== 'trigger').forEach(i => i.checked !== $el.checked && i.click());" />
    @endif
    {{ $slot }}
</div>
