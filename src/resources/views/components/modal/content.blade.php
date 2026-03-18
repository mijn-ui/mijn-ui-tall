@props([])

@push('modals')
    <x-slot name="content">
        <div x-bind:class="open ? 'pointer-events-auto opacity-100' : 'pointer-events-none opacity-0'"
            class="z-[9999] fixed inset-0 bg-black/50 flex items-center justify-center p-4" aria-hidden="true">

            <div x-show="open" x-transition @click.away="open = false" @keydown.escape.window="open = false"
                x-trap.noscroll="open" role="dialog" aria-modal="true" :aria-labelledby="modalId + '-title'"
                class="bg-background-alt text-foreground rounded-lg shadow-lg py-4 px-4 max-w-full sm:max-w-lg {{ $attributes->get('class') }}">
                <div class="relative">
                    <button type="button" x-on:click="open = false"
                        class="z-[9998] absolute top-0 right-0 text-lg text-muted-text hover:text-foreground"
                        aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </x-slot>
@endpush