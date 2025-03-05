@props([
    'href' => null,
    'isLast' => false
])
<!-- Breadcrumb Item -->
<li class="inline-flex items-center gap-1.5">
    @if ($href)
        <!-- Breadcrumb Link -->
        <a class="transition-colors hover:text-main-text hover:underline" href="{{ $href }}">{{ $slot }}</a>
    @else
        <span>{{ $slot }}</span>
    @endif
</li>
@if (!$isLast)
    <!-- Breadcrumb Item/Separator -->
    <li class="[&>svg]:w-3.5 [&>svg]:h-3.5">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="lucide lucide-chevron-right">
            <path d="m9 18 6-6-6-6"></path>
        </svg>
    </li>
@endif

