@props([
    'noSelect' => false,
    'direction' => 'horizontal',
    'label' => 'Select All',
])

@php
    $directionClass = match ($direction) {
        'vertical' => 'flex-col items-start',
        default => 'flex-row items-center',
    };
    $triggerId = 'trigger-' . uniqid();
@endphp

<div 
    x-data="{ 
        allChecked: false,
        isTogglingAll: false,
        update() {
            if (this.isTogglingAll) return;
            // Use data-attribute instead of ID to avoid mismatch after Livewire morph/re-render
            const children = Array.from($el.querySelectorAll('input[type=checkbox]'))
                .filter(i => !i.hasAttribute('data-mijn-trigger'));
            
            this.allChecked = children.length > 0 && children.every(i => i.checked);
        }
    }"
    x-init="
        update();
        {{-- Re-sync when Livewire morphs or replaces children --}}
        const observer = new MutationObserver(() => update());
        observer.observe($el, { childList: true, subtree: true });
    "
    x-on:change="update()"
    data-mijn-select-all 
    {{ $attributes->class(['flex gap-2', $directionClass]) }}
>
    @unless ($noSelect)
        <mijnui:checkbox id="{{ $triggerId }}" data-mijn-trigger class="py-3" :label="$label"
            x-model="allChecked"
            x-on:click="
                this.isTogglingAll = true;
                {{-- Explicitly calculate target state based on current allChecked value --}}
                const targetState = !allChecked;
                
                const children = Array.from($el.closest('[data-mijn-select-all]').querySelectorAll('input[type=checkbox]'))
                    .filter(i => !i.hasAttribute('data-mijn-trigger'));
                
                children.forEach(i => {
                    if (i.checked !== targetState) {
                        i.click();
                    }
                });
                
                this.allChecked = targetState;
                this.isTogglingAll = false;
            " />
    @endunless

    {{ $slot }}
</div>
