@php
    $base = 'overflow-hidden transition-all text-secondary-foreground ';
@endphp

<x-slot:content>
    <div x-cloak {{ $attributes->merge(['class' => $base]) }} x-bind:style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
        <div class="pb-4 ">{{ $slot }}</div>
    </div>
</x-slot:content>
