@props([
    'paginate' => null,
    'perPage' => null,
    'hoverable' => true,
    'striped' => false,
    'bordered' => false,
    'searchBy' => [],
    'hasViewable' => false,
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
@endphp

<div {{ $attributes->class(['space-y-4 mijnui-table-root']) }} 
    x-data="{
        hasViewable: @js($hasViewable),
        allColumns: [],
        visibleColumns: [],
        registerColumn(key, label, visible = true) {
            if (!this.allColumns.find(c => c.key === key)) {
                this.allColumns = [...this.allColumns, { key, label }];
                if (visible) {
                    this.visibleColumns = [...this.visibleColumns, key];
                }
            }
        },
        toggleColumn(key) {
            if (this.visibleColumns.includes(key)) {
                this.visibleColumns = this.visibleColumns.filter(c => c !== key);
            } else {
                this.visibleColumns = [...this.visibleColumns, key];
            }
        }
    }"
>
    @if($hasSearch || $hasViewable)
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-1">
            <div class="flex flex-1 items-center gap-2 max-w-md">
                @if($hasSearch)
                    <mijnui:input 
                        type="search" 
                        placeholder="Search by {{ implode(', ', $searchBy) }}..."
                        start-icon="fa-solid fa-magnifying-glass"
                        clearable
                        class="h-9"
                        {{ $attributes->only(['wire:model', 'wire:model.live', 'wire:model.live.debounce.300ms']) }}
                    />
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if($hasViewable)
                    <mijnui:dropdown :close-on-select="false" align="right">
                        <mijnui:dropdown.trigger>
                            <mijnui:button variant="outline" class="gap-2">
                                <span>Columns</span>
                                <mijnui:icon name="fa-solid fa-chevron-down" size="sm" />
                            </mijnui:button>
                        </mijnui:dropdown.trigger>

                        <mijnui:dropdown.content>
                            <div class="min-w-[200px] flex flex-col p-1">
                                <div class="space-y-0.5">
                                    <template x-for="column in allColumns" :key="column.key">
                                        <mijnui:dropdown.item 
                                            x-on:click.stop="toggleColumn(column.key)"
                                            class="flex items-center justify-between py-2 group"
                                        >
                                            <span x-text="column.label" class="flex-1 truncate"></span>
                                            <mijnui:switch 
                                                x-bind:checked="visibleColumns.includes(column.key)"
                                                x-on:change.stop="toggleColumn(column.key)"
                                                size="sm"
                                                class="pointer-events-none"
                                            />
                                        </mijnui:dropdown.item>
                                    </template>
                                </div>
                            </div>
                        </mijnui:dropdown.content>
                    </mijnui:dropdown>
                @endif
            </div>
        </div>
    @endif

    <div @class($wrapperClasses)>
        <table @class($tableClasses) @unless($attributes->has('aria-label')) aria-label="Data table" @endunless>
            {{ $slot }}
        </table>
    </div>

    @if($paginate)
        <div class="px-1">
            <mijnui:pagination :data="$paginate" :$perPage />
        </div>
    @endif
</div>
