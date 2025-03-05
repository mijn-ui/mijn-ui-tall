@props([
    'class' => '', // Allow custom classes to be passed
])

<h3 {{ $attributes->merge(['class' => "text-2xl font-semibold leading-none tracking-tight $class"]) }}>
    {{ $slot }}
</h3>
