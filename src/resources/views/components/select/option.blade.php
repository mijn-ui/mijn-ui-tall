@props([
    'value' => '',
    'disabled' => false,
])

<button
    x-on:click="
        $refs.nativeSelect.value = '{{ $value }}';
        selectedItem = '{{ $slot }}';
        selectedValue = '{{ $value }}';
        $refs.nativeSelect.dispatchEvent(new Event('input'));
        selectOpen = false;
    "
    x-init="chosenText['{{ $value }}'] = '{{ $slot }}'"
    :class="{
        'inline-flex w-full cursor-pointer items-center justify-between gap-2 rounded-md bg-primary/20 px-4 py-2 text-left text-sm text-primary': selectedValue === '{{ $value }}' && !{{ $disabled ? 'true' : 'false' }},
        'w-full cursor-pointer rounded-md px-4 py-2 text-left text-sm hover:bg-accent hover:text-accent-text': selectedValue !== '{{ $value }}' && !{{ $disabled ? 'true' : 'false' }},
        'pointer-events-none w-full cursor-default rounded-md bg-surface px-4 py-2 text-left text-sm opacity-50 hover:bg-accent hover:text-accent-text': {{ $disabled ? 'true' : 'false' }}
    }"
    type="button"
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
    <template x-if="selectedValue === '{{ $value }}'">
        <svg
            stroke="currentColor"
            fill="none"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke-linecap="round"
            stroke-linejoin="round"
            height="1em"
            width="1em"
            xmlns="http://www.w3.org/2000/svg"
        >
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </template>
</button>
