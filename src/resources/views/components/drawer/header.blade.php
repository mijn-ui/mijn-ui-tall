<x-slot name="header">
    <div class="flex items-center justify-between p-4 border-b">
        {{ $slot }}
        <button x-on:click="open = false" class="text-gray-500 hover:text-black">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</x-slot>