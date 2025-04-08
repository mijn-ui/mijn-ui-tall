@props(['name' => ''])

@push('modals')
    <x-slot name="content">
        <div x-show="open" x-transition
            class="z-[9999] fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">

            <div @click.away="open = false" class="bg-white rounded-lg shadow-lg py-4 px-4 {{ $attributes->get('class') }}">
                <div class="relative">
                    <button type="button" x-on:click="open = false" class="z-[9998] absolute top-0 right-0 text-lg text-gray-500 hover:text-gray-700"
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
