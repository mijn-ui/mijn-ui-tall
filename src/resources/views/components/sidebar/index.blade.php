<aside
    x-data
    :class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-52 space-y-2 overflow-y-auto border-r border-main-border bg-surface px-3 pb-4 pt-2 shadow-sm ease-out transition-transform duration-200">
    <!-- Sidebar Header -->
    <div class="flex items-center gap-2">
        <!-- Sidebar Toggle Button -->
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
                <path d="m16 15-3-3 3-3"></path>
            </svg>
        </button>

        <!-- Sidebar Brand/Logo -->
        <h5 class="flex items-center gap-1 font-extrabold text-main-text">MijnUI</h5>
    </div>
    <!-- Sidebar Content -->
    {{ $slot }}
</aside>
