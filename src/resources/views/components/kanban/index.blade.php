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
        background-color: rgba(209, 213, 219, 0.3);
    }
    .dragging {
        opacity: 0.5;
        transform: scale(0.98);
    }
    .drop-zone-active {
        background-color: rgba(209, 213, 219, 0.3);
        border: 2px dashed #3b82f6;
    }
</style>

@props([
    'width' => null,
    'maxWidth' => '352px',
    'columns' => [],
])

@php
    // Handle width/maxWidth
    $widthStyle = '';
    if ($width) {
        $widthValue = is_numeric($width) ? "{$width}px" : $width;
        $widthStyle = "width: {$widthValue}";
    } else {
        $maxWidthValue = is_numeric($maxWidth) ? "{$maxWidth}px" : $maxWidth;
        $widthStyle = "max-width: {$maxWidthValue}; width: 100%;";
    }

    $containerClasses = 'relative overflow-auto rounded-2xl bg-muted py-2';

    $normalizedColumns = [];

    foreach ($columns as $columnKey => $cards) {
        $normalizedColumns[$columnKey] = array_map(function ($card) use ($columnKey) {
            $normalizedKey = str_replace(' ', '_', strtolower($columnKey));
            $card['uid'] = $normalizedKey . '-' . $card['id'];
            $card['table'] = $normalizedKey;
            return $card;
        }, $cards);
    }
@endphp

<div
    x-data="kanbanBoard(@js($normalizedColumns))"
    x-init="init()"
    class="w-full flex justify-center items-start gap-5"
>
    <template x-for="(column, columnId) in columns" :key="columnId">
        <div
            {{ $attributes->merge(['class' => $containerClasses]) }}
            style="{{ $widthStyle }}"
            @dragover.prevent="
                $event.dataTransfer.dropEffect = 'move';
                $el.classList.add('drop-zone-active')"
            @dragleave="$el.classList.remove('drop-zone-active')"
            @drop.prevent="
                handleDrop($event, columnId);
                $el.classList.remove('drop-zone-active');
            "
        >
            <!-- Column Header -->
            <div class="flex w-full items-center justify-between px-3 py-2">
                <div class="flex items-center gap-2">
                    <h3 class="font-medium text-main-text sm:text-lg" x-text="column.name"></h3>
                    <span
                        class="flex h-5 w-5 items-center justify-center rounded-full bg-surface text-xs font-medium text-muted-text"
                        x-text="cards[columnId]?.length || 0"></span>
                </div>
                <button
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

                            <!-- Progress Section -->
                            <div class="space-y-1" x-show="card.showProgress">
                                <div class="flex items-center justify-between text-xs text-muted-text">
                                    <h5>Progress</h5>
                                    <p x-text="card.progress + '%'"></p>
                                </div>
                                <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        class="h-full bg-primary transition-all"
                                        aria-valuemin="0" aria-valuemax="100"
                                        :aria-valuenow="card.progress" role="progressbar"
                                        :style="`width: ${card.progress}%`">
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="flex items-center gap-2 text-muted-text sm:gap-4">
                                <!-- Left-aligned items -->
                                <template x-if="card.items && card.items.length > 0">
                                    <template x-for="item in card.items" :key="item.text">
                                        <div class="flex items-center gap-1">
                                            <template x-if="item.icon">
                                                <span class="w-5 h-5" x-html="getIcon(item.icon)"></span>
                                            </template>
                                            <span class="text-xs" x-text="item.text || ''"></span>
                                        </div>
                                    </template>
                                </template>

                                <!-- Right-aligned avatars -->
                                <template x-if="card.avatars && card.avatars.length > 0">
                                    <div class="flex w-full items-center justify-end -space-x-2">
                                        <template x-for="avatar in card.avatars">
                                            <div
                                                class="relative flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted text-xs ring-1 ring-muted-text/75">
                                                <img
                                                    x-show="avatar.image"
                                                    alt="avatar" class="h-full w-full object-cover" :src="avatar.image" />
                                                <span x-text="avatar.initials" x-show="!avatar.image && avatar.initials"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Children/Subtasks -->
                        <template x-if="card.has_children && card.children">
                            <div class="ml-6 mt-2 space-y-2 border-l-2 border-muted pl-3">
                                <template x-for="child in card.children" :key="child.id">
                                    <div
                                        class="text-xs p-2 rounded bg-surface/50 hover:bg-surface cursor-pointer flex items-center justify-between"
                                        @click.stop="$wire.startEdit(child.id)"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="text-muted-text">↳</span>
                                            <span x-text="child.name"></span>
                                            <span
                                                class="px-1.5 py-0.5 rounded text-xs bg-muted"
                                                x-text="child.type"
                                            ></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-xs"
                                                x-text="child.progress + '%'"
                                                x-show="child.progress > 0"
                                            ></span>
                                            <span
                                                class="text-xs text-muted-text"
                                                x-text="child.status_name"
                                            ></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Add New Card Button -->
                <div class="relative flex items-center justify-between gap-4 px-4 py-2">
                    <button
                        class="flex items-center gap-2 text-sm text-muted-text hover:text-main-text"
                        @click="openNewCardModal(columnId)"
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
                    </button>
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
            class="bg-white dark:bg-[#262626] rounded-lg p-6 w-full max-w-md shadow-md"
        >
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Add New Work Item</h2>

            <input
                type="text"
                x-model="newCardTitle"
                @input="errorMessage = ''"
                @keydown.enter="addNewCard"
                x-ref="newCardInput"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-[#262626]"
                placeholder="Enter work item title..."
            />

            <template x-if="errorMessage">
                <p class="text-sm text-red-600 mt-1" x-text="errorMessage"></p>
            </template>

            <div class="flex justify-end gap-2 mt-4">
                <button
                    class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300"
                    @click="showModal = false"
                >
                    Cancel
                </button>
                <button
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                    @click="addNewCard"
                >
                    Add
                </button>
            </div>
        </div>
    </div>
</div>

<script>

    function kanbanBoard(initialData) {
console.log(initialData);
        return {
            columns: initialData || {},
            cards: initialData || {},
            draggedCard: null,
            sourceColumn: null,
            sourceIndex: null,
            isDragging: false,
            iconCache: {},

            // For drop position
            dropIndex: null,
            dropColumn: null,

            // Modal state
            showModal: false,
            newCardTitle: '',
            newCardColumn: null,
            errorMessage: '',

            init() {
                // Debug logging
                console.log('Kanban Board Initialized');
                console.log('Columns:', this.columns);
                console.log('Cards:', this.cards);

                // Ensure all columns have card arrays
                Object.keys(this.columns).forEach(columnId => {
                    if (!this.cards[columnId]) {
                        this.cards[columnId] = [];
                    }
                });

                console.log('Cards after init:', this.cards);
            },

            async getIcon(iconName) {
                if (!this.iconCache[iconName]) {
                    try {
                        const response = await fetch(`https://cdn.jsdelivr.net/npm/heroicons@2.2.0/24/outline/${iconName}.svg`);
                        this.iconCache[iconName] = await response.text();
                    } catch {
                        this.iconCache[iconName] = '<span class="text-red-500">?</span>'
                    }
                }
                return this.iconCache[iconName];
            },

            handleDragStart(event, card, columnId, index) {
                this.isDragging = true;
                this.draggedCard = JSON.parse(JSON.stringify(card));
                this.sourceColumn = columnId;
                this.sourceIndex = index;
                event.dataTransfer.effectAllowed = 'move';
            },

            handleDrop(event, targetColumnId) {
                if (!this.draggedCard || this.sourceColumn === targetColumnId) {
                    this.isDragging = false;
                    return;
                }

                // Call Livewire method to update database
                this.$wire.call('updateCardStatus', this.draggedCard.id, parseInt(targetColumnId));

                // Optimistically update UI
                this.cards[this.sourceColumn].splice(this.sourceIndex, 1);

                if (!this.cards[targetColumnId]) {
                    this.cards[targetColumnId] = [];
                }

                if (this.dropColumn === targetColumnId && this.dropIndex !== null) {
                    this.cards[targetColumnId].splice(this.dropIndex, 0, this.draggedCard);
                } else {
                    this.cards[targetColumnId].push(this.draggedCard);
                }

                // Reset
                this.draggedCard = null;
                this.sourceColumn = null;
                this.sourceIndex = null;
                this.isDragging = false;
                this.dropIndex = null;
                this.dropColumn = null;
            },

            updateDropIndex(columnId, index) {
                this.dropColumn = columnId;
                this.dropIndex = index;
            },

            openNewCardModal(columnId) {
                this.newCardColumn = columnId;
                this.newCardTitle = '';
                this.errorMessage = '';
                this.showModal = true;
                this.$nextTick(() => {
                    this.$refs.newCardInput?.focus();
                });
            },

            async addNewCard() {
                if (!this.newCardTitle.trim()) {
                    this.errorMessage = 'Title is required.';
                    return;
                }

                try {
                    // Call Livewire method to create in database
                    await this.$wire.call('createCardFromKanban', this.newCardTitle, parseInt(this.newCardColumn));

                    this.showModal = false;
                    this.newCardTitle = '';
                    this.newCardColumn = null;
                    this.errorMessage = '';
                } catch (error) {
                    this.errorMessage = 'Failed to create work item';
                    console.error(error);
                }
            }
        }
    }
</script>
