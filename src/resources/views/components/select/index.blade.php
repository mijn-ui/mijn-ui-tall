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
    $base = 'relative px-4 py-2 border rounded cursor-pointer select-none ';
    $variantClass = [
        'primary' => 'bg-primary',
        'surface' => 'bg-surface',
    ][$variant];
    $sizeClass = [
        'default' => 'w-full',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
    ][$size];

    $class = "$base $variantClass $sizeClass";

    $message ??= $name ? $errors->first($name) : null;

    $classes = 'text-xs text-danger ' . ($message ? '' : 'hidden');

@endphp

<mijnui:with-field :$label :$description :$name>
    @if ($native)
        <select x-data="{
            selectOpen: false,
            selectedItem: '{{ $placeholder }}',
            selectedValue: '',
            chosenText: {}
        }" @isset($name) name="{{ $name }}" @endisset
            {{ $multiple ? 'multiple' : '' }}
            {{ $attributes->merge([
                'class' => $class,
            ]) }}>
            {{ $slot }}
        </select>
    @else
        <div x-data="{
            value: @if ($attributes->wire('model')) @entangle($attributes->wire('model')) @else {{ $multiple ? '[]' : 'null' }} @endif,
            chosenText: {},
        }">
            <input type="hidden" {{ $attributes->except('class') }}
                @isset($name) name="{{ $name }}" @endisset x-bind:value="value" />

            <!-- ComboBox -->
            <div x-data="{
                selectOpen: false, // Define selectOpen here
                selectedItem: {{ $multiple ? '[]' : "chosenText[value] ?? '$placeholder'" }},
                selectedValue: {{ $multiple ? 'value ?? []' : "''" }},
            }" class="flex flex-col justify-center gap-1 relative"
                x-on:click.outside="selectOpen = false">
                <!-- ComboBox Trigger -->
                <button x-on:click.self="selectOpen = !selectOpen" type="button" role="combobox" x-ref="selectTrigger"
                    @disabled($disabled)
                    class="flex {{$multiple ? 'h-full' : 'h-10'}} bg-background-alt items-center justify-between rounded-md border px-3 py-2 text-sm placeholder:text-muted-text focus:border-ring focus:outline-none focus:ring-1 focus:ring-ring {{ $variantClass }} {{ $sizeClass }}">

                    @if ($multiple)
                        <span class="line-clamp-1 flex flex-wrap gap-1 items-center">
                            <template x-for="item in selectedItem">
                                <mijnui:badge x-text="item" backicon="fa-solid fa-xmark"
                                    onClick="
                                    const index = selectedItem.findIndex(i => i === item);
                                    if (index !== -1) {
                                        selectedItem.splice(index, 1);
                                        selectedValue.splice(index, 1);
                                    }
                                    " />
                            </template>
                        </span>
                    @else
                        <span class="line-clamp-1" x-text="chosenText[value] ?? '{{ $placeholder }}'"></span>
                    @endif


                    <svg stroke="currentColor" fill="none" viewBox="0 0 24 24" height="1em" width="1em"
                        xmlns="http://www.w3.org/2000/svg" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="m7 15 5 5 5-5" />
                        <path d="m7 9 5-5 5 5" />
                    </svg>
                </button>

                <!-- ComboBox Content -->
                <div x-cloak x-show="selectOpen"
                    x-effect="
                    if (selectOpen) {
                        $nextTick(() => {
                        if ($refs.selectTrigger.getBoundingClientRect().bottom + $el.scrollHeight > window.innerHeight && $refs.selectTrigger.getBoundingClientRect().top > window.innerHeight /2) {
                            if (!$el.classList.contains('bottom-full')) {
                                $el.classList.add('bottom-full');
                                $el.classList.remove('top-full');
                            }
                        } else {
                            if (!$el.classList.contains('top-full')) {
                                $el.classList.add('top-full');
                                $el.classList.remove('bottom-full');
                            }
                        }
                    });
                }"
                    x-on:scroll.window="
            $nextTick(() => {
                if ($refs.selectTrigger.getBoundingClientRect().bottom + $el.scrollHeight > window.innerHeight && $refs.selectTrigger.getBoundingClientRect().top > window.innerHeight /2) {
                    if (!$el.classList.contains('bottom-full')) {
                        $el.classList.add('bottom-full');
                        $el.classList.remove('top-full');
                    }
                } else {
                    if (!$el.classList.contains('top-full')) {
                        $el.classList.add('top-full');
                        $el.classList.remove('bottom-full');
                    }
                }
            })
        "
                    x-transition
                    class="bg-background-alt absolute w-full left-0 space-y-px rounded-lg border bg-surface p-1 text-sm text-main-text z-50 overflow-hidden max-h-[50vh] overflow-y-auto ">

                    {{ $slot }}
                </div>
            </div>
        </div>
    @endif
</mijnui:with-field>
