<style>
    [draggable="true"] {
        cursor: grab;
        transition: transform 0.1s ease, box-shadow 0.2s ease;
    }

    [draggable="true"]:active {
        cursor: grabbing;
        transform: scale(1.02);
    }

    .dragging {
        opacity: 0.55;
        transform: scale(0.98);
    }

    .drop-zone-active {
        background-color: var(--kanban-subtle);
        border: 2px dashed var(--kanban-accent);
    }

    .kanban-column {
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .kanban-parent-card {
        transition: transform 0.15s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .kanban-parent-card:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .kanban-child-card {
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .kanban-progress-fill {
        transition: width 0.2s ease;
    }
</style>

@props([
    'width' => null,
    'maxWidth' => '352px',
    'columns' => [],
    'createAction' => 'createCardFromKanban',
    'updateAction' => 'updateCardFromKanban',
    'editAction' => 'startEdit',
    'moveAction' => 'updateCardStatus',
    'childMoveAction' => 'moveChildCard',
    'defaultCreatePayload' => [],
    'defaultCreateForm' => [],
    'defaultEditForm' => [],
])

@php
    $hasCustomCreateForm = isset($createForm) && ! $createForm->isEmpty();
    $hasCustomEditForm = isset($editForm) && ! $editForm->isEmpty();
    $livewireHost = app('livewire')->current();
    $livewireMethods = $livewireHost ? get_class_methods($livewireHost) : [];
    $resolveAction = function ($action) use ($livewireMethods) {
        $name = is_string($action) ? trim($action) : '';

        return $name !== '' && in_array($name, $livewireMethods, true) ? $name : null;
    };
    $resolvedCreateAction = $resolveAction($createAction);
    $resolvedUpdateAction = $resolveAction($updateAction);
    $resolvedEditAction = $resolveAction($editAction);
    $resolvedMoveAction = $resolveAction($moveAction);
    $resolvedChildMoveAction = $resolveAction($childMoveAction);
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
        // The package theme does not currently expose info tokens, so this component provides local info tones.
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
            'createForm' => is_array($column['createForm'] ?? null) ? $column['createForm'] : [],
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
    x-data="kanbanBoard({
        columns: @js($normalizedColumns),
        cards: @js($normalizedCards),
        widthStyle: @js($widthStyle),
        palette: @js($palette),
        createAction: @js($resolvedCreateAction),
        updateAction: @js($resolvedUpdateAction),
        editAction: @js($resolvedEditAction),
        moveAction: @js($resolvedMoveAction),
        childMoveAction: @js($resolvedChildMoveAction),
        defaultCreatePayload: @js($defaultCreatePayload),
        defaultCreateForm: @js($defaultCreateForm),
        defaultEditForm: @js($defaultEditForm),
        hasCustomCreateForm: @js($hasCustomCreateForm),
        hasCustomEditForm: @js($hasCustomEditForm),
    })"
    x-init="init()"
    class="flex w-full items-start justify-center gap-5"
>
    <template x-for="(column, columnId) in columns" :key="columnId">
        <div
            {{ $attributes->merge(['class' => $containerClasses]) }}
            x-bind:style="getColumnStyle(columnId)"
            @dragover.prevent="
                if (!hasAction(moveAction)) return;
                $event.dataTransfer.dropEffect = 'move';
                $el.classList.add('drop-zone-active');
            "
            @dragleave="if (!hasAction(moveAction)) return; $el.classList.remove('drop-zone-active')"
            @drop.prevent="
                if (!hasAction(moveAction)) return;
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
                        class="inline-flex min-w-8 items-center justify-center rounded-full px-2 py-0.5 text-xs font-semibold"
                        x-bind:style="getCountBadgeStyle(columnId)"
                        x-text="(cards[columnId] || []).length"
                    ></span>
                </div>
            </div>

            <div class="space-y-4 px-4 py-2">
                <template x-for="(card, index) in cards[columnId] || []" :key="card.uid">
                    <div>
                        <div
                            class="kanban-parent-card w-full cursor-pointer space-y-3 rounded-xl p-4"
                            x-bind:draggable="hasAction(moveAction)"
                            @dragstart="if (!hasAction(moveAction)) return; handleDragStart($event, card, columnId, index)"
                            @dragend="if (!hasAction(moveAction)) return; resetDragState()"
                            @dragover.prevent="if (!hasAction(moveAction)) return; updateDropIndex(columnId, index)"
                            @dragleave="if (!hasAction(moveAction)) return; dropIndex = null"
                            :class="{ 'dragging': isDragging && draggedCard?.uid === card.uid }"
                            x-bind:style="getParentCardStyle(card)"
                            @click="handleEdit(card.id)"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex h-2.5 w-2.5 rounded-full"
                                            x-bind:style="getAccentDotStyle(card)"
                                        ></span>
                                        <h5 class="text-sm font-semibold text-main-text" x-text="card.title"></h5>
                                    </div>
                                    {{-- <template x-if="card.is_parent">
                                        <p class="text-xs text-muted-text">Parent item</p>
                                    </template> --}}
                                </div>

                                <template x-if="hasAction(updateAction)">
                                    <mijnui:button
                                        type="button"
                                        color="secondary"
                                        variant="ghost"
                                        size="icon-sm"
                                        rounded="full"
                                        x-bind:style="getEntityActionStyle(card)"
                                        @click.stop="openCardEditor(columnId, `parent:${card.id}`)"
                                    >
                                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                            stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="12" cy="5" r="1"></circle>
                                            <circle cx="12" cy="19" r="1"></circle>
                                        </svg>
                                    </mijnui:button>
                                </template>
                            </div>

                            <template x-if="card.tags.length > 0">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="tag in card.tags" :key="`${card.uid}-tag-${tag}`">
                                        <span
                                            class="inline-flex items-center justify-center rounded-full border px-2.5 py-0.5 text-xs font-medium"
                                            x-bind:style="getTagStyle(card)"
                                            x-text="tag"
                                        ></span>
                                    </template>
                                </div>
                            </template>

                            <div class="space-y-1" x-show="card.showProgress">
                                <div class="flex items-center justify-between text-xs text-muted-text">
                                    <h5>Progress</h5>
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
                                    <div class="flex w-full items-center justify-end -space-x-2">
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
                                    </div>
                                </template>
                            </div>
                        </div>

                        <template x-if="card.has_children && card.children.length > 0">
                            <div class="ml-6 mt-2 space-y-2 border-l-2 border-muted pl-3">
                                <template x-for="child in card.children" :key="child.uid">
                                    <div
                                        class="kanban-child-card flex cursor-pointer items-center justify-between gap-3 rounded-xl p-3 text-xs"
                                        x-bind:style="getChildRowStyle(child)"
                                        @click.stop="handleEdit(child.id)"
                                    >
                                        <div class="flex min-w-0 items-center gap-2">
                                            <span
                                                class="inline-flex h-8 w-1 rounded-full"
                                                x-bind:style="getChildAccentStyle(child)"
                                            ></span>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-muted-text">↳</span>
                                                    <span class="truncate font-medium text-main-text" x-text="child.name"></span>
                                                    {{-- <span
                                                        class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[11px] font-semibold"
                                                        x-bind:style="getChildTypeStyle(child)"
                                                        x-text="child.type"
                                                    ></span> --}}
                                                </div>
                                                <template x-if="child.status_name">
                                                    <p class="mt-1 text-[11px] text-muted-text" x-text="child.status_name"></p>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-2" @click.stop>
                                            <template x-if="child.progress > 0">
                                                <span class="text-[11px] font-semibold" x-text="`${child.progress}%`"></span>
                                            </template>

                                            <template x-if="hasAction(updateAction)">
                                                <mijnui:button
                                                    type="button"
                                                    color="secondary"
                                                    variant="ghost"
                                                    size="icon-sm"
                                                    rounded="full"
                                                    x-bind:style="getEntityActionStyle(child)"
                                                    @click.stop="openCardEditor(columnId, `child:${child.id}`)"
                                                >
                                                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                                        stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="1"></circle>
                                                        <circle cx="12" cy="5" r="1"></circle>
                                                        <circle cx="12" cy="19" r="1"></circle>
                                                    </svg>
                                                </mijnui:button>
                                            </template>

                                            <template x-if="hasAction(childMoveAction)">
                                                <mijnui:button
                                                    type="button"
                                                    size="xs"
                                                    rounded="full"
                                                    color="secondary"
                                                    variant="outline"
                                                    class="text-[11px]"
                                                    x-bind:style="getChildMoveButtonStyle(child)"
                                                    @click.stop="openChildMove(child, columnId, card.id)"
                                                >
                                                    Move
                                                </mijnui:button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="relative flex items-center justify-between gap-4 px-4 py-2">
                    <template x-if="hasAction(createAction)">
                        <mijnui:button
                            type="button"
                            color="secondary"
                            variant="ghost"
                            justify="start"
                            class="gap-2 text-sm font-medium"
                            x-bind:style="getAddButtonStyle(columnId)"
                            @click="openCreateModal(columnId)"
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

    <template x-if="hasAction(updateAction)">
        <mijnui:modal x-model="cardEditorOpen">
            <mijnui:modal.content class="max-w-3xl">
                <mijnui:modal.header>
                    <p class="text-xs uppercase tracking-[0.2em] text-muted-text">Manage</p>
                    <h2 class="text-lg font-semibold text-main-text">Update Card Info</h2>
                </mijnui:modal.header>

                <mijnui:modal.body class="space-y-5">
                    <template x-if="getCardEditorTarget()">
                        <div class="grid gap-4 rounded-xl border border-main-border bg-muted p-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-start">
                            <div class="min-w-0 space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-text" x-text="getCardEditorTypeLabel()"></p>
                                <p class="text-base font-semibold text-main-text" x-text="getCardEditorTarget()?.item?.title || getCardEditorTarget()?.item?.name || ''"></p>
                                <template x-if="getCardEditorTarget()?.kind === 'child'">
                                    <p class="text-sm text-muted-text" x-text="`Parent: ${getCardEditorTarget().parentTitle}`"></p>
                                </template>
                            </div>

                            <div class="flex flex-wrap gap-2 md:justify-end">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                    x-bind:style="getToneBadgeStyle(editFormData.color || getCardEditorTarget()?.item?.color || columns[editingColumnId]?.color)"
                                    x-text="getCardEditorTypeLabel()"
                                ></span>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                    x-bind:style="getToneBadgeStyle(columns[editingColumnId]?.color)"
                                    x-text="`Section: ${columns[editingColumnId]?.name || ''}`"
                                ></span>
                            </div>
                        </div>
                    </template>

                    @if ($hasCustomEditForm)
                        <div class="w-full space-y-4">
                            {{ $editForm }}
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-main-border bg-muted/60 p-4 text-sm text-muted-text">
                            Pass an <code>x-slot:editForm</code> from your Blade view to render the editor form.
                        </div>
                    @endif

                    <template x-if="editingCardError">
                        <p class="text-sm text-red-600" x-text="editingCardError"></p>
                    </template>
                </mijnui:modal.body>

                <mijnui:modal.footer>
                    <mijnui:button
                        color="secondary"
                        variant="subtle"
                        type="button"
                        @click="cardEditorOpen = false"
                    >
                        Cancel
                    </mijnui:button>

                    <mijnui:button
                        type="button"
                        x-bind:style="getModalPrimaryStyle(editingColumnId)"
                        x-bind:disabled="!editingCardSelection || !hasCustomEditForm"
                        @click="const updated = await saveCardUpdate(); if (updated) { cardEditorOpen = false; }"
                    >
                        Save
                    </mijnui:button>
                </mijnui:modal.footer>
            </mijnui:modal.content>
        </mijnui:modal>
    </template>

    <template x-if="hasAction(childMoveAction)">
        <mijnui:modal x-model="childMoveOpen">
            <mijnui:modal.content class="max-w-xl">
                <mijnui:modal.header>
                    <p class="text-xs uppercase tracking-[0.2em] text-muted-text">Reassign</p>
                    <h2 class="text-lg font-semibold text-main-text">Move Child Item</h2>
                </mijnui:modal.header>

                <mijnui:modal.body class="space-y-4">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-text">Child</p>
                        <p class="text-sm font-medium text-main-text" x-text="movingChild?.name || ''"></p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-main-text">Placement</label>
                        <mijnui:select
                            x-model="childMoveMode"
                            placeholder="Choose placement"
                            class="w-full min-w-0"
                        >
                            <mijnui:select.option value="child">Keep as child</mijnui:select.option>
                            <mijnui:select.option value="parent">Promote to parent</mijnui:select.option>
                        </mijnui:select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-main-text">Section</label>
                        <mijnui:select
                            x-model="childMoveTargetColumn"
                            placeholder="Choose section"
                            class="w-full min-w-0"
                        >
                            @foreach ($normalizedColumns as $targetColumnId => $targetColumn)
                                <mijnui:select.option value="{{ $targetColumnId }}">
                                    {{ $targetColumn['name'] }}
                                </mijnui:select.option>
                            @endforeach
                        </mijnui:select>
                    </div>

                    <div class="space-y-2" x-show="childMoveMode === 'child'">
                        <label class="text-sm font-medium text-main-text">Parent card</label>
                        <mijnui:select
                            x-model="childMoveTargetParentId"
                            placeholder="Select parent"
                            searchable
                            clearable
                            class="w-full min-w-0"
                        >
                            @foreach ($normalizedCards as $targetColumnId => $targetCards)
                                @foreach ($targetCards as $targetCard)
                                    <mijnui:select.option
                                        value="{{ $targetCard['id'] }}"
                                        x-show="childMoveTargetColumn === @js((string) $targetColumnId)"
                                    >
                                        {{ $targetCard['title'] }}
                                    </mijnui:select.option>
                                @endforeach
                            @endforeach
                        </mijnui:select>
                        <template x-if="getChildMoveParentOptions().length === 0">
                            <p class="text-sm text-muted-text">No parent cards are available in the selected section.</p>
                        </template>
                    </div>

                    <template x-if="childMoveError">
                        <p class="text-sm text-red-600" x-text="childMoveError"></p>
                    </template>
                </mijnui:modal.body>

                <mijnui:modal.footer>
                    <mijnui:button
                        color="secondary"
                        variant="subtle"
                        type="button"
                        @click="childMoveOpen = false"
                    >
                        Cancel
                    </mijnui:button>

                    <mijnui:button
                        type="button"
                        x-bind:style="getModalPrimaryStyle(childMoveTargetColumn)"
                        x-bind:disabled="childMoveMode === 'child' && !childMoveTargetParentId"
                        @click="const moved = await submitChildMove(); if (moved) { childMoveOpen = false; }"
                    >
                        Save
                    </mijnui:button>
                </mijnui:modal.footer>
            </mijnui:modal.content>
        </mijnui:modal>
    </template>

    <template x-if="hasAction(createAction)">
        <mijnui:modal x-model="createModalOpen">
            <mijnui:modal.content class="max-w-xl">
                <mijnui:modal.header>
                    <p class="text-xs uppercase tracking-[0.2em] text-muted-text">Create</p>
                    <h2 class="text-lg font-semibold text-main-text">Add New</h2>
                </mijnui:modal.header>

                <mijnui:modal.body class="space-y-4">
                    @if ($hasCustomCreateForm)
                        <div class="w-full space-y-4">
                            {{ $createForm }}
                        </div>
                    @else
                        <mijnui:input
                            x-model="createFormData.title"
                            x-bind:data-kanban-new-card-input="newCardColumn"
                            @input="errorMessage = ''"
                            @keydown.enter="const created = await addNewCard(); if (created) createModalOpen = false;"
                            placeholder="Enter work item title..."
                            wrapper-class="w-full"
                        />
                    @endif

                    <template x-if="errorMessage">
                        <p class="text-sm text-red-600" x-text="errorMessage"></p>
                    </template>
                </mijnui:modal.body>

                <mijnui:modal.footer>
                    <mijnui:button
                        color="secondary"
                        variant="subtle"
                        type="button"
                        @click="createModalOpen = false"
                    >
                        Cancel
                    </mijnui:button>

                    <mijnui:button
                        type="button"
                        x-bind:style="getModalPrimaryStyle(newCardColumn)"
                        @click="const created = await addNewCard(); if (created) createModalOpen = false;"
                    >
                        Add
                    </mijnui:button>
                </mijnui:modal.footer>
            </mijnui:modal.content>
        </mijnui:modal>
    </template>
</div>

<script>
    function kanbanBoard(config) {
        return {
            columns: config.columns || {},
            cards: config.cards || {},
            widthStyle: config.widthStyle || '',
            palette: config.palette || {},
            createAction: config.createAction ?? null,
            updateAction: config.updateAction ?? null,
            editAction: config.editAction ?? null,
            moveAction: config.moveAction ?? null,
            childMoveAction: config.childMoveAction ?? null,
            defaultCreatePayload: config.defaultCreatePayload || {},
            defaultCreateForm: config.defaultCreateForm || {},
            defaultEditForm: config.defaultEditForm || {},
            hasCustomCreateForm: Boolean(config.hasCustomCreateForm),
            hasCustomEditForm: Boolean(config.hasCustomEditForm),
            draggedCard: null,
            sourceColumn: null,
            sourceIndex: null,
            isDragging: false,
            iconCache: {},
            dropIndex: null,
            dropColumn: null,
            createModalOpen: false,
            createFormData: {},
            newCardColumn: null,
            newCardMeta: {},
            errorMessage: '',
            childMoveOpen: false,
            movingChild: null,
            movingChildSourceColumn: null,
            movingChildSourceParentId: null,
            childMoveMode: 'child',
            childMoveTargetColumn: null,
            childMoveTargetParentId: null,
            childMoveError: '',
            cardEditorOpen: false,
            editingColumnId: null,
            editingCardSelection: null,
            editFormData: {},
            editingCardError: '',

            init() {
                const hydratedColumns = {};
                const hydratedCards = {};

                Object.entries(this.columns).forEach(([columnId, column]) => {
                    hydratedColumns[columnId] = this.hydrateColumn(columnId, column);
                    hydratedCards[columnId] = (this.cards[columnId] || []).map((card, index) => this.hydrateCard(card, columnId, index));
                });

                this.columns = hydratedColumns;
                this.cards = hydratedCards;
                this.$watch('childMoveMode', () => this.syncChildMoveTargetParent());
                this.$watch('childMoveTargetColumn', () => this.syncChildMoveTargetParent());
                this.$watch('editingCardSelection', () => this.syncCardEditorForm());
                this.$watch('createModalOpen', (value) => {
                    if (!value) {
                        this.resetNewCardState();
                    }
                });
                this.$watch('childMoveOpen', (value) => {
                    if (!value) {
                        this.resetChildMoveState();
                    }
                });
                this.$watch('cardEditorOpen', (value) => {
                    if (!value) {
                        this.resetCardEditorState();
                    }
                });
            },

            headline(value) {
                return String(value || '')
                    .replace(/[_-]+/g, ' ')
                    .trim()
                    .replace(/\b\w/g, (character) => character.toUpperCase());
            },

            normalizeColor(color) {
                const normalized = typeof color === 'string' ? color.toLowerCase() : 'secondary';

                return Object.prototype.hasOwnProperty.call(this.palette, normalized) ? normalized : 'secondary';
            },

            normalizeProgress(value) {
                const numericValue = Number(value);
                const progress = Number.isFinite(numericValue) ? numericValue : 0;

                return Math.max(0, Math.min(100, Math.round(progress)));
            },

            buildToneVars(color) {
                const tones = this.palette[this.normalizeColor(color)] || this.palette.secondary || {};

                return [
                    `--kanban-accent: ${tones.accent || 'hsl(var(--secondary-foreground))'}`,
                    `--kanban-subtle: ${tones.subtle || 'hsl(var(--secondary))'}`,
                    `--kanban-foreground: ${tones.foreground || 'hsl(var(--secondary-foreground))'}`,
                    `--kanban-border: ${tones.border || 'hsl(var(--border-secondary))'}`,
                    `--kanban-contrast: ${tones.contrast || 'hsl(var(--background))'}`,
                ].join('; ');
            },

            composeStyle(...segments) {
                return segments.filter(Boolean).join('; ');
            },

            hasAction(action) {
                return typeof action === 'string' && action.trim().length > 0;
            },

            hydrateColumn(columnId, column) {
                const color = this.normalizeColor(column.color || 'secondary');

                return {
                    ...column,
                    id: column.id || columnId,
                    name: column.name || this.headline(columnId),
                    color,
                    createPayload: column.createPayload && typeof column.createPayload === 'object' ? { ...column.createPayload } : {},
                    createForm: column.createForm && typeof column.createForm === 'object' ? { ...column.createForm } : {},
                    editForm: column.editForm && typeof column.editForm === 'object' ? { ...column.editForm } : {},
                    styles: {
                        vars: this.buildToneVars(color),
                    },
                };
            },

            hydrateChild(child, fallbackColor, childKey) {
                const color = this.normalizeColor(child.color || fallbackColor);

                return {
                    ...child,
                    uid: child.uid || childKey,
                    name: child.name || child.title || 'Untitled child',
                    type: child.type || 'Child',
                    status_name: child.status_name || '',
                    progress: this.normalizeProgress(child.progress),
                    color,
                    styles: {
                        vars: this.buildToneVars(color),
                    },
                };
            },

            hydrateCard(card, columnId, cardIndex = 0) {
                const color = this.normalizeColor(card.color || this.columns[columnId]?.color || 'secondary');
                const baseId = card.id || `${columnId}-${cardIndex}`;
                const children = Array.isArray(card.children)
                    ? card.children.map((child, childIndex) => this.hydrateChild(child, color, `${columnId}-card-${baseId}-child-${child.id || childIndex}`))
                    : [];

                return {
                    ...card,
                    id: baseId,
                    uid: card.uid || `${columnId}-card-${baseId}`,
                    title: card.title || card.name || 'Untitled',
                    tags: Array.isArray(card.tags) ? card.tags : [],
                    items: Array.isArray(card.items) ? card.items : [],
                    avatars: Array.isArray(card.avatars) ? card.avatars : [],
                    progress: this.normalizeProgress(card.progress),
                    showProgress: Boolean(card.showProgress ?? this.normalizeProgress(card.progress) > 0),
                    is_parent: Boolean(card.is_parent ?? children.length > 0),
                    children,
                    has_children: children.length > 0,
                    meta: card.meta && typeof card.meta === 'object' ? { ...card.meta } : {},
                    color,
                    styles: {
                        vars: this.buildToneVars(color),
                    },
                };
            },

            getColumnStyle(columnId) {
                return this.composeStyle(this.widthStyle, this.columns[columnId]?.styles?.vars);
            },

            getCountBadgeStyle(columnId) {
                return this.composeStyle(
                    this.columns[columnId]?.styles?.vars,
                    'background-color: var(--kanban-subtle); color: var(--kanban-foreground); border: 1px solid var(--kanban-border);'
                );
            },

            getParentCardStyle(card) {
                return this.composeStyle(
                    card.styles?.vars,
                    'border: 1px solid var(--kanban-border); background: linear-gradient(180deg, var(--kanban-subtle) 0%, hsl(var(--background)) 28%);'
                );
            },

            getTagStyle(card) {
                return this.composeStyle(
                    card.styles?.vars,
                    'border-color: var(--kanban-border); background-color: var(--kanban-subtle); color: var(--kanban-foreground);'
                );
            },

            getProgressFillStyle(card) {
                return this.composeStyle(
                    card.styles?.vars,
                    `width: ${this.normalizeProgress(card.progress)}%; background-color: var(--kanban-accent);`
                );
            },

            getChildRowStyle(child) {
                return this.composeStyle(
                    child.styles?.vars,
                    'border: 1px solid var(--kanban-border); background-color: var(--kanban-subtle); color: var(--kanban-foreground);'
                );
            },

            getChildTypeStyle(child) {
                return this.composeStyle(
                    child.styles?.vars,
                    'border: 1px solid var(--kanban-border); background-color: var(--kanban-contrast); color: var(--kanban-foreground);'
                );
            },

            getChildMoveButtonStyle(child) {
                return this.composeStyle(
                    child.styles?.vars,
                    'border-color: var(--kanban-border); background-color: var(--kanban-contrast); color: var(--kanban-foreground);'
                );
            },

            getChildAccentStyle(child) {
                return this.composeStyle(child.styles?.vars, 'background-color: var(--kanban-accent);');
            },

            getAccentDotStyle(entity) {
                return this.composeStyle(entity?.styles?.vars, 'background-color: var(--kanban-accent);');
            },

            getEntityActionStyle(entity) {
                return this.composeStyle(entity?.styles?.vars, 'color: var(--kanban-foreground);');
            },

            getToneBadgeStyle(color) {
                return this.composeStyle(
                    this.buildToneVars(color),
                    'background-color: var(--kanban-subtle); color: var(--kanban-foreground); border: 1px solid var(--kanban-border);'
                );
            },

            getAddButtonStyle(columnId) {
                return this.composeStyle(this.columns[columnId]?.styles?.vars, 'color: var(--kanban-foreground);');
            },

            getModalPrimaryStyle(columnId = null) {
                const activeColumnId = columnId || this.newCardColumn || this.childMoveTargetColumn;
                const column = this.columns[activeColumnId] || {};

                return this.composeStyle(
                    column.styles?.vars,
                    'background-color: var(--kanban-accent); color: var(--kanban-contrast);'
                );
            },

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

            async callWireAction(action, ...params) {
                if (!this.$wire?.call || !this.hasAction(action)) {
                    return null;
                }

                return this.$wire.call(action, ...params);
            },

            handleEdit(cardId) {
                if (!this.hasAction(this.editAction)) {
                    return;
                }

                void this.callWireAction(this.editAction, cardId);
            },

            findParentCard(columnId, cardId) {
                return (this.cards[columnId] || []).find((card) => String(card.id) === String(cardId)) || null;
            },

            findCardEditorTarget(columnId = this.editingColumnId, selection = this.editingCardSelection) {
                if (!columnId || !selection) {
                    return null;
                }

                const [kind, rawId] = String(selection).split(':');

                if (!kind || !rawId) {
                    return null;
                }

                if (kind === 'parent') {
                    const parent = this.findParentCard(columnId, rawId);

                    return parent
                        ? {
                            kind: 'parent',
                            columnId,
                            parentId: null,
                            parentTitle: null,
                            item: parent,
                        }
                        : null;
                }

                for (const parent of this.cards[columnId] || []) {
                    const child = (parent.children || []).find((entry) => String(entry.id) === String(rawId));

                    if (child) {
                        return {
                            kind: 'child',
                            columnId,
                            parentId: parent.id,
                            parentTitle: parent.title,
                            item: child,
                        };
                    }
                }

                return null;
            },

            getCardEditorTarget() {
                return this.findCardEditorTarget();
            },

            getCardEditorTypeLabel() {
                return this.getCardEditorTarget()?.kind === 'child' ? 'Child card' : 'Parent card';
            },

            buildEditFormData(target) {
                if (!target) {
                    return {};
                }

                const title = target.kind === 'child' ? target.item.name : target.item.title;
                const color = this.normalizeColor(target.item.color || this.columns[target.columnId]?.color || 'secondary');

                return {
                    ...(this.defaultEditForm || {}),
                    ...((this.columns[target.columnId]?.editForm || {})),
                    ...(target.item?.meta && typeof target.item.meta === 'object' ? target.item.meta : {}),
                    title,
                    name: title,
                    progress: this.normalizeProgress(target.item.progress),
                    color,
                    type: target.kind,
                    columnId: target.columnId,
                    parentId: target.parentId,
                    parentTitle: target.parentTitle,
                };
            },

            cloneEditFormData() {
                return this.editFormData && typeof this.editFormData === 'object'
                    ? JSON.parse(JSON.stringify(this.editFormData))
                    : {};
            },

            getEditFormTitle(formData = this.editFormData) {
                if (!formData || typeof formData !== 'object') {
                    return '';
                }

                const title = typeof formData.title === 'string'
                    ? formData.title
                    : (typeof formData.name === 'string' ? formData.name : '');

                return title.trim();
            },

            getEditFormProgress(formData = this.editFormData) {
                if (!formData || typeof formData !== 'object' || formData.progress === undefined || formData.progress === null || formData.progress === '') {
                    return null;
                }

                return this.normalizeProgress(formData.progress);
            },

            getEditFormColor(formData = this.editFormData) {
                if (!formData || typeof formData !== 'object' || !formData.color) {
                    return null;
                }

                return this.normalizeColor(formData.color);
            },

            syncCardEditorForm() {
                const target = this.getCardEditorTarget();

                if (!target) {
                    this.editFormData = {};
                    return;
                }

                this.editFormData = this.buildEditFormData(target);
                this.editingCardError = '';
            },

            prepareCardEditor(columnId, selection) {
                this.editingColumnId = columnId;
                this.editingCardSelection = selection || null;
                this.editFormData = {};
                this.editingCardError = '';
                this.syncCardEditorForm();

                this.$nextTick(() => {
                    const selectors = [
                        `[data-kanban-edit-autofocus="${columnId}"] input`,
                        `input[data-kanban-edit-autofocus="${columnId}"]`,
                        `[data-kanban-edit-autofocus] input`,
                        `input[data-kanban-edit-autofocus]`,
                    ];

                    document.querySelector(selectors.join(', '))?.focus();
                });
            },

            openCardEditor(columnId, selection) {
                this.prepareCardEditor(columnId, selection);
                this.cardEditorOpen = true;
            },

            resetCardEditorState() {
                this.editingColumnId = null;
                this.editingCardSelection = null;
                this.editFormData = {};
                this.editingCardError = '';
            },

            syncParentChildItem(card) {
                const childCount = Array.isArray(card.children) ? card.children.length : 0;
                const currentItems = Array.isArray(card.items) ? [...card.items] : [];
                const childCountIndex = currentItems.findIndex((item) => item.icon === 'queue-list');

                if (childCount > 0 && childCountIndex === -1) {
                    currentItems.push({
                        icon: 'queue-list',
                        text: childCount,
                    });
                }

                if (childCount > 0 && childCountIndex !== -1) {
                    currentItems[childCountIndex] = {
                        ...currentItems[childCountIndex],
                        text: childCount,
                    };
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

            getChildMoveParentOptions() {
                if (!this.childMoveTargetColumn) {
                    return [];
                }

                return (this.cards[this.childMoveTargetColumn] || []).map((card) => ({
                    id: String(card.id),
                    title: card.title,
                }));
            },

            syncChildMoveTargetParent() {
                if (this.childMoveMode !== 'child') {
                    this.childMoveTargetParentId = null;
                    return;
                }

                const options = this.getChildMoveParentOptions();

                if (options.length === 0) {
                    this.childMoveTargetParentId = null;
                    return;
                }

                if (!options.some((parent) => String(parent.id) === String(this.childMoveTargetParentId))) {
                    this.childMoveTargetParentId = String(options[0].id);
                }
            },

            prepareChildMove(child, columnId, parentCardId) {
                this.movingChild = JSON.parse(JSON.stringify(child));
                this.movingChildSourceColumn = columnId;
                this.movingChildSourceParentId = parentCardId;
                this.childMoveMode = 'child';
                this.childMoveTargetColumn = columnId;
                this.childMoveTargetParentId = String(parentCardId);
                this.childMoveError = '';
                this.syncChildMoveTargetParent();
            },

            openChildMove(child, columnId, parentCardId) {
                this.prepareChildMove(child, columnId, parentCardId);
                this.childMoveOpen = true;
            },

            resetChildMoveState() {
                this.movingChild = null;
                this.movingChildSourceColumn = null;
                this.movingChildSourceParentId = null;
                this.childMoveMode = 'child';
                this.childMoveTargetColumn = null;
                this.childMoveTargetParentId = null;
                this.childMoveError = '';
            },

            applyChildMoveResult(result) {
                const removed = this.removeChildFromLocalState(this.movingChild?.id);
                const targetColumnId = result.columnId || this.childMoveTargetColumn;

                if (!removed || !targetColumnId) {
                    return;
                }

                if (result.mode === 'parent') {
                    if (!Array.isArray(this.cards[targetColumnId])) {
                        this.cards[targetColumnId] = [];
                    }

                    this.cards[targetColumnId].push(
                        this.hydrateCard(result.item || {
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

                const targetParentId = result.targetParentId || this.childMoveTargetParentId;
                const targetParent = this.findParentCard(targetColumnId, targetParentId);

                if (!targetParent) {
                    return;
                }

                if (!Array.isArray(targetParent.children)) {
                    targetParent.children = [];
                }

                targetParent.children.push(
                    this.hydrateChild(
                        result.item || {
                            ...removed.child,
                            name: removed.child.name,
                            color: removed.child.color || targetParent.color,
                        },
                        targetParent.color,
                        `${targetColumnId}-card-${targetParent.id}-child-${result.item?.id || removed.child.id}`
                    )
                );

                this.syncParentChildItem(targetParent);
            },

            applyCardUpdateResult(result) {
                const target = this.getCardEditorTarget();
                const columnId = result.columnId || target?.columnId || this.editingColumnId;

                if (!target || !columnId || !result?.item) {
                    return;
                }

                if (result.mode === 'parent') {
                    const cards = this.cards[columnId] || [];
                    const cardIndex = cards.findIndex((card) => String(card.id) === String(result.item.id));

                    if (cardIndex === -1) {
                        return;
                    }

                    cards.splice(cardIndex, 1, this.hydrateCard(result.item, columnId, cardIndex));
                    return;
                }

                const parent = this.findParentCard(columnId, result.parentId || target.parentId);

                if (!parent) {
                    return;
                }

                const childIndex = (parent.children || []).findIndex((child) => String(child.id) === String(result.item.id));

                if (childIndex === -1) {
                    return;
                }

                parent.children.splice(
                    childIndex,
                    1,
                    this.hydrateChild(result.item, parent.color, `${columnId}-card-${parent.id}-child-${result.item.id}`)
                );
                this.syncParentChildItem(parent);
            },

            async saveCardUpdate() {
                const target = this.getCardEditorTarget();
                const form = this.cloneEditFormData();
                const resolvedTitle = this.getEditFormTitle(form);

                if (!target) {
                    this.editingCardError = 'Choose a card to update.';
                    return false;
                }

                if (!this.hasAction(this.updateAction)) {
                    this.editingCardError = 'No update action is configured.';
                    return false;
                }

                if (!this.hasCustomEditForm) {
                    this.editingCardError = 'Provide an editForm slot to save changes.';
                    return false;
                }

                this.editingCardError = '';

                try {
                    const result = await this.callWireAction(this.updateAction, {
                        cardId: Number(target.item.id),
                        columnId: this.editingColumnId,
                        meta: target.item?.meta || {},
                        title: resolvedTitle || null,
                        progress: this.getEditFormProgress(form),
                        color: this.getEditFormColor(form),
                        form,
                    });

                    if (!result || typeof result !== 'object') {
                        throw new Error('Update action must return an updated placement payload.');
                    }

                    this.applyCardUpdateResult(result);
                    return true;
                } catch (error) {
                    this.editingCardError = 'Failed to update card info';
                    console.error(error);
                    return false;
                }
            },

            async submitChildMove() {
                if (!this.movingChild) {
                    return false;
                }

                if (!this.hasAction(this.childMoveAction)) {
                    this.childMoveError = 'No child move action is configured.';
                    return false;
                }

                if (this.childMoveMode === 'child' && !this.childMoveTargetParentId) {
                    this.childMoveError = 'Choose a target parent.';
                    return false;
                }

                this.childMoveError = '';

                try {
                    const result = await this.callWireAction(this.childMoveAction, {
                        childId: this.movingChild.id,
                        targetColumnId: this.childMoveTargetColumn,
                        targetParentId: this.childMoveMode === 'child'
                            ? Number(this.childMoveTargetParentId)
                            : null,
                    });

                    if (!result || typeof result !== 'object') {
                        throw new Error('Child move action must return a placement payload.');
                    }

                    this.applyChildMoveResult(result);
                    return true;
                } catch (error) {
                    this.childMoveError = 'Failed to move child item';
                    console.error(error);
                    return false;
                }
            },

            handleDragStart(event, card, columnId, index) {
                this.isDragging = true;
                this.draggedCard = JSON.parse(JSON.stringify(card));
                this.sourceColumn = columnId;
                this.sourceIndex = index;
                event.dataTransfer.effectAllowed = 'move';
            },

            resetDragState() {
                this.draggedCard = null;
                this.sourceColumn = null;
                this.sourceIndex = null;
                this.isDragging = false;
                this.dropIndex = null;
                this.dropColumn = null;
            },

            async handleDrop(event, targetColumnId) {
                if (!this.hasAction(this.moveAction)) {
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

                try {
                    await this.callWireAction(this.moveAction, {
                        cardId: draggedCard.id,
                        fromColumnId: this.sourceColumn,
                        toColumnId: targetColumnId,
                        dropIndex: insertIndex,
                    });
                } catch (error) {
                    console.error(error);
                }

                this.resetDragState();
            },

            updateDropIndex(columnId, index) {
                this.dropColumn = columnId;
                this.dropIndex = index;
            },

            cloneCreateFormData() {
                return this.createFormData && typeof this.createFormData === 'object'
                    ? JSON.parse(JSON.stringify(this.createFormData))
                    : {};
            },

            getCreateFormTitle(formData = this.createFormData) {
                if (!formData || typeof formData !== 'object') {
                    return '';
                }

                const title = typeof formData.title === 'string'
                    ? formData.title
                    : (typeof formData.name === 'string' ? formData.name : '');

                return title.trim();
            },

            prepareNewCard(columnId) {
                this.newCardColumn = columnId;
                this.createFormData = {
                    ...(this.defaultCreateForm || {}),
                    ...((this.columns[columnId]?.createForm || {})),
                };
                this.newCardMeta = {
                    ...(this.defaultCreatePayload || {}),
                    ...((this.columns[columnId]?.createPayload || {})),
                };
                this.errorMessage = '';

                this.$nextTick(() => {
                    const selectors = [
                        `[data-kanban-create-autofocus="${columnId}"] input`,
                        `input[data-kanban-create-autofocus="${columnId}"]`,
                        `[data-kanban-create-autofocus] input`,
                        `input[data-kanban-create-autofocus]`,
                        `[data-kanban-new-card-input="${columnId}"] input`,
                        `input[data-kanban-new-card-input="${columnId}"]`,
                    ];

                    document.querySelector(selectors.join(', '))?.focus();
                });
            },

            openCreateModal(columnId) {
                this.prepareNewCard(columnId);
                this.createModalOpen = true;
            },

            resetNewCardState() {
                this.createFormData = {};
                this.newCardColumn = null;
                this.newCardMeta = {};
                this.errorMessage = '';
            },

            async addNewCard() {
                if (!this.hasAction(this.createAction)) {
                    this.errorMessage = 'No create action is configured.';
                    return false;
                }

                const form = this.cloneCreateFormData();
                const resolvedTitle = this.getCreateFormTitle(form);

                if (!this.hasCustomCreateForm && !resolvedTitle) {
                    this.errorMessage = 'Title is required.';
                    return false;
                }

                try {
                    const createdCard = await this.callWireAction(this.createAction, {
                        title: resolvedTitle || null,
                        columnId: this.newCardColumn,
                        meta: this.newCardMeta,
                        form,
                    });

                    if (!createdCard || typeof createdCard !== 'object') {
                        throw new Error('Create action must return a card payload.');
                    }

                    if (!Array.isArray(this.cards[this.newCardColumn])) {
                        this.cards[this.newCardColumn] = [];
                    }

                    this.cards[this.newCardColumn].push(
                        this.hydrateCard(createdCard, this.newCardColumn, this.cards[this.newCardColumn].length)
                    );

                    this.resetNewCardState();
                    return true;
                } catch (error) {
                    this.errorMessage = 'Failed to create work item';
                    console.error(error);
                    return false;
                }
            },
        };
    }
</script>
