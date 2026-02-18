@props([
    'paginate' => null,
    'perPage' => null,
    'hoverable' => true,
    'striped' => false,
    'bordered' => false,
    'searchBy' => [],
    'viewableColumns' => [], // Array of objects: ['label' => 'Name', 'key' => 'name']
])

@php
    $wrapperClasses = [
        'relative w-full overflow-auto rounded-xl border border-border bg-background',
        'shadow-sm' => $bordered,
    ];

    $tableClasses = [
        'w-full text-left text-sm text-foreground rtl:text-right',
        '[&_tbody_tr:hover]:bg-secondary/50' => $hoverable,
        '[&_tbody_tr:nth-child(even)]:bg-muted/30' => $striped,
        'border-separate border-spacing-0' => $bordered,
        '[&_th]:border [&_td]:border' => $bordered,
    ];

    $hasSearch = !empty($searchBy);
    $hasViewableColumns = !empty($viewableColumns);
@endphp

<div {{ $attributes->class(['space-y-4']) }} 
    x-data="{
        visibleColumns: @js(collect($viewableColumns)->pluck('key')->toArray()),
        toggleColumn(key) {
            if (this.visibleColumns.includes(key)) {
                this.visibleColumns = this.visibleColumns.filter(c => c !== key);
            } else {
                this.visibleColumns.push(key);
            }
        }
    }"
>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-1">
        @if($hasSearch)
            <div class="relative max-w-sm w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
                    <x-mijnui::icon name="fa-solid fa-magnifying-glass" size="sm" />
                </div>
                <input 
                    type="search" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by {{ implode(', ', $searchBy) }}..."
                    class="block w-full pl-10 pr-3 py-2 border border-border rounded-lg bg-background text-sm focus:ring-primary focus:border-primary"
                >
            </div>
        @endif

        @if($hasViewableColumns)
            <x-mijnui::dropdown :close-on-select="false" align="right" teleport>
                <x-slot:trigger>
                    <x-mijnui::button variant="outline" class="gap-2">
                        <x-mijnui::icon name="fa-solid fa-filter" size="sm" />
                        <span>Filtered by</span>
                        <x-mijnui::icon name="fa-solid fa-chevron-down" size="sm" />
                    </x-mijnui::button>
                </x-slot:trigger>

                <x-slot:content>
                    <div class="min-w-[200px] space-y-1">
                        @foreach($viewableColumns as $column)
                            <x-mijnui::dropdown.item 
                                x-on:click.stop="toggleColumn('{{ $column['key'] }}')"
                                class="flex items-center justify-between"
                            >
                                <span>{{ $column['label'] }}</span>
                                <template x-if="visibleColumns.includes('{{ $column['key'] }}')">
                                    <x-mijnui::icon name="fa-solid fa-check" size="xs" class="text-primary" />
                                </template>
                            </x-mijnui::dropdown.item>
                        @endforeach
                    </div>
                </x-slot:content>
            </x-mijnui::dropdown>
        @endif
    </div>

    <div @class($wrapperClasses)>
        <table @class($tableClasses)>
            {{ $slot }}
        </table>
    </div>

    @if($paginate)
        <div class="px-1">
            <x-mijnui::pagination :data="$paginate" :$perPage />
        </div>
    @endif
</div>

