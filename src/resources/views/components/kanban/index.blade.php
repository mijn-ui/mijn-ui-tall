@once
<style>
    [draggable="true"] {
        cursor: grab;
        transition: transform 0.1s ease, box-shadow 0.2s ease;
    }

    [draggable="true"]:active {
        cursor: grabbing;
        transform: scale(1.02);
    }
    .bg-muted-darker {
        background-color: var(--muted);
        opacity: 0.3;
    }
    .dragging {
        opacity: 0.55;
        transform: scale(0.98);
    }

    .drop-zone-active {
        background-color: var(--muted);
        border: 2px dashed var(--primary);
    }
</style>
@endonce

@props([
    'width' => null,
    'maxWidth' => '352px',
    'columns' => [],
    'mode' => 'wire',
    'createAction' => 'createCardFromKanban',
    'updateAction' => 'updateCardFromKanban',
    'editAction' => 'startEdit',
    'moveAction' => 'updateCardStatus',
    'childMoveAction' => 'moveChildCard',
    'deleteAction' => null,
    'demoteToChildAction' => 'demoteParentToChild',
    'defaultCreatePayload' => [],
    'showAddButton' => true,
    'showEditButton' => true,
    'showDeleteButton' => false,
    'inlineEditable' => [],
    'validationRules' => [],
    'onBeforeCreate' => null,
    'onAfterCreate' => null,
    'onBeforeMove' => null,
    'onAfterMove' => null,
    'onBeforeUpdate' => null,
    'onAfterUpdate' => null,
    'onBeforeChildMove' => null,
    'onAfterChildMove' => null,
])

@php
    $hasCardTemplate = isset($cardTemplate) && ! $cardTemplate->isEmpty();
    $hasChildTemplate = isset($childTemplate) && ! $childTemplate->isEmpty();
    $widthStyle = '';
    if ($width) {
        $widthValue = is_numeric($width) ? "{$width}px" : $width;
        $widthStyle = "width: {$widthValue};";
    } else {
        $maxWidthValue = is_numeric($maxWidth) ? "{$maxWidth}px" : $maxWidth;
        $widthStyle = "max-width: {$maxWidthValue}; width: 100%;";
    }

    $containerClasses = 'kanban-column relative overflow-auto rounded-2xl border border-main-border bg-muted py-2';

    $palette = [
        'primary' => [
            'accent' => 'hsl(var(--primary))',
            'subtle' => 'hsl(var(--primary-subtle))',
            'foreground' => 'hsl(var(--primary-foreground-subtle))',
            'border' => 'hsl(var(--border-primary-subtle))',
            'contrast' => 'hsl(var(--primary-foreground))',
        ],
        'secondary' => [
            'accent' => 'hsl(var(--secondary-foreground))',
            'subtle' => 'hsl(var(--secondary))',
            'foreground' => 'hsl(var(--secondary-foreground))',
            'border' => 'hsl(var(--border-secondary))',
            'contrast' => 'hsl(var(--background))',
        ],
        'success' => [
            'accent' => 'hsl(var(--success))',
            'subtle' => 'hsl(var(--success-subtle))',
            'foreground' => 'hsl(var(--success-foreground-subtle))',
            'border' => 'hsl(var(--border-success-subtle))',
            'contrast' => 'hsl(var(--success-foreground))',
        ],
        'warning' => [
            'accent' => 'hsl(var(--warning))',
            'subtle' => 'hsl(var(--warning-subtle))',
            'foreground' => 'hsl(var(--warning-foreground-subtle))',
            'border' => 'hsl(var(--border-warning-subtle))',
            'contrast' => 'hsl(var(--warning-foreground))',
        ],
        'danger' => [
            'accent' => 'hsl(var(--danger))',
            'subtle' => 'hsl(var(--danger-subtle))',
            'foreground' => 'hsl(var(--danger-foreground-subtle))',
            'border' => 'hsl(var(--border-danger-subtle))',
            'contrast' => 'hsl(var(--danger-foreground))',
        ],
        'info' => [
            'accent' => '#2563eb',
            'subtle' => 'rgba(37, 99, 235, 0.12)',
            'foreground' => '#1d4ed8',
            'border' => 'rgba(37, 99, 235, 0.24)',
            'contrast' => '#eff6ff',
        ],
    ];

    $allowedColors = array_keys($palette);

    $normalizeColor = function (?string $color) use ($allowedColors) {
        $value = strtolower((string) $color);

        return in_array($value, $allowedColors, true) ? $value : 'secondary';
    };

    $toneFor = function (?string $color) use ($palette, $normalizeColor) {
        return $palette[$normalizeColor($color)] ?? $palette['secondary'];
    };

    $buildToneVars = function (?string $color) use ($toneFor) {
        $tones = $toneFor($color);

        return implode('; ', [
            "--kanban-accent: {$tones['accent']}",
            "--kanban-subtle: {$tones['subtle']}",
            "--kanban-foreground: {$tones['foreground']}",
            "--kanban-border: {$tones['border']}",
            "--kanban-contrast: {$tones['contrast']}",
        ]);
    };

    $normalizeProgress = fn ($value) => max(0, min(100, (int) round(is_numeric($value) ? (float) $value : 0)));

    $normalizeChild = function (array $child, string $fallbackColor, string $columnKey, string|int $cardId, int $childIndex) use ($normalizeColor, $normalizeProgress, $buildToneVars) {
        $childColor = $normalizeColor($child['color'] ?? $fallbackColor);
        $childId = $child['id'] ?? "{$cardId}-child-{$childIndex}";

        return [
            'id' => $childId,
            'uid' => $child['uid'] ?? "{$columnKey}-card-{$cardId}-child-{$childId}",
            'name' => $child['name'] ?? $child['title'] ?? 'Untitled child',
            'type' => $child['type'] ?? 'Child',
            'status_name' => $child['status_name'] ?? '',
            'progress' => $normalizeProgress($child['progress'] ?? 0),
            'color' => $childColor,
            'meta' => is_array($child['meta'] ?? null) ? $child['meta'] : [],
            'styles' => [
                'vars' => $buildToneVars($childColor),
            ],
        ];
    };

    $normalizeCard = function (array $card, string $fallbackColor, string $columnKey, int $cardIndex) use ($normalizeColor, $normalizeProgress, $normalizeChild, $buildToneVars) {
        $cardId = $card['id'] ?? "{$columnKey}-{$cardIndex}";
        $cardColor = $normalizeColor($card['color'] ?? $fallbackColor);
        $children = [];

        foreach (array_values($card['children'] ?? []) as $childIndex => $child) {
            if (is_array($child)) {
                $children[] = $normalizeChild($child, $cardColor, $columnKey, $cardId, $childIndex);
            }
        }

        return [
            'id' => $cardId,
            'uid' => $card['uid'] ?? "{$columnKey}-card-{$cardId}",
            'title' => $card['title'] ?? $card['name'] ?? 'Untitled',
            'color' => $cardColor,
            'progress' => $normalizeProgress($card['progress'] ?? 0),
            'showProgress' => (bool) ($card['showProgress'] ?? (($card['progress'] ?? 0) > 0)),
            'tags' => array_values($card['tags'] ?? []),
            'items' => array_values($card['items'] ?? []),
            'avatars' => array_values($card['avatars'] ?? []),
            'is_parent' => (bool) ($card['is_parent'] ?? !empty($children)),
            'children' => $children,
            'has_children' => count($children) > 0,
            'meta' => is_array($card['meta'] ?? null) ? $card['meta'] : [],
            'styles' => [
                'vars' => $buildToneVars($cardColor),
            ],
        ];
    };

    $normalizedColumns = [];
    $normalizedCards = [];

    foreach ($columns as $columnKey => $column) {
        $columnName = $column['name'] ?? \Illuminate\Support\Str::headline((string) $columnKey);
        $columnColor = $normalizeColor($column['color'] ?? null);
        $columnCards = array_values($column['cards'] ?? []);

        $normalizedColumns[$columnKey] = [
            'id' => (string) $columnKey,
            'name' => $columnName,
            'color' => $columnColor,
            'createPayload' => is_array($column['createPayload'] ?? null) ? $column['createPayload'] : [],
            'editForm' => is_array($column['editForm'] ?? null) ? $column['editForm'] : [],
            'styles' => [
                'vars' => $buildToneVars($columnColor),
            ],
        ];

        $normalizedCards[$columnKey] = [];

        foreach ($columnCards as $cardIndex => $card) {
            if (is_array($card)) {
                $normalizedCards[$columnKey][] = $normalizeCard($card, $columnColor, (string) $columnKey, $cardIndex);
            }
        }
    }
@endphp

<div
    x-ref="kanban"
    x-data="kanbanBoard({
        columns: @js($normalizedColumns),
        cards: @js($normalizedCards),
        widthStyle: @js($widthStyle),
        palette: @js($palette),
        mode: @js($mode),
        createAction: @js($createAction),
        updateAction: @js($updateAction),
        editAction: @js($editAction),
        moveAction: @js($moveAction),
        childMoveAction: @js($childMoveAction),
        deleteAction: @js($deleteAction),
        demoteToChildAction: @js($demoteToChildAction),
        defaultCreatePayload: @js($defaultCreatePayload),
        showAddButton: @js($showAddButton),
        showEditButton: @js($showEditButton),
        showDeleteButton: @js($showDeleteButton),
        hasCardTemplate: @js($hasCardTemplate),
        hasChildTemplate: @js($hasChildTemplate),
        inlineEditable: @js($inlineEditable),
        validationRules: @js($validationRules),
        callbackNames: {
            onBeforeCreate: @js($onBeforeCreate),
            onAfterCreate: @js($onAfterCreate),
            onBeforeMove: @js($onBeforeMove),
            onAfterMove: @js($onAfterMove),
            onBeforeUpdate: @js($onBeforeUpdate),
            onAfterUpdate: @js($onAfterUpdate),
            onBeforeChildMove: @js($onBeforeChildMove),
            onAfterChildMove: @js($onAfterChildMove),
        },
    })"
    x-init="init()"
    class="flex w-full items-start justify-center gap-5"
>
    <template x-for="(column, columnId) in columns" :key="columnId">
        <div
            {{ $attributes->merge(['class' => $containerClasses]) }}
            x-bind:style="getColumnStyle(columnId)"
            @dragover.prevent="
                $event.dataTransfer.dropEffect = 'move';
                $el.classList.add('drop-zone-active');
            "
            @dragleave="$el.classList.remove('drop-zone-active')"
            @drop.prevent="
                handleDrop($event, columnId);
                $el.classList.remove('drop-zone-active');
            "
        >
            <div class="flex w-full items-center px-3 py-2">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex h-2.5 w-2.5 rounded-full"
                        x-bind:style="getAccentDotStyle(column)"
                    ></span>
                    <h3 class="font-medium text-main-text sm:text-lg" x-text="column.name"></h3>
                    <span
                        class="flex h-5 w-5 items-center justify-center rounded-full bg-surface text-xs font-medium text-muted-text"
                        x-text="cards[columnId]?.length || 0"></span>
                </div>
                <button
                    aria-label="Column options"
                    class="disabled:text-muted-text/75-text inline-flex h-7 w-7 items-center justify-center gap-1 rounded-full text-sm text-muted-text hover:bg-accent hover:text-main-text">
                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                        stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="12" cy="5" r="1"></circle>
                        <circle cx="12" cy="19" r="1"></circle>
                    </svg>
                </button>
            </div>

            <!-- Cards Container -->
            <div class="px-4 py-2 space-y-4">
                <template x-for="(card, index) in cards[columnId] || []" :key="card.id">
                    <div>
                        <!-- Main Card -->
                        <div
                            class="w-full cursor-pointer rounded-lg bg-surface p-4 space-y-3"
                            draggable="true"
                            @dragstart="handleDragStart($event, card, columnId, index)"
                            @dragend="isDragging = false"
                            @dragover.prevent="updateDropIndex(columnId, index)"
                            @dragleave="dropIndex = null"
                            :class="{
                                'dragging': isDragging && draggedCard?.id === card.id
                            }"
                            @click="$wire.startEdit(card.id)"
                        >
                            <h5 class="text-sm font-medium" x-text="card.title"></h5>

                            <!-- Tags -->
                            <template x-if="card.tags && card.tags.length > 0">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="tag in card.tags">
                                        <span
                                            class="inline-flex items-center justify-center rounded-full border px-2.5 py-0.5 text-xs hover:bg-accent"
                                            x-text="tag">
                                        </span>
                                    </template>
                                </div>
                            </template>

                                <div class="space-y-1" x-show="card.showProgress">
                                    <div class="flex items-center justify-between text-xs text-muted-text">
                                         <p x-text="`${card.progress}%`"></p>
                                    </div>
                                    <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted">
                                        <div
                                            class="kanban-progress-fill h-full rounded-full"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                            :aria-valuenow="card.progress"
                                            role="progressbar"
                                            x-bind:style="getProgressFillStyle(card)"
                                        ></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-muted-text sm:gap-4">
                                    <template x-if="card.items.length > 0">
                                        <template x-for="item in card.items" :key="`${card.uid}-item-${item.icon || 'text'}-${item.text || ''}`">
                                            <div class="flex items-center gap-1"> 
                                                <template x-if="item.icon">
                                                    <span class="h-5 w-5" x-html="getIcon(item.icon)"></span> 
                                                </template>
                                                <span class="text-xs" x-text="item.text || ''"></span>
                                            </div>
                                        </template>
                                    </template>

                                    <template x-if="card.avatars.length > 0">
                                        <mijnui:avatar.group class="w-full justify-end">
                                            <template x-for="avatar in card.avatars" :key="`${card.uid}-avatar-${avatar.image || avatar.initials || 'blank'}`">
                                                <div
                                                    class="relative flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted text-xs ring-1 ring-muted-text/75">
                                                    <img
                                                        x-show="avatar.image"
                                                        alt="avatar"
                                                        class="h-full w-full object-cover"
                                                        :src="avatar.image"
                                                    />
                                                    <span x-show="!avatar.image && avatar.initials" x-text="avatar.initials"></span>
                                                </div>
                                            </template>
                                        </mijnui:avatar.group>
                                    </template>
                                </div>
                            </div>
                        @endif

                        {{-- Children Cards --}}
                        <template x-if="card.has_children && card.children.length > 0 && isChildrenExpanded(card.uid)">
                            <div class="kanban-children-enter ml-4 mt-2 space-y-2 border-muted pl-3">
                                <template x-for="(child, childIndex) in card.children" :key="child.uid">
                                    @if ($hasChildTemplate)
                                        <div
                                            class="kanban-child-card rounded-lg border p-2.5"
                                            draggable="true"
                                            @dragstart.stop="handleChildDragStart($event, child, columnId, card.id, childIndex)"
                                            @dragend="resetDragState()"
                                            :class="{ 'dragging': isDragging && draggedChild?.uid === child.uid }"
                                            x-bind:style="getChildRowStyle(child)"
                                            @click.stop="handleEdit(child.id)"
                                        >
                                            {{ $childTemplate }}
                                        </div>
                                    @else
                                        <div
                                            class="kanban-child-card cursor-pointer space-y-2 rounded-lg border p-3"
                                            draggable="true"
                                            @dragstart.stop="handleChildDragStart($event, child, columnId, card.id, childIndex)"
                                            @dragend="resetDragState()"
                                            :class="{ 'dragging': isDragging && draggedChild?.uid === child.uid }"
                                            x-bind:style="getChildRowStyle(child)"
                                            @click.stop="handleEdit(child.id)"
                                        >
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex min-w-0 items-center gap-2">
                                                    <span class="kanban-grip flex shrink-0 items-center text-muted-text">
                                                        <svg width="8" height="12" viewBox="0 0 10 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="2" cy="2" r="1.5"/>
                                                            <circle cx="8" cy="2" r="1.5"/>
                                                            <circle cx="2" cy="7" r="1.5"/>
                                                            <circle cx="8" cy="7" r="1.5"/>
                                                            <circle cx="2" cy="12" r="1.5"/>
                                                            <circle cx="8" cy="12" r="1.5"/>
                                                        </svg>
                                                    </span>
                                                    <div class="min-w-0">
                                                        <span class="block truncate text-xs font-semibold text-main-text" x-text="child.name"></span>
                                                        {{-- <template x-if="child.status_name">
                                                            <p class="mt-0.5 text-[10px] text-muted-text" x-text="child.status_name"></p>
                                                        </template> --}}
                                                    </div>
                                                </div>

                                                <div class="kanban-card-actions flex shrink-0 items-center gap-1" @click.stop>
                                                    <template x-if="showEditButton && hasAction(updateAction)">
                                                        <span
                                                            class="cursor-pointer text-muted-text hover:text-main-text"
                                                            @click.stop="requestEdit(columnId, card, child)"
                                                        >
                                                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                                                stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                <circle cx="12" cy="12" r="1"></circle>
                                                                <circle cx="12" cy="5" r="1"></circle>
                                                                <circle cx="12" cy="19" r="1"></circle>
                                                            </svg>
                                                        </span>
                                                    </template>

                                                    <template x-if="showDeleteButton && hasAction(deleteAction)">
                                                        <span
                                                            class="cursor-pointer text-muted-text hover:text-danger transition-colors"
                                                            @click.stop="pendingDelete = { columnId, cardId: child.id, kind: 'child', parentCardId: card.id, title: child.name }"
                                                        >
                                                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                                                stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3 6h18"></path>
                                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                                            </svg>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>

                                            <template x-if="child.progress > 0">
                                                <div class="space-y-0.5">
                                                    <div class="flex items-center justify-between"> 
                                                        <span class="text-[10px] font-semibold" x-bind:style="'color: var(--kanban-accent);'" x-text="`${child.progress}%`"></span>
                                                    </div>
                                                    <div class="relative h-1 w-full overflow-hidden rounded-full" style="background-color: var(--kanban-border);">
                                                        <div
                                                            class="kanban-progress-fill h-full rounded-full"
                                                            style="background-color: var(--kanban-accent);"
                                                            :style="'width:' + Math.min(100, Math.max(0, child.progress)) + '%'"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @endif
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="relative flex items-center justify-between gap-4 px-4 py-2">
                    <template x-if="showAddButton && hasAction(createAction)">
                        <mijnui:button
                            type="button"
                            color="secondary"
                            variant="ghost"
                            justify="start"
                            class="gap-2 text-sm font-medium"
                            x-bind:style="getAddButtonStyle(columnId)"
                            @click="requestCreate(columnId)"
                        >
                            <span>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5v14"></path>
                                </svg>
                            </span>
                            Add a new item
                        </mijnui:button>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- New Card Modal -->
    <div
        x-show="showModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showModal = false"
    >
        <div
            class="bg-surface rounded-lg p-6 w-full max-w-md shadow-md"
        >
            <h2 class="text-lg font-semibold mb-4 text-main-text">Add New Work Item</h2>

            <input
                type="text"
                x-model="newCardTitle"
                @input="errorMessage = ''"
                @keydown.enter="addNewCard"
                x-ref="newCardInput"
                class="w-full border border-main-border rounded-lg px-4 py-2 bg-surface text-main-text focus:outline-none focus:ring-2 focus:ring-primary"
                placeholder="Enter work item title..."
            />

            <template x-if="errorMessage">
                <p class="text-sm text-danger mt-1" x-text="errorMessage"></p>
            </template>

            <div class="flex justify-end gap-2 mt-4">
                <button
                    class="px-4 py-2 rounded bg-muted text-muted-text hover:bg-accent"
                    @click="showModal = false"
                >
                    Cancel
                </button>
                <button
                    class="px-4 py-2 rounded bg-primary text-primary-foreground hover:bg-primary/80"
                    @click="addNewCard"
                >
                    Add
                </button>
            </div>
        </div>
    </div>
</div>

@once
<script>

    function kanbanBoard(initialData) {
        return {
            columns: config.columns || {},
            cards: config.cards || {},
            widthStyle: config.widthStyle || '',
            palette: config.palette || {},
            mode: config.mode || 'wire',
            createAction: config.createAction ?? null,
            updateAction: config.updateAction ?? null,
            editAction: config.editAction ?? null,
            moveAction: config.moveAction ?? null,
            childMoveAction: config.childMoveAction ?? null,
            deleteAction: config.deleteAction ?? null,
            demoteToChildAction: config.demoteToChildAction ?? null,
            defaultCreatePayload: config.defaultCreatePayload || {},
            showAddButton: Boolean(config.showAddButton ?? true),
            showEditButton: Boolean(config.showEditButton ?? true),
            showDeleteButton: Boolean(config.showDeleteButton ?? false),
            hasCardTemplate: Boolean(config.hasCardTemplate),
            hasChildTemplate: Boolean(config.hasChildTemplate),
            inlineEditable: Array.isArray(config.inlineEditable) ? config.inlineEditable : [],
            validationRules: config.validationRules || {},

            // Drag state
            draggedCard: null,
            draggedChild: null,
            dragType: null,
            sourceColumn: null,
            sourceIndex: null,
            dragSourceParentId: null,
            isDragging: false,
            iconCache: {},
            dropIndex: null,
            dropColumn: null,

            // Expand/collapse state
            expandedCards: {},

            // Inline editing state
            inlineEdit: { cardId: null, field: null },
            inlineEditValue: '',

            // Delete confirmation state
            pendingDelete: null,
            inlineEditError: '',
            inlineEditColumnId: null,

            // Callback references
            __callbacks: {
                onBeforeCreate: null, onAfterCreate: null,
                onBeforeMove: null, onAfterMove: null,
                onBeforeUpdate: null, onAfterUpdate: null,
                onBeforeChildMove: null, onAfterChildMove: null,
            },

            init() {
                // Ensure all columns have card arrays
                Object.keys(this.columns).forEach(columnId => {
                    if (!this.cards[columnId]) {
                        this.cards[columnId] = [];
                    }
                });

            },

            getAddButtonStyle(columnId) {
                return this.composeStyle(this.columns[columnId]?.styles?.vars, 'color: var(--kanban-foreground);');
            },

            getModalPrimaryStyle(columnId = null) {
                const column = this.columns[columnId] || {};

                return this.composeStyle(
                    column.styles?.vars,
                    'background-color: var(--kanban-accent); color: var(--kanban-contrast);'
                );
            },

            // ── Icons ────────────────────────────────────────────────

            async getIcon(iconName) {
                if (!this.iconCache[iconName]) {
                    try {
                        const response = await fetch(`https://cdn.jsdelivr.net/npm/heroicons@2.2.0/24/outline/${iconName}.svg`);
                        this.iconCache[iconName] = await response.text();
                    } catch {
                        this.iconCache[iconName] = '<span class="text-red-500">?</span>';
                    }
                }

                return this.iconCache[iconName];
            },

            // ── Livewire Bridge ──────────────────────────────────────

            async callWireAction(action, ...params) {
                if (!this.isWireMode() || !this.hasAction(action)) {
                    return null;
                }

                return this.$wire.call(action, ...params);
            },

            // ── Expand/Collapse ──────────────────────────────────────

            toggleChildren(cardUid) {
                this.expandedCards[cardUid] = !this.expandedCards[cardUid];
            },

            isChildrenExpanded(cardUid) {
                return this.expandedCards[cardUid] !== false;
            },

            // ── Event Dispatchers (replace modals) ───────────────────

            requestCreate(columnId) {
                const column = this.columns[columnId] || {};
                this.$dispatch('kanban:create-requested', {
                    columnId,
                    column: JSON.parse(JSON.stringify(column)),
                    meta: {
                        ...(this.defaultCreatePayload || {}),
                        ...(column.createPayload || {}),
                    },
                });
            },

            requestEdit(columnId, card, child = null) {
                this.$dispatch('kanban:edit-requested', {
                    cardId: child ? child.id : card.id,
                    columnId,
                    kind: child ? 'child' : 'parent',
                    card: JSON.parse(JSON.stringify(card)),
                    child: child ? JSON.parse(JSON.stringify(child)) : null,
                });
            },

            requestChildMove(child, columnId, parentCardId) {
                this.$dispatch('kanban:child-move-requested', {
                    childId: child.id,
                    child: JSON.parse(JSON.stringify(child)),
                    columnId,
                    parentCardId,
                    columns: JSON.parse(JSON.stringify(this.columns)),
                    cards: JSON.parse(JSON.stringify(this.cards)),
                });
            },

            // ── Edit Click ───────────────────────────────────────────

            handleEdit(cardId) {
                this.$dispatch('kanban:edit-clicked', { cardId });

                if (this.isWireMode() && this.hasAction(this.editAction)) {
                    void this.callWireAction(this.editAction, cardId);
                }
            },

            // ── Inline Editing ───────────────────────────────────────

            isInlineEditing(cardId, field) {
                return this.inlineEdit.cardId === cardId && this.inlineEdit.field === field;
            },

            isFieldInlineEditable(field) {
                return this.inlineEditable.includes(field);
            },

            startInlineEdit(cardId, field, currentValue, columnId) {
                if (!this.isFieldInlineEditable(field)) return;
                this.inlineEdit = { cardId, field };
                this.inlineEditValue = currentValue;
                this.inlineEditError = '';
                this.inlineEditColumnId = columnId;
            },

            cancelInlineEdit() {
                this.inlineEdit = { cardId: null, field: null };
                this.inlineEditValue = '';
                this.inlineEditError = '';
                this.inlineEditColumnId = null;
            },

            async confirmInlineEdit(card, columnId) {
                if (!this.inlineEdit.cardId) return;

                const field = this.inlineEdit.field;
                const value = this.inlineEditValue;

                const fieldRules = this.validationRules[field];
                if (fieldRules) {
                    const error = this.validateSingleField(value, fieldRules);
                    if (error) {
                        this.inlineEditError = error;
                        return;
                    }
                }

                const detail = { cardId: card.id, columnId, field, value, card: JSON.parse(JSON.stringify(card)) };
                if (!await this.fireBeforeHook('Update', detail)) {
                    this.cancelInlineEdit();
                    return;
                }

                card[field] = value;
                if (field === 'title') card.title = value;
                if (field === 'color') card.styles = { vars: this.buildToneVars(value) };

                if (this.isWireMode() && this.hasAction(this.updateAction)) {
                    try {
                        const payload = {
                            cardId: Number(card.id),
                            columnId,
                            meta: card.meta || {},
                            title: field === 'title' ? value : null,
                            progress: field === 'progress' ? this.normalizeProgress(value) : null,
                            color: field === 'color' ? this.normalizeColor(value) : null,
                            form: { [field]: value },
                        };
                        const result = await this.callWireAction(this.updateAction, payload);
                        if (result && typeof result === 'object') {
                            this.applyCardUpdateResult(result);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                }

                this.fireAfterHook('Update', { cardId: card.id, columnId, [field]: value });
                this.cancelInlineEdit();
            },

            // ── Card Finder ──────────────────────────────────────────

            findParentCard(columnId, cardId) {
                return (this.cards[columnId] || []).find((card) => String(card.id) === String(cardId)) || null;
            },

            // ── Parent-Child Sync ────────────────────────────────────

            syncParentChildItem(card) {
                const childCount = Array.isArray(card.children) ? card.children.length : 0;
                const currentItems = Array.isArray(card.items) ? [...card.items] : [];
                const childCountIndex = currentItems.findIndex((item) => item.icon === 'queue-list');

                if (childCount > 0 && childCountIndex === -1) {
                    currentItems.push({ icon: 'queue-list', text: childCount });
                }

                if (childCount > 0 && childCountIndex !== -1) {
                    currentItems[childCountIndex] = { ...currentItems[childCountIndex], text: childCount };
                }

                if (childCount === 0 && childCountIndex !== -1) {
                    currentItems.splice(childCountIndex, 1);
                }

                card.items = currentItems;
                card.has_children = childCount > 0;
                card.is_parent = true;
            },

            removeChildFromLocalState(childId) {
                for (const [columnId, cards] of Object.entries(this.cards)) {
                    for (const card of cards) {
                        const childIndex = (card.children || []).findIndex((child) => String(child.id) === String(childId));

                        if (childIndex !== -1) {
                            const [child] = card.children.splice(childIndex, 1);
                            this.syncParentChildItem(card);

                            return {
                                child,
                                sourceColumnId: columnId,
                                sourceParentId: card.id,
                            };
                        }
                    }
                }

                return null;
            },

            // ── Result Appliers ──────────────────────────────────────

            applyChildMoveResult(result, movingChildId, targetColumn, targetParentId) {
                const removed = this.removeChildFromLocalState(movingChildId);
                const targetColumnId = result?.columnId || targetColumn;

                if (!removed || !targetColumnId) return;

                if (result?.mode === 'parent' || !targetParentId) {
                    if (!Array.isArray(this.cards[targetColumnId])) {
                        this.cards[targetColumnId] = [];
                    }

                    this.cards[targetColumnId].push(
                        this.hydrateCard(result?.item || {
                            id: removed.child.id,
                            title: removed.child.name,
                            color: removed.child.color || this.columns[targetColumnId]?.color,
                            progress: removed.child.progress,
                            showProgress: removed.child.progress > 0,
                            tags: [],
                            items: [],
                            avatars: [],
                            children: [],
                            meta: removed.child.meta || {},
                        }, targetColumnId, this.cards[targetColumnId].length)
                    );

                    return;
                }

                const targetParent = this.findParentCard(targetColumnId, targetParentId);
                if (!targetParent) return;

                if (!Array.isArray(targetParent.children)) {
                    targetParent.children = [];
                }

                targetParent.children.push(
                    this.hydrateChild(
                        result?.item || { ...removed.child, color: removed.child.color || targetParent.color },
                        targetParent.color,
                        `${targetColumnId}-card-${targetParent.id}-child-${result?.item?.id || removed.child.id}`
                    )
                );

                this.syncParentChildItem(targetParent);
            },

            applyCardUpdateResult(result) {
                const columnId = result.columnId;
                if (!columnId || !result?.item) return;

                if (result.mode === 'parent') {
                    const cards = this.cards[columnId] || [];
                    const cardIndex = cards.findIndex((card) => String(card.id) === String(result.item.id));
                    if (cardIndex === -1) return;
                    cards.splice(cardIndex, 1, this.hydrateCard(result.item, columnId, cardIndex));
                    return;
                }

                const parent = this.findParentCard(columnId, result.parentId);
                if (!parent) return;

                const childIndex = (parent.children || []).findIndex((child) => String(child.id) === String(result.item.id));
                if (childIndex === -1) return;

                parent.children.splice(
                    childIndex, 1,
                    this.hydrateChild(result.item, parent.color, `${columnId}-card-${parent.id}-child-${result.item.id}`)
                );
                this.syncParentChildItem(parent);
            },

            // ── Public API ───────────────────────────────────────────

            async addCard(columnId, cardData) {
                const detail = { columnId, cardData };

                console.log(detail)
                if (!await this.fireBeforeHook('Create', detail)) return false;

                const errors = this.validateFields(cardData, this.validationRules);
                if (Object.keys(errors).length > 0) {
                    this.$dispatch('kanban:validation-failed', { errors, operation: 'create' });
                    return false;
                }

                if (this.isWireMode() && this.hasAction(this.createAction)) {
                    try {
                        const payload = {
                            title: cardData.title || null,
                            columnId,
                            meta: {
                                ...(this.defaultCreatePayload || {}),
                                ...(this.columns[columnId]?.createPayload || {}),
                                ...(cardData.meta || {}),
                            },
                            form: cardData,
                        };
                        const createdCard = await this.callWireAction(this.createAction, payload);
                        if (!createdCard || typeof createdCard !== 'object') {
                            throw new Error('Create action must return a card payload.');
                        }
                        this.pushCard(columnId, createdCard);
                        this.fireAfterHook('Create', { card: createdCard, columnId });
                        return true;
                    } catch (error) {
                        console.error(error);
                        return false;
                    }
                }

                const optimisticCard = {
                    id: `temp-${Date.now()}`,
                    title: cardData.title || 'Untitled',
                    color: cardData.color || this.columns[columnId]?.color || 'secondary',
                    progress: cardData.progress ? this.normalizeProgress(cardData.progress) : 0,
                    showProgress: (cardData.progress || 0) > 0,
                    tags: cardData.tags || [],
                    items: cardData.items || [],
                    avatars: cardData.avatars || [],
                    children: cardData.children || [],
                    meta: cardData.meta || {},
                };
                this.pushCard(columnId, optimisticCard);
                this.fireAfterHook('Create', { card: optimisticCard, columnId });
                return true;
            },

            pushCard(columnId, cardData) {
                if (!Array.isArray(this.cards[columnId])) {
                    this.cards[columnId] = [];
                }

                this.cards[columnId].push(
                    this.hydrateCard(cardData, columnId, this.cards[columnId].length)
                );
            },

            removeCard(columnId, cardId) {
                if (!Array.isArray(this.cards[columnId])) return false;
                const index = this.cards[columnId].findIndex((c) => String(c.id) === String(cardId));
                if (index === -1) return false;
                this.cards[columnId].splice(index, 1);
                return true;
            },

            async deleteCard(columnId, cardId, kind = 'parent', parentCardId = null) {
                if (!this.hasAction(this.deleteAction)) return false;

                const payload = { cardId, columnId, kind };
                if (parentCardId) payload.parentCardId = parentCardId;

                if (this.isWireMode()) {
                    try {
                        await this.callWireAction(this.deleteAction, payload);
                    } catch (e) {
                        console.error(e);
                        return false;
                    }
                }

                if (kind === 'child' && parentCardId) {
                    this.removeChildFromLocalState(cardId);
                } else {
                    this.removeCard(columnId, cardId);
                }

                this.$dispatch('kanban:card-deleted', { cardId, columnId, kind });
                return true;
            },

            async moveCard(cardId, fromColumn, toColumn, index) {
                const detail = { cardId, fromColumn, toColumn, index };
                if (!await this.fireBeforeHook('Move', detail)) return false;

                const sourceCards = this.cards[fromColumn] || [];
                const cardIndex = sourceCards.findIndex((c) => String(c.id) === String(cardId));
                if (cardIndex === -1) return false;

                const [card] = sourceCards.splice(cardIndex, 1);
                if (!Array.isArray(this.cards[toColumn])) this.cards[toColumn] = [];

                const targetCards = this.cards[toColumn];
                const insertAt = Math.max(0, Math.min(index ?? targetCards.length, targetCards.length));
                const movedCard = this.hydrateCard(
                    { ...card, color: this.columns[toColumn]?.color || card.color },
                    toColumn, insertAt
                );
                targetCards.splice(insertAt, 0, movedCard);

                const payload = { cardId, fromColumnId: fromColumn, toColumnId: toColumn, dropIndex: insertAt, dragType: 'parent' };
                if (this.isWireMode() && this.hasAction(this.moveAction)) {
                    try { await this.callWireAction(this.moveAction, payload); } catch (e) { console.error(e); }
                }

                this.fireAfterHook('Move', payload);
                return true;
            },

            async demoteParentToChild(draggedCard, sourceColumnId, targetCard, targetColumnId) {
                const payload = {
                    cardId: Number(draggedCard.id),
                    targetParentId: Number(targetCard.id),
                    targetColumnId,
                    sourceColumnId,
                };

                if (!await this.fireBeforeHook('Move', payload)) return;

                // Remove parent from source column
                const sourceCards = this.cards[sourceColumnId] || [];
                const cardIndex = sourceCards.findIndex((c) => String(c.id) === String(draggedCard.id));
                if (cardIndex === -1) return;
                sourceCards.splice(cardIndex, 1);

                // Convert to child and add to target parent
                if (!Array.isArray(targetCard.children)) {
                    targetCard.children = [];
                }

                const childData = {
                    id: draggedCard.id,
                    name: draggedCard.title,
                    type: 'Child',
                    status_name: '',
                    progress: draggedCard.progress || 0,
                    color: draggedCard.color || targetCard.color,
                    meta: draggedCard.meta || {},
                };

                targetCard.children.push(
                    this.hydrateChild(
                        childData,
                        targetCard.color,
                        `${targetColumnId}-card-${targetCard.id}-child-${draggedCard.id}`
                    )
                );
                this.syncParentChildItem(targetCard);
                this.expandedCards[targetCard.uid] = true;

                // Call Livewire action
                if (this.isWireMode() && this.hasAction(this.demoteToChildAction)) {
                    try {
                        const result = await this.callWireAction(this.demoteToChildAction, payload);
                        if (result && typeof result === 'object' && result.item) {
                            // Update the child with server-returned data
                            const childIndex = targetCard.children.findIndex((c) => String(c.id) === String(draggedCard.id));
                            if (childIndex !== -1) {
                                targetCard.children.splice(childIndex, 1,
                                    this.hydrateChild(result.item, targetCard.color, `${targetColumnId}-card-${targetCard.id}-child-${result.item.id || draggedCard.id}`)
                                );
                                this.syncParentChildItem(targetCard);
                            }
                        }
                    } catch (error) {
                        console.error(error);
                    }
                }

                this.fireAfterHook('Move', { ...payload, mode: 'demote' });
            },

            updateCard(columnId, cardId, data) {
                const cards = this.cards[columnId] || [];
                const card = cards.find((c) => String(c.id) === String(cardId));
                if (!card) return false;
                Object.assign(card, data);
                if (data.color) {
                    card.color = this.normalizeColor(data.color);
                    card.styles = { vars: this.buildToneVars(card.color) };
                }
                if (data.title !== undefined) card.title = data.title;
                if (data.progress !== undefined) {
                    card.progress = this.normalizeProgress(data.progress);
                    card.showProgress = card.progress > 0;
                }
                if (data.children !== undefined) {
                    card.children = data.children;
                    card.has_children = card.children.length > 0;
                }
                return true;
            },

            async saveCard(columnId, cardId, data) {
                const detail = { columnId, cardId, data };
                if (!await this.fireBeforeHook('Update', detail)) return false;

                this.updateCard(columnId, cardId, data);

                if (this.isWireMode() && this.hasAction(this.updateAction)) {
                    try {
                        const payload = {
                            cardId: Number(cardId),
                            columnId,
                            meta: data.meta || {},
                            title: data.title ?? null,
                            progress: data.progress !== undefined ? this.normalizeProgress(data.progress) : null,
                            color: data.color ?? null,
                            form: data,
                        };
                        const result = await this.callWireAction(this.updateAction, payload);
                        if (result && typeof result === 'object') {
                            this.applyCardUpdateResult(result);
                        }
                    } catch (error) {
                        console.error(error);
                        return false;
                    }
                }

                this.fireAfterHook('Update', { cardId, columnId, ...data });
                return true;
            },

            patchCard(result) {
                if (!result || typeof result !== 'object') return;
                this.applyCardUpdateResult(result);
            },

            getCards(columnId) {
                return JSON.parse(JSON.stringify(this.cards[columnId] || []));
            },

            getCard(columnId, cardId) {
                const card = (this.cards[columnId] || []).find((c) => String(c.id) === String(cardId));
                return card ? JSON.parse(JSON.stringify(card)) : null;
            },

            // ── Drag and Drop ────────────────────────────────────────

            handleDragStart(event, card, columnId, index) {
                this.isDragging = true;
                this.dragType = 'parent';
                this.draggedCard = JSON.parse(JSON.stringify(card));
                this.draggedChild = null;
                this.sourceColumn = columnId;
                this.sourceIndex = index;
                this.dragSourceParentId = null;
                event.dataTransfer.effectAllowed = 'move';
            },

            handleChildDragStart(event, child, columnId, parentId, childIndex) {
                event.stopPropagation();
                this.isDragging = true;
                this.dragType = 'child';
                this.draggedChild = JSON.parse(JSON.stringify(child));
                this.draggedCard = null;
                this.sourceColumn = columnId;
                this.sourceIndex = childIndex;
                this.dragSourceParentId = parentId;
                event.dataTransfer.effectAllowed = 'move';
            },

            resetDragState() {
                this.draggedCard = null;
                this.draggedChild = null;
                this.dragType = null;
                this.sourceColumn = null;
                this.sourceIndex = null;
                this.dragSourceParentId = null;
                this.isDragging = false;
                this.dropIndex = null;
                this.dropColumn = null;

                document.querySelectorAll('.kanban-drop-target').forEach((el) => {
                    el.classList.remove('kanban-drop-target');
                });
            },

            handleDragOverParent(event, columnId, card, index) {
                if (!this.isDragging) return;

                event.dataTransfer.dropEffect = 'move';

                if (this.dragType === 'child') {
                    if (String(card.id) !== String(this.dragSourceParentId) || columnId !== this.sourceColumn) {
                        event.currentTarget.classList.add('kanban-drop-target');
                    }
                } else if (this.dragType === 'parent') {
                    if (this.hasAction(this.demoteToChildAction) && this.draggedCard && String(card.id) !== String(this.draggedCard.id)) {
                        event.currentTarget.classList.add('kanban-drop-target');
                    } else {
                        this.updateDropIndex(columnId, index);
                    }
                }
            },

            async handleDropOnParent(event, columnId, targetCard) {
                if (!this.isDragging) return;

                event.currentTarget.classList.remove('kanban-drop-target');

                if (this.dragType === 'child' && this.draggedChild) {
                    const payload = {
                        childId: this.draggedChild.id,
                        targetColumnId: columnId,
                        targetParentId: Number(targetCard.id),
                        mode: 'child',
                    };

                    if (!await this.fireBeforeHook('ChildMove', payload)) {
                        this.resetDragState();
                        return;
                    }

                    const removed = this.removeChildFromLocalState(this.draggedChild.id);
                    if (!removed) {
                        this.resetDragState();
                        return;
                    }

                    if (!Array.isArray(targetCard.children)) {
                        targetCard.children = [];
                    }

                    targetCard.children.push(
                        this.hydrateChild(
                            { ...removed.child, color: removed.child.color || targetCard.color },
                            targetCard.color,
                            `${columnId}-card-${targetCard.id}-child-${removed.child.id}`
                        )
                    );
                    this.syncParentChildItem(targetCard);
                    this.expandedCards[targetCard.uid] = true;

                    if (this.isWireMode() && this.hasAction(this.childMoveAction)) {
                        void this.callWireAction(this.childMoveAction, payload).catch(console.error);
                    }

                    this.fireAfterHook('ChildMove', payload);
                } else if (this.dragType === 'parent' && this.draggedCard) {
                    if (this.hasAction(this.demoteToChildAction) && String(targetCard.id) !== String(this.draggedCard.id)) {
                        await this.demoteParentToChild(this.draggedCard, this.sourceColumn, targetCard, columnId);
                    } else {
                        this.handleDrop(event, columnId);
                        return;
                    }
                }

                this.resetDragState();
            },

            async handleDrop(event, targetColumnId) {
                if (!this.isDragging) return;

                if (this.dragType === 'child' && this.draggedChild) {
                    const payload = {
                        childId: this.draggedChild.id,
                        targetColumnId,
                        targetParentId: null,
                        mode: 'parent',
                    };

                    if (!await this.fireBeforeHook('ChildMove', payload)) {
                        this.resetDragState();
                        return;
                    }

                    const removed = this.removeChildFromLocalState(this.draggedChild.id);
                    if (!removed) {
                        this.resetDragState();
                        return;
                    }

                    if (!Array.isArray(this.cards[targetColumnId])) {
                        this.cards[targetColumnId] = [];
                    }

                    this.cards[targetColumnId].push(
                        this.hydrateCard({
                            id: removed.child.id,
                            title: removed.child.name,
                            color: this.columns[targetColumnId]?.color || removed.child.color,
                            progress: removed.child.progress,
                            showProgress: removed.child.progress > 0,
                            tags: [],
                            items: [],
                            avatars: [],
                            children: [],
                            meta: removed.child.meta || {},
                        }, targetColumnId, this.cards[targetColumnId].length)
                    );

                    if (this.isWireMode() && this.hasAction(this.childMoveAction)) {
                        void this.callWireAction(this.childMoveAction, payload).catch(console.error);
                    }

                    this.fireAfterHook('ChildMove', payload);
                    this.resetDragState();
                    return;
                }

                if (!this.draggedCard || this.sourceColumn === null || this.sourceIndex === null) {
                    this.resetDragState();
                    return;
                }

                const sourceCards = this.cards[this.sourceColumn] || [];
                const draggedCard = sourceCards[this.sourceIndex];

                if (!draggedCard) {
                    this.resetDragState();
                    return;
                }

                const movePayload = {
                    cardId: draggedCard.id,
                    fromColumnId: this.sourceColumn,
                    toColumnId: targetColumnId,
                    dropIndex: null,
                    dragType: 'parent',
                };

                if (!await this.fireBeforeHook('Move', movePayload)) {
                    this.resetDragState();
                    return;
                }

                sourceCards.splice(this.sourceIndex, 1);

                if (!Array.isArray(this.cards[targetColumnId])) {
                    this.cards[targetColumnId] = [];
                }

                const targetCards = this.cards[targetColumnId];
                let insertIndex = this.dropColumn === targetColumnId && this.dropIndex !== null ? this.dropIndex : targetCards.length;

                if (this.sourceColumn === targetColumnId && this.sourceIndex < insertIndex) {
                    insertIndex -= 1;
                }

                insertIndex = Math.max(0, Math.min(insertIndex, targetCards.length));

                const movedCard = this.hydrateCard(
                    {
                        ...draggedCard,
                        color: this.sourceColumn === targetColumnId ? draggedCard.color : this.columns[targetColumnId]?.color || draggedCard.color,
                    },
                    targetColumnId,
                    insertIndex
                );

                targetCards.splice(insertIndex, 0, movedCard);

                movePayload.dropIndex = insertIndex;

                if (this.isWireMode() && this.hasAction(this.moveAction)) {
                    try {
                        await this.callWireAction(this.moveAction, movePayload);
                    } catch (error) {
                        console.error(error);
                    }
                }

                this.fireAfterHook('Move', movePayload);
                this.resetDragState();
            },

            updateDropIndex(columnId, index) {
                this.dropColumn = columnId;
                this.dropIndex = index;
            },
        };
    }
</script>
@endonce
