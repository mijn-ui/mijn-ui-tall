@props([
    'placement' => 'bottom-start',
    'offset' => 4,
    'disabled' => false,
])

@php
    $contentClasses = 'absolute border-border bg-background-alt text-foreground z-50 min-w-[8rem] overflow-hidden rounded-md border shadow-md data-[side=bottom]:animate-in data-[side=bottom]:fade-in-0 data-[side=bottom]:slide-in-from-top-2 data-[side=top]:animate-in data-[side=top]:fade-in-0 data-[side=top]:slide-in-from-bottom-2';
@endphp

<div x-data="{ 
    open: false,
}" class="relative inline-block" x-on:click.outside="open = false">
    <!-- Trigger -->
    <div x-on:click="open = !open" x-ref="trigger" @disabled($disabled)>
        @isset($trigger)
            {{ $trigger }}
        @endisset
    </div>

    <!-- Content -->   
    <div x-cloak
        x-show="open"
        x-ref="content"
        x-transition
        class="{{ $contentClasses }}"
        x-effect="
    if (open) {
        $nextTick(() => {
            const trigger = $refs.trigger;
            const content = $refs.content;
            const viewportHeight = window.innerHeight;
            const triggerRect = trigger.getBoundingClientRect();
            const contentHeight = content.offsetHeight;

            const spaceBelow = viewportHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;

            if (spaceBelow < contentHeight && spaceAbove > contentHeight) {
                content.style.bottom = 'calc(100% + 4px)';
                content.style.top = '';
            } else {
                content.style.top = 'calc(100% + 4px)';
                content.style.bottom = '';
            }
        });
    }
"
>
        @isset($content)
            {{ $content }}
        @endisset
    </div>
</div>
