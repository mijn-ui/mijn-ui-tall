@props(['name'])

<div x-data="{ open: false }">
    <button @click="open = true" {{ $attributes }}>
        {{ $slot }}
    </button>

    @push('modals')
        <div x-show="open" @click.away="open = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg {{ $attributes->get('class') }}">
                {{ ${'modal_' . $name} }}
            </div>
        </div>
    @endpush
</div>
