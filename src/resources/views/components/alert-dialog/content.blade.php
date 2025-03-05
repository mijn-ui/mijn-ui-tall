@props(['id' => 'alert-dialog'])

<div
    x-show="open"
    @keydown.escape.window="open = false"
    @click.away="open = false"
    x-cloak
    role="dialog"
    aria-labelledby="{{ $id }}-title"
    aria-describedby="{{ $id }}-description"
    class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50"
    x-transition.opacity
>
    <div class="relative w-full max-w-lg rounded-xl border bg-white p-6 shadow-lg">
        <button
            @click="open = false"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
            aria-label="Close"
        >
            &times;
        </button>

        {{ $slot }}
    </div>
</div>
