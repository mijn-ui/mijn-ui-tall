@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'placeholder' => 'Select an option',
    'label' => null,
    'description' => null,
    'size' => 'sm',
    'variant' => 'primary',
])

@php
    $base = 'relative px-4 py-2 bg-white border rounded cursor-pointer select-none ';
    $variantClass = [
        'primary' => 'bg-primary',
    ][$variant];
    $sizeClass = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
    ][$size];

        $message ??= $name ? $errors->first($name) : null;

        $classes = "text-xs text-danger " . ($message ? '' : 'hidden');

@endphp

<mijnui:with-field :$label :$description :$name>
<div>
    <input
        class="hidden"
        x-ref="nativeSelect"
        type="text"
        {{ $attributes->except('class') }}
        @isset($name) name="{{ $name }}" @endisset
    />

    <!-- ComboBox -->
    <div
        class="flex flex-col justify-center gap-1 relative"
    x-data="{
    selectOpen: false,
    selectedItem: '{{ $placeholder }}',
    selectedValue: '',
    chosenText: {}
    }"
    x-init="
    $nextTick(() => {
    if ($refs.nativeSelect.value) {
    selectedItem = chosenText[$refs.nativeSelect.value] || '{{ $placeholder }}';
    selectedValue = $refs.nativeSelect.value;
    }
    })
    "
    >
    <!-- ComboBox Trigger -->
    <button
        x-on:click="selectOpen = !selectOpen"
        type="button"
        role="combobox"
        class="flex h-10 items-center justify-between rounded-md border bg-surface px-3 py-2 text-sm placeholder:text-muted-text focus:border-ring focus:outline-none focus:ring-1 focus:ring-ring"
    >
        <span class="line-clamp-1 text-muted" x-text="selectedItem">{{ $placeholder }}</span>
        <svg
            stroke="currentColor"
            fill="none"
            viewBox="0 0 24 24"
            height="1em"
            width="1em"
            xmlns="http://www.w3.org/2000/svg"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="m7 15 5 5 5-5" />
            <path d="m7 9 5-5 5 5" />
        </svg>
    </button>

    <!-- ComboBox Content -->
    <div
        x-show="selectOpen"
        x-transition
        class="absolute w-full top-full left-0 rounded-lg border bg-surface p-1 text-sm text-main-text z-50 overflow-hidden mt-1"
    >

    {{ $slot }}


</div>


</div>


</div>

</mijnui:with-field>
