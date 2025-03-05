<nav x-data class="flex w-full items-center justify-between px-3 py-2">
    <!-- Navbar Left -->
    <div class="flex items-center gap-4">
        <!-- Sidebar Trigger -->
        <button
            @click="$store.sidebar.toggle()"
            class="text-default-text inline-flex h-8 w-8 items-center justify-center gap-1 rounded-md border border-main-border bg-transparent text-sm transition-colors duration-200 ease-in-out hover:bg-accent hover:text-accent-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90 disabled:pointer-events-none disabled:opacity-50 sm:h-10 sm:w-10">
            <svg
                stroke="currentColor"
                fill="none"
                stroke-width="2"
                viewBox="0 0 24 24"
                stroke-linecap="round"
                stroke-linejoin="round"
                height="20"
                width="20"
                xmlns="http://www.w3.org/2000/svg">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                <path d="M9 3v18"></path>
                <path d="m14 9 3 3-3 3"></path>
            </svg>
        </button>

        <!-- Navbar Brand Title -->
        <h3 class="text-sm font-semibold text-main-text sm:text-base">MijnUI</h3>
    </div>

    <!-- Navbar Right -->
    <div class="flex items-center gap-2">
        {{ $slot }}
    </div>
</nav>
