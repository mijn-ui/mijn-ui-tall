@props(['variant' => 'single'])

<x-slot:sidebar>
    <div class="sticky top-0 left-0 h-screen z-100">


        @if($variant == "double")
            <!-- Overlay for mobile -->
            <div x-show="$store.sidebar.isOpen" x-on:click="$store.sidebar.toggle()"
                class="fixed inset-0 bg-black/50 z-40 sm:hidden" x-transition:enter="transition opacity-0 duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition opacity-100 duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <aside x-data
                class="flex h-full z-50 shadow-sm ease-out max-sm:fixed max-sm:inset-y-0 max-sm:left-0 max-sm:transition-transform max-sm:duration-300 max-sm:-translate-x-full"
                :class="$store.sidebar.isOpen ? 'max-sm:translate-x-0' : 'max-sm:-translate-x-full'">
                {{ $slot }}
            </aside>
        @elseif($variant == "single")
            <aside x-data x-bind:class="$store.sidebar.isOpen ? 'w-0' : 'w-56 px-2'"
                class="hidden z-50 h-full space-y-2 overflow-y-auto border-r  overflow-x-hidden border-main-border bg-surface pb-4 pt-2 shadow-sm transition-all duration-200 sm:block">
                @isset($logo)
                    {{ $logo }}
                @endisset
                <div class="w-52">
                    {{ $slot }}
                </div>
            </aside>
        @endif
    </div>

</x-slot:sidebar>