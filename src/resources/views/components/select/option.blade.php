@props([
    'value' => '',
    'disabled' => false,
])

@aware(['multiple'])

@php
    $optionValue = (string) $value;
    $optionLabel = trim((string) $slot);
    $searchLabel = \Illuminate\Support\Str::lower($optionLabel);
@endphp

<button
    type="button"
    @click="handleSelect(@js($optionLabel), @js($optionValue))"
    x-show="!search || @js($searchLabel)?.includes(search.toLowerCase())"
    x-init="chosenText[@js($optionValue)] = @js($optionLabel);"
    :class="{
        'inline-flex w-full cursor-pointer items-center justify-between gap-2 rounded-md px-4 py-2 text-left text-sm': true,
        'bg-primary/10 text-black': multiple ? selectedValue?.includes(@js($optionValue)) : selectedValue === @js($optionValue),
        'hover:bg-primary/20 hover:text-primary': !(multiple ? selectedValue?.includes(@js($optionValue)) : selectedValue === @js($optionValue)),
        'opacity-50 pointer-events-none bg-surface': {{ $disabled ? 'true' : 'false' }}
    }"
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
    <template x-if="multiple ? selectedValue.includes(@js($optionValue)) : selectedValue === @js($optionValue)">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
            stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </template>
</button>
