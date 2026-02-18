@props([
    'value' => '',
    'disabled' => false,
])

@aware(['multiple'])

<button
    type="button"
    @click="handleSelect('{{ $slot }}', '{{ $value }}')"
    x-show="!search || '{{ strtolower($slot) }}'?.includes(search.toLowerCase())"
    x-init="chosenText['{{ $value }}'] = '{{ $slot }}';"
    :class="{
        'relative flex w-full cursor-pointer select-none items-center justify-between rounded-sm px-2 py-1.5 text-sm outline-none transition-colors': true,
        'bg-primary/10 text-primary font-medium': multiple ? selectedValue?.includes('{{ $value }}') : selectedValue === '{{ $value }}',
        'hover:bg-secondary text-foreground': !(multiple ? selectedValue?.includes('{{ $value }}') : selectedValue === '{{ $value }}'),
        'opacity-50 pointer-events-none': {{ $disabled ? 'true' : 'false' }}
    }"
    {{ $disabled ? 'disabled' : '' }}
>
    <span class="truncate">{{ $slot }}</span>
    <template x-if="multiple ? selectedValue.includes('{{ $value }}') : selectedValue === '{{ $value }}'">
        <svg stroke="currentColor" fill="none" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"
            stroke-linejoin="round" class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </template>
</button>
