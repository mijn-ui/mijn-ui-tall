@php
    $base = 'w-full rounded-lg overflow-hidden bg-surface text-surface-text shadow-sm';
@endphp

<div {{ $attributes->merge(['class' => "$base"]) }}>
    <div class="p-6">
        @isset($header)
            {{ $header }}
        @endisset
    </div>

    <div>
        @isset($content)
            {{ $content }}
        @endisset
    </div>
    
    <div class="p-6 pt-0">
        @isset($footer)
            {{ $footer }}
        @endisset
    </div>
</div>
