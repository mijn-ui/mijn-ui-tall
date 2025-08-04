@props([
    'name' => null,
    'title' => null,
])

@php
    if (!$title) {
        $title = ucwords(str_replace('_', ' ', $name));
    }
@endphp

<mijnui:button :title="$title" variant="ghost" size="icon-sm"
    x-bind:class="{
        'bg-primary text-primary-foreground': $store.sidebar.activeContent === '{{ $name }}'
    }"
    x-on:click="$store.sidebar.setActiveContent('{{ $name }}')">
    {{ $slot }}
</mijnui:button>
