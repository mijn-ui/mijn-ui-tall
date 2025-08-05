@props(['variant' => 'single'])

<x-slot:sidebar>
    <div class="sticky top-0 left-0 h-screen">

        <?php if ($variant === 'single'): ?>

        <aside x-data x-bind:class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full'"
            class="hidden z-50 h-full w-52 space-y-2 overflow-y-auto border-r border-main-border bg-surface px-3 pb-4 pt-2 shadow-sm ease-out transition-transform duration-200 sm:block">
            @isset($logo)
                {{ $logo }}
            @endisset
            {{ $slot }}
        </aside>
        <?php elseif ($variant === 'double'): ?>
        <aside x-data class="flex h-full z-50 shadow-sm ease-out">
            {{ $slot }}
        </aside>
        <?php endif; ?>
    </div>

</x-slot:sidebar>
