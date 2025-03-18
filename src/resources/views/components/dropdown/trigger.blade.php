<x-slot name="trigger">
    <div {{ $attributes->merge([
        'x-on:click' => 'dropdownOpen = !dropdownOpen;',
    ]) }} x-ref="trigger">
        {{ $slot }}
    </div>
</x-slot>
