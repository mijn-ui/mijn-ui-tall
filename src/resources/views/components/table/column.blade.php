@props([
    'name' => null,
    'label' => null,
    'visible' => true,
])
@php
    $hasLabel = !empty($label);
    $columnLabel = $label ?? trim(strip_tags($slot));
@endphp

<th scope="col" 
    {{ $attributes->class([
        'px-4 py-3.5 text-left align-middle font-semibold text-muted-foreground first:pl-6 last:pr-6',
    ])->merge([
        'data-table-column' => '',
        'data-name' => $name,
        'data-label' => $columnLabel,
        'data-visible' => $visible ? 'true' : 'false',
    ]) }}
    @if($name) 
        x-init="registerColumn(@js($name), @js($columnLabel), {{ $visible ? 'true' : 'false' }})"
        x-show="!hasViewable || visibleColumns.includes(@js($name))" 
    @endif
>
    {{ $slot }}
</th>