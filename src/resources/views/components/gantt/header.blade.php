<!-- Gantt Chart Header -->
<x-slot name="header" >
    <div class="flex h-14 w-full items-center justify-between gap-2 border-b border-main-border bg-accent px-3 py-2 font-semibold">
        <p class="text-sm text-main-text">
            {{ $slot }}
        </p>
    </div>
    
</x-slot>