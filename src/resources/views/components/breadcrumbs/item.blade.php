@props([
    'href' => null,
    'isLast' => false,
])

@php
    $base = 'inline-flex items-center gap-4 text-sm transition duration-300 hover:text-secondary-foreground';
    if (!$isLast) {
        $base .= ' text-muted-foreground';
    }
@endphp

<!-- Breadcrumb Item -->
<li {{ $attributes->merge(['class' => $base]) }}>
    @if ($href)
        <!-- Breadcrumb Link -->
        <a href="{{ $href }}" class="transition-colors hover:text-main-text hover:underline">
            {{ $slot }}
        </a>
    @else
        <span>{{ $slot }}</span>
    @endif
</li>

@if (!$isLast)
    <!-- Breadcrumb Separator -->
    <li class="[&>svg]:w-3.5 [&>svg]:h-3.5">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
            <path d="m9 18 6-6-6-6" />
        </svg>
    </li>
@endif
