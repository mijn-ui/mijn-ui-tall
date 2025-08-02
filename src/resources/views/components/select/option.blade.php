@props([
    'value' => '',
    'selected' => false,
    'disabled' => false,
])

@aware(['native', 'multiple'])

@if ($native)
    <option value="{{ $value }}" {{ $selected ? 'selected' : '' }} {{ $disabled ? 'disabled' : '' }}>
        {{ $slot }}
    </option>
@else
    <button
        x-on:click="
        selectedItem.includes('{{$slot}}');
        {{ $multiple
            ? "selectedItem.includes('$slot') ? selectedItem = selectedItem.filter(i => i !== '$slot') : selectedItem.push('$slot')"
            : "selectedItem = '$slot'" }}

        selectedValue.includes('{{$value}}');
        {{ $multiple
            ? "selectedValue.includes('$value') ? selectedValue = selectedValue.filter(v => v !== '$value') : selectedValue.push('$value')"
            : "selectedValue = '$value'" }}
        
        {{ $multiple ? '' : 'selectOpen = false' }}

        value = selectedValue;
    "
        x-init="
            chosenText['{{ $value }}'] = '{{ $slot }}';
            if(selectedValue.includes('{{$value}}')){
                selectedItem.push('{{$slot}}')
            }
        "
        :class="{
            'inline-flex w-full cursor-pointer items-center justify-between gap-2 rounded-md bg-primary/20 px-4 py-2 text-left text-sm text-primary': (
                    value == '{{ $value }}' || selectedValue.includes('{{ $value }}')) &&
                !{{ $disabled ? 'true' : 'false' }},
            'w-full cursor-pointer rounded-md px-4 py-2 text-left text-sm hover:bg-accent hover:text-accent-text': value !=
                '{{ $value }}' &&
                !{{ $disabled ? 'true' : 'false' }},
            'pointer-events-none w-full cursor-inverse rounded-md bg-surface px-4 py-2 text-left text-sm opacity-50 hover:bg-accent hover:text-accent-text': {{ $disabled ? 'true' : 'false' }}
        }"
        type="button" {{ $disabled ? 'disabled' : '' }}>
        {{ $slot }}
        <template x-if="value == '{{ $value }}' || selectedValue.includes('{{ $value }}')">
            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </template>
    </button>
@endif
