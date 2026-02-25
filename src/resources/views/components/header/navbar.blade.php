@props([
    'class' => '',
])
@php
    $base = 'flex items-center gap-2';
@endphp

<nav x-data class="flex w-full items-center justify-between px-3 py-2">
    <div class="flex items-center gap-4">
        <button @click="$store.sidebar.toggle()"
            class="inline-flex h-8 w-8 items-center justify-center gap-1 rounded-md border border-main-border bg-transparent text-sm text-foreground transition-colors duration-200 ease-in-out hover:bg-accent hover:text-accent-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90 disabled:pointer-events-none disabled:opacity-50 sm:h-10 sm:w-10">
             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12C2 8.31087 2 6.4663 2.81382 5.15877C3.1149 4.67502 3.48891 4.25427 3.91891 3.91554C5.08116 3 6.72077 3 10 3H14C17.2792 3 18.9188 3 20.0811 3.91554C20.5111 4.25427 20.8851 4.67502 21.1862 5.15877C22 6.4663 22 8.31087 22 12C22 15.6891 22 17.5337 21.1862 18.8412C20.8851 19.325 20.5111 19.7457 20.0811 20.0845C18.9188 21 17.2792 21 14 21H10C6.72077 21 5.08116 21 3.91891 20.0845C3.48891 19.7457 3.1149 19.325 2.81382 18.8412C2 17.5337 2 15.6891 2 12Z" />
                <path d="M9.5 3L9.5 21" />
                <path d="M5 7H6M5 10H6" />
            </svg>
        </button>

    </div>
 
    <div {{ $attributes->merge(['class' => "$base $class"]) }}>
       {{$slot}}
    </div>
</nav>
