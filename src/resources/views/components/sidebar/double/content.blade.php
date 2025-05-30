@props([
    'title' => '',
    'mijnuiSidebarChild' => ''
])
<div
x-cloak
    <?php if($mijnuiSidebarChild): ?>
        x-show="$store.sidebar.isOpen && $store.sidebar.activeContent === '{{$mijnuiSidebarChild}}'"
    <?php endif; ?>
    :class="$store.sidebar.isOpen ? 'w-56' : 'w-0 overflow-hidden'"
    class="h-full bg-surface transition-[width] duration-300 ease-in-out">
    <?php if (!empty($title)): ?>
    <div class="flex items-center gap-1 px-4 pt-6">
        <h5 class="text-xs font-semibold uppercase text-muted-text">{{$title}}</h5>
    </div>
    <?php endif; ?>
    {{$slot}}
</div>
