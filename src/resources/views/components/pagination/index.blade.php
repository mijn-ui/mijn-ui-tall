@props([
    'data' => [],
    'hasLinks' => true,
    'hasGuide' => true,
    'hasPerPage' => true,
    'perPage' => null,
    'perPageOptions' => ['10', '25', '50', '100', '200'],
])

@php

if (!method_exists($data, 'firstItem')) {
    throw new \Exception('data for pagination must be type of Illuminate\Pagination\LengthAwarePaginator');
}

$c_page = request()->query('page') ?? 1;
$c_perPage = request()->query('perPage') ?? $perPage ?? $perPageOptions[0];
@endphp

<nav 
    {{ $attributes->merge([
        'class' => 'flex flex-col items-center justify-between gap-2 py-2 sm:flex-row',
    ]) }}
    x-data="{
        currentPage: {{ $c_page }},
        perPage: {{ $c_perPage }},
        perPageOpen: false,
        goToPage(page) {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            params.set('page', page);
            params.set('perPage', this.perPage);

            window.location.href = `${url.pathname}?${params.toString()}`;
        },
        updatePerPage(value) {
            this.perPage = value;
            this.currentPage = 1;
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            params.set('perPage', this.perPage);
            params.set('page', this.currentPage);

            window.location.href = `${url.pathname}?${params.toString()}`;
        }
    }"
>
    {{-- Pagination Guide --}}
    @if ($hasGuide)
        <p class="text-sm text-muted-foreground">
            Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results
        </p>
    @endif

    {{-- Pagination Links --}}
    @if ($hasLinks && $data->hasPages())

        <div class="flex items-center gap-2">
            {{-- Previous --}}
            <mijnui:pagination.previous current="{{$c_page}}" @click="goToPage(currentPage - 1)"/>
            
            {{-- First Page --}}
            <span v-if="currentPage == 1">
                <mijnui:pagination.link current="{{$c_page}}" page="1"/>
            </span>

            {{-- Ellipsis Left --}}
            @if($data->currentPage() > 3)
                <span class="px-2">…</span>
            @endif

            {{-- Middle Pages --}}
            @for ($i = max(2, $data->currentPage() - 1); $i <= min($data->lastPage() - 1, $data->currentPage() + 1); $i++)
                <mijnui:pagination.link current="{{$c_page}}" page="{{ $i }}" />
            @endfor

            {{-- Ellipsis Right --}}
            @if ($data->currentPage() < $data->lastPage() - 2)
                <span class="px-2">…</span>
            @endif

            <mijnui:pagination.link current="{{$c_page}}" page="{{ $data->lastPage() }}" />

            <mijnui:pagination.next current="{{$c_page}}" lastPage="{{ $data->lastPage() }}" @click="goToPage(currentPage + 1)" />

        </div>

    @endif

    {{-- Per Page Selector --}}
    @if ($hasPerPage)
    <div class="relative w-60">
        <!-- Trigger -->
        <button
            type="button"
            @click="perPageOpen = !perPageOpen"
            class="border-border min-w-44 bg-background-alt placeholder:text-muted-foreground hover:bg-secondary flex h-10 w-full items-center justify-between rounded-md border px-3 py-2 text-sm gap-4 [&_svg]:size-4 [&_svg]:opacity-50 disabled:pointer-events-none disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background"
        >
            <span x-text="perPage"></span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    
        <!-- Options -->
        <div
            x-show="perPageOpen"
            @click.outside="perPageOpen = false"
            class="w-full absolute border-border bg-background-alt text-foreground z-50 max-h-96 min-w-32 overflow-auto rounded-md border shadow-sm mt-1"
        >
            @foreach ($perPageOptions as $perPageOption)
                <div
                    @click="updatePerPage({{ $perPageOption }}); perPageOpen = false"
                    :class="perPage == {{ $perPageOption }} ? 'bg-primary/10 text-black' : 'hover:bg-primary/20 hover:text-primary'"
                    class="inline-flex w-full cursor-pointer items-center justify-between gap-2 rounded-md px-4 py-2 text-left text-sm"
                >
                    {{ $perPageOption }}
                </div>
            @endforeach
        </div>
    </div>
    @endif
</nav>
