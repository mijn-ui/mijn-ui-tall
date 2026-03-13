@props([
    'title' => '',
    'value' => '',
])

@push('mijn_sidebar_content')
    <div class="w-[14rem]" x-cloak x-show="$store.sidebar.activeContent === '{{ $value }}'">
        @if (!empty($title))
            <div class="flex items-center gap-1 px-4 pt-6">
                <h5 class="text-xs font-semibold uppercase text-foreground">{{ $title }}</h5>
            </div>
        @endif
        {{ $slot }}
    </div>
@endpush
