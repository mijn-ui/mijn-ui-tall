@props([
    'value' => '',
    'disabled' => false,
])

@aware(['multiple'])

<button
    type="button"
    @click="handleSelect('{{ $slot }}', '{{ $value }}')"
x-show="!search || '{{ strtolower($slot) }}'.includes(search.toLowerCase())"
    x-init="chosenText['{{ $value }}'] = '{{ $slot }}';"
    :class="{
        'inline-flex w-full cursor-pointer items-center justify-between gap-2 rounded-md px-4 py-2 text-left text-sm': true,
        'bg-primary/10 text-black': multiple ? selectedValue.includes('{{ $value }}') : selectedValue === '{{ $value }}',
        'hover:bg-primary/20 hover:text-primary': !(multiple ? selectedValue.includes('{{ $value }}') : selectedValue === '{{ $value }}'),
        'opacity-50 pointer-events-none bg-surface': {{ $disabled ? 'true' : 'false' }}
    }"
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
    <template x-if="multiple ? selectedValue.includes('{{ $value }}') : selectedValue === '{{ $value }}'">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
            stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </template>
</button>
