@props([
    'class' => '', // Allow custom classes to be passed
])

<p {{ $attributes->merge(['class' => "text-muted-foreground text-small $class"]) }}>
    {{ $slot }}
</p>
