@props([
    'textAlign' => 'left',
    'href' => null,
])

@php
    $base =
        'data-[disabled]:pointer-events-none data-[disabled]:opacity-50 relative flex h-9 w-full cursor-pointer select-none items-center gap-2 px-2 text-sm outline-none hover:bg-secondary focus-visible:bg-secondary transition-colors [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0';

    $textAlignClass =
        [
            'left' => '',
            'center' => 'justify-center',
            'right' => 'justify-end',
        ][$textAlign] ?? '';

@endphp

@if ($href)
    <a href="{{ $href }}" role="menuitem" class="{{ $base }} {{ $textAlignClass }}" {{ $attributes->except('class') }}>
        {{ $slot }}
    </a>
@else
    <button type="button" role="menuitem" {{ $attributes->merge(['class' => " $base $textAlignClass"]) }}>
        {{ $slot }}
    </button>
@endif
