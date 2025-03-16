<x-slot name="content">
    <div x-show="dropdownOpen" x-cloak x-transition
        class="absolute z-10 mt-0.5 w-64 overflow-hidden rounded-md border border-main-border bg-surface p-1 text-surface-text"
        role="menu">

        @isset($header)
            {{ $header }}
        @endisset

        @isset($items)
            {{ $items }}
        @endisset
    </div>
</x-slot>
