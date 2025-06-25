@php
    $base = 'text-sm overflow-hidden transition-all';
@endphp

<x-slot:content>
    <div x-cloak {{ $attributes->merge(['class' => $base]) }} x-bind:style="open ? 'max-height: ' + $el.scrollHeight + 'px' : 'max-height: 0px'">
        <div class="pb-3 pt-0 text-muted-text">{{ $slot }}</div>
    </div>
</x-slot:content>
