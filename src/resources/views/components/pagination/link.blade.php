@props([
    'page' => null,
    'current' => null,
])
@php

$base = 'inline-flex px-0 gap-0 size-8 items-center justify-center rounded-md text-sm hover:bg-accent hover:text-accent-text/80';

$base = $current == $page ? $base . ' border bg-surface' : $base;

    @endphp
<button x-init {{ $current == $page ? 'disabled' : '' }} x-on:click="changePage({{ $page }})"
    {{ $attributes->merge([
        'class' => $base,
    ]) }}>
    {{ $page }}
</button>

