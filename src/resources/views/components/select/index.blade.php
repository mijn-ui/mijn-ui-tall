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
    'searchable' => false,
    'clearable' => false,
    'options' => null,
    'key' => 'id',
    'value' => 'name'
])

@php
    $message ??= $name ? $errors->first($name) : null;
    $errorClass = 'text-xs text-danger ' . ($message ? '' : 'hidden');
    $wireModel = $attributes->wire('model');
    $wireModelName = $wireModel?->value();
    $rootModelAttributes = $attributes->only('x-model');
    $inputAttributes = $attributes->except(['class', 'x-model']);

    $triggerClasses =
        'border-border min-w-44 bg-background-alt placeholder:text-muted-foreground hover:bg-secondary flex h-10 w-full items-center justify-between rounded-md border px-3 py-2 text-sm font-medium transition-colors hover:border-border-primary-subtle [&>span]:line-clamp-1 gap-4 [&_svg]:size-4 [&_svg]:opacity-50 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background';

    $contentClasses =
        'absolute border-border bg-background-alt text-foreground z-50 max-h-96 min-w-32 overflow-hidden rounded-md border shadow-md duration-300 w-full mt-1';
@endphp

<mijnui:with-field :$label :$description :$name>
    @if ($native)
        <select x-data="{
            selectOpen: false,
            selectedItem: {{ $multiple ? '[]' : 'null' }},
            selectedValue: {{ $multiple ? '[]' : 'null' }},
            chosenText: {},
            search: ''
        }" @isset($name) name="{{ $name }}" @endisset {{ $multiple ? 'multiple' : '' }} {{ $attributes->merge(['class' => $triggerClasses]) }}>
            {{ $slot }}
        </select>
    @else
        <div
            x-data="selectComponent(
                @js($multiple),
                @js($placeholder),
                {{ $wireModelName ? "\$wire.entangle('{$wireModelName}')" : 'null' }},
                @js($attributes->get('value'))
            )"
            x-modelable="value"
            {{ $rootModelAttributes }}
        >
            <input type="hidden" {{ $inputAttributes }} @isset($name) name="{{ $name }}" @endisset x-bind:value="value" />

            <div class="relative w-full" x-on:click.outside="selectOpen = false">
                <!-- Trigger -->
                <button type="button" x-on:click="selectOpen = !selectOpen" role="combobox" x-ref="selectTrigger"
                    @disabled($disabled) class="{{ $triggerClasses }}">
                    <template x-if="multiple">
                        <div class="flex flex-wrap gap-1 items-center min-h-[1.25rem]">
                            <template x-for="item in selectedItem" :key="item">
                                <mijnui:badge x-text="item" backicon="fa-solid fa-xmark" @click.stop="removeSelected(item)" />
                            </template>
                            <template x-if="selectedItem.length === 0">
                                <span x-text="chosenText[value] ?? placeholder" class="text-muted-text line-clamp-1"></span>
                            </template>
                        </div>
                    </template>
                    <template x-if="!multiple">
                        <span x-text="chosenText[value] ?? placeholder" class="line-clamp-1"></span>
                    </template>
                    <svg stroke="currentColor" fill="none" viewBox="0 0 24 24" height="1em" width="1em"
                        xmlns="http://www.w3.org/2000/svg" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m7 15 5 5 5-5" />
                        <path d="m7 9 5-5 5 5" />
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-cloak x-show="selectOpen" x-ref="dropdownPanel" x-transition
                    class="{{ $contentClasses }}"
                    x-effect="adjustDropdown($refs.selectTrigger, $refs.dropdownPanel)">
                    @if ($searchable)
                        <div class="border-b border-border/10 p-2">
                            <input type="text" x-model="search" placeholder="Search..." 
                                class="w-full rounded-md border-0 bg-secondary px-3 py-1.5 text-sm ring-1 ring-border focus:ring-2 focus:ring-primary outline-none placeholder:text-muted-foreground transition-all" />
                            @if($clearable)
                                <button type="button" x-show="search" @click="search=''; $nextTick(() => $el.previousElementSibling.focus())" class="absolute right-4 top-4 text-muted-foreground hover:text-foreground">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endif

                    @if ($options)
                        @foreach ($options as $opt)
                            <mijnui:select.option 
                                disabled="{{ $opt['disabled'] ?? false }}"
                                value="{{ is_array($opt) ? ($opt[$key] ?? '') : $opt }}">
                                {{ is_array($opt) ? ($opt[$value] ?? '') : $opt }}
                            </mijnui:select.option>
                        @endforeach
                    @else
                        {{ $slot }}
                    @endif
                </div>
            </div>
        </div>
    @endif
</mijnui:with-field>

<script>
    function selectComponent(multiple, placeholder, entangledValue, initialValue = null) {
        return {
            selectOpen: false,
            multiple: multiple,
            placeholder: placeholder,
            selectedItem: multiple ? [] : null,
            selectedValue: multiple ? [] : null,
            chosenText: {},
            search: '',
            value: entangledValue ?? initialValue,
            init() {
                this.$nextTick(() => this.syncFromValue());
                this.$watch('value', () => this.syncFromValue());
            },
            syncFromValue() {
                if (this.multiple) {
                    const selectedValues = Array.isArray(this.value) ? [...this.value] : [];

                    this.selectedValue = selectedValues;
                    this.selectedItem = selectedValues.map((item) => this.chosenText[item] ?? item);

                    return;
                }

                this.selectedValue = this.value ?? null;
                this.selectedItem = this.selectedValue === null ? null : (this.chosenText[this.selectedValue] ?? this.selectedValue);
            },
            handleSelect(slot, value) {
                if (this.multiple) {
                    this.selectedItem = Array.isArray(this.selectedItem) ? this.selectedItem : [];
                    this.selectedValue = Array.isArray(this.selectedValue) ? this.selectedValue : [];
                    const index = this.selectedValue.indexOf(value);
                    if (index !== -1) {
                        this.selectedValue.splice(index, 1);
                        this.selectedItem.splice(index, 1);
                    } else {
                        this.selectedValue.push(value);
                        this.selectedItem.push(slot);
                    }
                } else {
                    this.selectedValue = value;
                    this.selectedItem = slot;
                    this.selectOpen = false;
                }
                this.value = this.multiple ? this.selectedValue : this.selectedValue;
                this.syncFromValue();
            },
            removeSelected(item) {
                const index = this.selectedItem.indexOf(item);
                if (index !== -1) {
                    this.selectedItem.splice(index, 1);
                    this.selectedValue.splice(index, 1);
                    this.value = this.multiple ? this.selectedValue : null;
                    this.syncFromValue();
                }
            }
        }
    }

    function adjustDropdown(trigger, panel) {
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
    }
</script>
