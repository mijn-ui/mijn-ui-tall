@props([
    'noSelect' => false,
    'direction' => 'horizontal',
    'label' => 'Select All',
])

@php
    $directionClass = match ($direction) {
        'vertical' => 'flex-col items-start',
        default => 'flex-row items-center',
    };
@endphp

<div data-mijn-select-all {{ $attributes->class(['flex gap-2', $directionClass]) }}>
    @unless ($noSelect)
        <mijnui:checkbox id="trigger" class="py-3" :label="$label"
            x-on:click="
                Array.from(
                    $el.closest('[data-mijn-select-all]')
                         .querySelectorAll('input[type=checkbox]')
                )
                .filter(i => i.id !== 'trigger')
                .forEach(i => i.checked = $event.target.checked)
            " />
    @endunless

    {{ $slot }}
</div>
