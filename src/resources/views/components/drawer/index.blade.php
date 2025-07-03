@props([
    'position' => 'left',
    'size' => 'md',
    'noBackdrop' => false,
    'persistent' => false,
    'class' => '',
])

@php
$isVertical = in_array($position, ['left', 'right']);
$isHorizontal = in_array($position, ['top', 'bottom']);

$positionClass = match ($position) {
    'left' => 'inset-y-0 left-0',
    'right' => 'inset-y-0 right-0',
    'top' => 'inset-x-0 top-0',
    'bottom' => 'inset-x-0 bottom-0',
    default => 'inset-y-0 left-0',
};

$sizeClass = match ($size) {
    'sm' => $isHorizontal ? 'h-48' : 'w-64',
    'md' => $isHorizontal ? 'h-72' : 'w-80',
    'lg' => $isHorizontal ? 'h-[32rem]' : 'w-[32rem]',
    'full' => $isHorizontal ? 'h-screen' : 'w-screen',
    default => $isHorizontal ? 'h-72' : 'w-80',
};
@endphp

<div
    x-data="{open: false}"
    x-init="init"
    @keydown.escape.window="if(!{{$persistent}}) open = false"
>
    @isset($trigger)
        {{ $trigger }}
    @endisset

    @unless($noBackdrop)
        <div class="fixed inset-0 z-40 bg-black/50" x-show="open" x-transition.opacity @click="if(!{{$persistent}}) open = false"></div>
    @endunless

    <div
        x-show="open"
        x-transition
        @click.away="if(!{{$persistent}}) open = false"
        class="fixed z-50 bg-white shadow-xl flex flex-col {{ $positionClass }} {{ $sizeClass }} {{ $class }}"
    >

         @isset($header)
            {{ $header }}  
        @endisset

        @isset($content)
            {{ $content }}  
        @endisset

        @isset($footer)
            {{ $footer }}  
        @endisset
    </div>
</div>
