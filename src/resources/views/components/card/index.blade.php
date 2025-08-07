@php
    $base = 'w-full rounded-lg bg-surface text-surface-text shadow-sm';
@endphp

<div {{ $attributes->merge(['class' => "$base"]) }}>
    <div class="p-6 {{isset($header) ? '' :'pb-0'}}">
        @isset($header)
            {{ $header }}
        @endisset
    </div>

    <div>
        @isset($content)
            {{ $content }}
        @endisset
    </div>
    
    <div>
        @isset($footer)
            {{ $footer }}
        @endisset
    </div>
</div>
