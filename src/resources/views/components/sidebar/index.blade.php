@props(['variant' => 'single'])

<x-slot:sidebar>
    <div class="sticky top-0 left-0 h-screen z-[100]">


        @if($variant == "double")
            <aside x-data class="flex h-full z-50 shadow-sm ease-out">
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