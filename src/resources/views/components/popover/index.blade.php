@props([
    'align' => 'center',
])

@php
    $contentBase = 'absolute bottom-full my-0.5 w-full text-sm';
   
@endphp


<div class="relative max-w-lg" x-data="{ open: false }" x-on:click.away="open = false">
    <div x-on:click="open= !open">
        {{ $trigger }}
    </div>
    <div x-show="open" x-transition class="{{ $contentBase }}">
        {{ $content }}
    </div>
</div>
