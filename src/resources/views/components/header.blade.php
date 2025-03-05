@props([
    'sticky' => null,
    'container' => null,
])

@php
    $classes = 'fixed inset-x-0 top-0 z-10 flex items-center justify-center border-b bg-surface';

    if ($sticky) {
        $attributes = $attributes->merge([
            'x-data' => '',
            'x-bind:style' => '{ position: \'sticky\', top: $el.offsetTop + \'px\', \'max-height\': \'calc(100vh - \' + $el.offsetTop + \'px)\' }',
        ]);
    }
@endphp

<header {{ $attributes->class($classes) }} data-flux-header>
    @if ($container)
        <div class="mx-auto w-full h-full [:where(&)]:max-w-7xl px-6 lg:px-8 flex items-center">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</header>
