@props([
    'name' => null,
    'message' => null,
])

@php
    $message ??= $name ? $errors->first($name) : null;

    $classes = "text-xs text-danger " . ($message ? '' : 'hidden');

@endphp

<div role="alert" aria-live="polite" aria-atomic="true" {{ $attributes->class($classes) }}>
    @if($message)
    {{ $message }}
    @endif
</div>
