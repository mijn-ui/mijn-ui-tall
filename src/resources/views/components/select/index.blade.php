@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'placeholder' => 'Select an option',
    'label' => null,
    'description' => null,
    'size' => 'default',
    'variant' => 'surface',
    'native' => false,
    'multiple' => false,
    'disabled' => false,
])

@php
    $message ??= $name ? $errors->first($name) : null;
    $errorClass = 'text-xs text-danger ' . ($message ? '' : 'hidden');

    $triggerClasses =
        'border-border min-w-44 bg-background-alt placeholder:text-muted-foreground hover:bg-secondary flex h-10 w-full items-center justify-between rounded-md border px-3 py-2 text-sm [&>span]:line-clamp-1 gap-4 [&_svg]:size-4 [&_svg]:opacity-50 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background';

    $contentClasses =
        'w-full absolute border-border bg-background-alt text-foreground z-50 max-h-96 min-w-32 overflow-hidden rounded-md border shadow-sm duration-300 w-60';
@endphp

<mijnui:with-field :$label :$description :$name>
    @if ($native)
        <select x-data="{
            selectOpen: false,
            selectedItem: '{{ $placeholder }}',
            selectedValue: '',
            chosenText: {}
        }" @isset($name) name="{{ $name }}" @endisset
            {{ $multiple ? 'multiple' : '' }} {{ $attributes->merge(['class' => $triggerClasses]) }}>
            {{ $slot }}
        </select>
    @else
        <div x-data="{
            value: @if ($attributes->wire('model')) @entangle($attributes->wire('model')) @else {{ $multiple ? '[]' : 'null' }} @endif,
            chosenText: {},
        }">
            <input type="hidden" {{ $attributes->except('class') }}
                @isset($name) name="{{ $name }}" @endisset x-bind:value="value" />

            <div x-data="{
                selectOpen: false,
                selectedItem: {{ $multiple ? '[]' : "chosenText[value] ?? '$placeholder'" }},
                selectedValue: {{ $multiple ? 'value ?? []' : "''" }},
            }" class="relative w-full" x-on:click.outside="selectOpen = false">
                <!-- Trigger -->
                <button x-on:click="selectOpen = !selectOpen" type="button" role="combobox" x-ref="selectTrigger"
                    @disabled($disabled) class="{{ $triggerClasses }}">
                    @if ($multiple)
                        <div class="flex flex-wrap gap-1 items-center min-h-[1.25rem]">
                            <template x-for="item in selectedItem" :key="item">
                                <mijnui:badge x-text="item" backicon="fa-solid fa-xmark"
                                    onClick="const index = selectedItem.findIndex(i => i === item); if (index !== -1) { selectedItem.splice(index, 1); selectedValue.splice(index, 1); }" />
                            </template>

                            <template x-if="!selectedItem.length">
                                <span x-text="chosenText[value] ?? '{{ $placeholder }}'"
                                    class="text-muted-text line-clamp-1"></span>
                            </template>
                        </div>
                    @else
                        <span x-text="chosenText[value] ?? '{{ $placeholder }}'" class="line-clamp-1"></span>
                    @endif

                    <svg stroke="currentColor" fill="none" viewBox="0 0 24 24" height="1em" width="1em"
                        xmlns="http://www.w3.org/2000/svg" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m7 15 5 5 5-5" />
                        <path d="m7 9 5-5 5 5" />
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-cloak x-show="selectOpen" x-ref="dropdownPanel"
                     x-transition
                    class="{{ $contentClasses }}"
                    x-effect="
                        if (selectOpen) {
                            $nextTick(() => {
                                const trigger = $refs.selectTrigger;
                                const panel = $refs.dropdownPanel;
                                const viewportHeight = window.innerHeight;
                                const triggerRect = trigger.getBoundingClientRect();
                                const panelHeight = panel.offsetHeight;

                                const spaceBelow = viewportHeight - triggerRect.bottom;
                                const spaceAbove = triggerRect.top;

                                if (spaceBelow < panelHeight && spaceAbove > panelHeight) {
                                       panel.style.bottom = 'calc(100% + 4px)';
                                       panel.style.top = '';
                                       } else {
                                        panel.style.top = 'calc(100% + 4px)';
                                        panel.style.bottom = '';
                                }
                            });
                        }
                    ">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @endif
</mijnui:with-field>
