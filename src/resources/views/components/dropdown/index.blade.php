@props([
    'placement' => 'bottom-start',
    'offset' => 4,
    'disabled' => false,
    'align' => 'left',
    'closeOnSelect' => true,
    'teleport' => false,
])

@php
    $contentClasses =
        'absolute border-border bg-background-alt text-foreground z-[100] min-w-[8rem] overflow-hidden rounded-md border shadow-md data-[side=bottom]:animate-in data-[side=bottom]:fade-in-0 data-[side=bottom]:slide-in-from-top-2 data-[side=top]:animate-in data-[side=top]:fade-in-0 data-[side=top]:slide-in-from-bottom-2';
@endphp

<div x-data="{
    open: false,
    closeOnSelect: {{ $closeOnSelect ? 'true' : 'false' }},
    teleport: {{ $teleport ? 'true' : 'false' }},
    align: '{{ $align }}',
    reposition() {
        if (!this.open || !this.teleport) return;

        $nextTick(() => {
            const trigger = this.$refs.trigger;
            const content = this.$refs.content;
            if (!trigger || !content) return;

            const viewportHeight = window.innerHeight;
            const viewportWidth = window.innerWidth;
            const triggerRect = trigger.getBoundingClientRect();
            const contentHeight = content.offsetHeight;
            const contentWidth = content.offsetWidth;

            const spaceBelow = viewportHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;

            let top, left;

            if (spaceBelow < contentHeight && spaceAbove > contentHeight) {
                top = triggerRect.top - contentHeight - 4;
            } else {
                top = triggerRect.bottom + 4;
            }

            if (this.align === 'right') {
                left = triggerRect.right - contentWidth;
            } else if (this.align === 'middle') {
                left = triggerRect.left + (triggerRect.width - contentWidth) / 2;
            } else {
                left = triggerRect.left;
            }

            // Boundary checks
            if (left < 4) left = 4;
            if (left + contentWidth > viewportWidth - 4) left = viewportWidth - contentWidth - 4;
            if (top < 4) top = 4;

            content.style.top = `${top}px`;
            content.style.left = `${left}px`;
            content.style.margin = 0;
        });
    }
}" class="relative inline-block" x-on:click.outside="open = false"
    @scroll.window.passive.capture="reposition()"
    @resize.window.passive="reposition()">
    <!-- Trigger -->
    <div x-on:click="open = !open" x-ref="trigger" @disabled($disabled)>
        @isset($trigger)
            {{ $trigger }}
        @endisset
    </div>

    <!-- Content -->
    @if ($teleport)
        <template x-teleport="body">
    @endif
    <div x-cloak x-show="open" x-ref="content" x-transition
        class="{{ $contentClasses }}" @click="if (closeOnSelect) open = false"
        :style="teleport ? 'position: fixed; width: auto;' : ''" 
        x-effect="if (open) reposition()"
    >
        @isset($content)
            {{ $content }}
        @endisset
    </div>
    @if ($teleport)
        </template>
    @endif
</div>
