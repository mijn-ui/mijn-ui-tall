@props(['variant' => 'single'])

<?php if ($variant === 'single'): ?>
<aside
    x-data
    :class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 hidden w-52 space-y-2 overflow-y-auto border-r border-main-border bg-surface px-3 pb-4 pt-2 shadow-sm ease-out transition-transform duration-200 sm:block">

    {{ $slot }}
</aside>
<?php elseif ($variant === 'double'): ?>
<aside
    x-data
    class="fixed inset-y-0 left-0 z-50 flex shadow-sm ease-out">
   {{ $slot }}
</aside>
<?php endif; ?>
