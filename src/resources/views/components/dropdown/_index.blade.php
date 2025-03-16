@props([
    'align' => 'center',
    'size' => 'sm',
    'variant' => 'primary',
])

@php
    $base = 'relative px-4 py-2 bg-white border rounded cursor-pointer select-none ';
    $variantClass = [
        'primary' => 'bg-primary',
    ][$variant];
    $sizeClass = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
    ][$size];

    $classes = $base . $variantClass . $sizeClass;

@endphp

<div x-data="{ dropdownOpen: false }"
x-init="window.addEventListener('resize', positionDropdown($refs.trigger, $refs.content, '{{ $align }}')));">
    @isset($trigger)
        <div>

            <div class="w-fit" x-ref="trigger"
                x-on:click="
                    dropdownOpen = !dropdownOpen
                    $nextTick(()=>positionDropdown($refs.trigger, $refs.content, '{{ $align }}'))
                    "
                >
                {{ $trigger }}
            </div>
        </div>
    @endisset

    @isset($content)
        <div class="fixed mt-1 bg-red-400 min-w-64" x-show="dropdownOpen" x-cloak x-transition x-ref="content">
            {{ $content }}
        </div>
    @endisset

</div>

<script>
    function positionDropdown(trigger, dropdown, position = "left") {
        console.log('running')
        let triggerRect = trigger.getBoundingClientRect();
        let dropdownWidth = dropdown.offsetWidth;

        let leftPosition = triggerRect.left + window.scrollX;
        let rightPosition = window.innerWidth - (triggerRect.right + window.scrollX);
        let centerPosition = triggerRect.left + (triggerRect.width / 2) - (dropdownWidth / 2) + window.scrollX;

        dropdown.style.top = `${triggerRect.bottom + window.scrollY}px`; // Position below the trigger

        if (position === "left") {
            dropdown.style.left = `${leftPosition}px`;
        } else if (position === "right") {
            dropdown.style.right = `${rightPosition}px`;
        } else if (position === "center") {
            dropdown.style.left = `${centerPosition}px`;
        }
    }
</script>
