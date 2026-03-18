@props([
    'page' => null,
    'current' => null,
])
@php

    $base = 'inline-flex px-0 gap-0 size-8 items-center justify-center rounded-md text-sm hover:bg-accent hover:text-accent-text/80';
    $base = $current == $page ? $base . ' border bg-surface' : $base;

    $c_perPage = request()->query('perPage');
    $query_perPage = $c_perPage ? "&perPage=$c_perPage" : '';
    
@endphp
<a href="?page={{$page . $query_perPage}}" wire:navigate aria-label="Go to page {{ $page }}" {{ $current == $page ? 'aria-current=page' : '' }}>
    <button x-init {{ $current == $page ? 'disabled' : '' }} {{ $attributes->merge(['class' => $base]) }}>
        {{ $page }}
    </button>
</a>
