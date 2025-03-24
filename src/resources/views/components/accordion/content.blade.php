@php
    $base = 'text-sm overflow-hidden transition-all duration-300 origin-top';
@endphp


<div x-show="open" x-transition:enter-start="scale-y-0" x-transition:enter-end="scale-y-100"
    x-transition:leave-start="scale-y-100" x-transition:leave-end="scale-y-0"
    {{ $attributes->merge([
        'class' => $base,
    ]) }}>
    <div class="pb-3 pt-0 text-muted-text">{{ $slot }}</div>
</div>
