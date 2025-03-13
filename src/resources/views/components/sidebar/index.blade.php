<aside
    x-data
    :class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 hidden w-52 space-y-2 overflow-y-auto border-r border-main-border bg-surface px-3 pb-4 pt-2 shadow-sm ease-out transition-transform duration-200 sm:block">

    {{ $slot }}
</aside>
