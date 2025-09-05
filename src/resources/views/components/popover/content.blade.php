@props([
    'align' => 'center',
])

@php
    $base = 'absolute transition z-10 mt-0.5 overflow-hidden rounded-md border border-main-border bg-surface text-surface-text p-2 min-w-48';
@endphp

<x-slot:content>
    <div x-cloak {{ $attributes->merge(['class' => $base]) }}
        x-bind:class="{'pointer-events-none opacity-0': !open }"
        x-on:click="open = false"
        x-effect="
            $nextTick(() => {
                let trigger = $refs.trigger;
                let align = '{{ $align }}';
                if (trigger) {
                console.log(trigger.scrollWidth, $el.scrollWidth)
                    if (align == 'center') {
                        $el.style.left = ((trigger.scrollWidth - $el.scrollWidth) / 2) + 'px';
                    } else if (align == 'left') {
                        $el.style.left = '0px';
                    } else if (align == 'right') {
                        $el.style.right = '0px';
                    }
                }   
            })
        ">
        {{$slot}}
    </div>
</x-slot:content>
