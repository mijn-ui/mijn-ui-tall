@props([
    'size' => 'md',
    'persistent' => false,
    'name' => null,
    'open' => false,
])

@php
    $persistentJs = $persistent ? 'true' : 'false';
    $openJs = $open ? 'true' : 'false';
    $nameJs = $name ? "'$name'" : 'null';
@endphp

<span x-data="{
    open: {{ $openJs }},
    persistent: {{ $persistentJs }},
    modalName: {{ $nameJs }},
    modalId: 'modal-' + Math.random().toString(36).substr(2, 9)
}"
@if($name)
    x-on:modal-open.window="if ($event.detail === modalName) open = true"
    x-on:modal-close.window="if ($event.detail === modalName) open = false"
    x-on:modal-toggle.window="if ($event.detail === modalName) open = !open"
@endif
{{ $attributes }}>

    @isset($trigger)
        {{ $trigger }}
    @endisset

    @isset($content)
        {{ $content }}
    @endisset

</span>
