@props([
    'align' => 'center',
])

<x-slot name="content">
    <div x-cloak
        :class="dropdownOpen ? '': 'pointer-events-none opacity-0'"
        class="absolute transition z-10 mt-0.5 w-64 overflow-hidden rounded-md border border-main-border bg-surface text-surface-text"
        x-ref="dropdown"
        x-on:click="dropdownOpen = false"
        x-effect="
            $nextTick(() => {
                let el = $refs.dropdown;
                let trigger = $refs.trigger;
                if (el && trigger) {
                    if ('{{ $align }}' === 'center') {
                        el.style.left = ((trigger.scrollWidth - el.scrollWidth) / 2) + 'px';
                    } else if ('{{ $align }}' === 'left') {
                        el.style.left = '0px';
                    } else if ('{{ $align }}' === 'right') {
                        el.style.right = '0px';
                    }
                }
            })
        "
        role="menu">
        
        @isset($header)
            {{ $header }}
        @endisset

        @isset($body)
            {{ $body }}
        @endisset
    </div>
</x-slot>
