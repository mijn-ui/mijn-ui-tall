@props([
    'href' => '/',
    'logo' => null,
    'name' => null,
])

@php
    $classes = "h-10 flex items-center mr-4";
    $textClasses = "text-sm font-semibold text-main-text sm:text-base";

    $hasName = !empty($name);
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->class([$classes, 'gap-2' => $hasName])->except('alt') }}
    data-flux-brand
>
    <div class="{{ $hasName ? 'size-6' : 'size-8' }} rounded-sm overflow-hidden shrink-0">
        @if(is_string($logo))
            <img src="{{ $logo }}" {{ $attributes->only('alt') }} />
        @else
            {{ $logo ?? $slot }}
        @endif
    </div>

    @if($hasName)
        <div class="{{ $textClasses }}">{{ $name }}</div>
    @endif
</a>
