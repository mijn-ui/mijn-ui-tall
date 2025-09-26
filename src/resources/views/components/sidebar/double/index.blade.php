<div class="flex relative">

    <div
        class=" flex flex-col justify-between items-center h-full w-16 space-y-2 overflow-y-auto border-r border-main-border bg-surface">
        <div>
            @isset($logo)
                {{ $logo }}
            @endisset
            <!-- Sidebar Icon Group -->
            <div class="flex flex-col items-center justify-center gap-2 py-4">
                {{ $slot }}
            </div>
        </div>

        @isset($footer)
            {{ $footer }}
        @endisset

        <!-- Sidebar Content Toggle Button -->
        <button @click="$store.sidebar.toggle()"
            class="absolute -right-4 bottom-32 inline-flex size-7 items-center justify-center gap-0 rounded-md bg-primary px-0 text-sm text-primary-foreground transition-colors duration-200 ease-in-out hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90 disabled:pointer-events-none disabled:opacity-50">
            <!-- Icon with dynamic rotation -->
            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                stroke-linejoin="round" :class="$store.sidebar.isOpen ? 'rotate-180' : 'rotate-0'"
                class="transition-transform duration-300" height="1em" width="1em"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12h14"></path>
                <path d="m12 5 7 7-7 7"></path>
            </svg>
            <span class="sr-only">Toggle Sidebar</span>
        </button>

    </div>
    <div x-bind:style="{ width: $store.sidebar.isOpen ? '14rem' : '0' }"
        class="h-full bg-surface overflow-hidden transition-all border-accent"
        x-bind:class="{ 'border-r': $store.sidebar.isOpen }">
        @stack('mijn_sidebar_content')
    </div>
</div>
