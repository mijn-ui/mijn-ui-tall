@props([
    'textAlign' => 'left',
    'href' => null,
])

@php
    $base =
        'data-[disabled]:pointer-events-none data-[disabled]:opacity-50 relative flex h-9 cursor-pointer select-none items-center gap-2 px-2 text-sm outline-none hover:bg-secondary transition-colors [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0';

    $textAlignClass =
        [
            'left' => '',
            'center' => 'justify-center',
            'right' => 'justify-end',
        ][$textAlign] ?? '';

@endphp

@if ($href)
    <a href="{{ $href }}">
@endif
<div {{ $attributes->merge(['class' => " $base $textAlignClass"]) }}>
    {{ $slot }}
</div>
@if ($href)
    </a>
@endif
