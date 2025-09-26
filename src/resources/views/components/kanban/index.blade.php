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
    // Width options
    'width' => null,
    'maxWidth' => '352px',
    'columns' => [],
    'name' => $attributes->whereStartsWith('wire:model')->first()
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
    x-data="kanbanBoard(@js($normalizedColumns), @entangle($name))" 
    x-init="init()"
    class="w-full flex justify-center items-start gap-5"
    >
    <template x-for="(columnCards, columnName) in columns" :key="columnName">
        <div 
            {{ $attributes->merge(['class' => $containerClasses]) }} 
            style="{{ $widthStyle }}"
            @dragover.prevent="
                $event.dataTransfer.dropEffect = 'move';
                $el.classList.add('drop-zone-active')"
            @dragleave="$el.classList.remove('drop-zone-active')"
            @drop.prevent="
                handleDrop($event, columnName);
                $el.classList.remove('drop-zone-active');    
            "
        >
            <div class="flex w-full items-center justify-between px-3 py-2">
                <div class="flex items-center gap-2">
                    <h3 class="font-medium text-main-text sm:text-lg" x-text="capitalizeWords(columnName)"></h3>
                    <span
                        class="flex h-5 w-5 items-center justify-center rounded-full bg-surface text-xs font-medium text-muted-text"
                        x-text="columnCards.length"></span>
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
            <div class="px-4 py-2">
                <template x-for="(card, index) in columnCards" :key="card.uid">
                    <div class="w-full cursor-pointer space-y-4 rounded-lg bg-surface p-4"
                        draggable="true"
                        @dragstart="handleDragStart($event, card, columnName, index)"
                        @dragend="isDragging = false"
                        @dragover.prevent="updateDropIndex(columnName, index)"
                        @dragleave="dropIndex = null"
                        :class="{
                            'mt-4': index > 0,
                            'dragging': isDragging && draggedCard?.id === card.id
                        }"
                    >
                        <h5 class="w-10/12 text-sm font-medium" x-text="card.title"></h5>
    
                        <template x-if="card.tags.length > 0">
                            <div class="flex flex-wrap">
                                <template x-for="tag in card.tags">
                                    <span
                                        class="inline-flex items-center justify-center rounded-full border px-2.5 py-0.5 text-xs hover:bg-accent" x-text="tag">
                                    </span>
                                </template>
                            </div>
                        </template>
    
                        <!-- Progress Section -->
                        <div class="space-y-1" x-show="card.showProgress">
                            <div class="flex items-center justify-between text-xs text-muted-text">
                                <h5>CheckList</h5>
                                <p x-text="card.progressText || `${card.progress}%`"></p>
                            </div>
                            <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted">
                                <div 
                                    class="h-full"
                                    :class="`bg-${card.progressColor || 'primary'}`"
                                    aria-valuemin="0" aria-valuemax="100"
                                    :aria-valuenow="card.progress" role="progressbar"
                                    :style="`transform: scaleX(${card.progress / 100}); transform-origin: left center`">
                                </div>
                            </div>
                        </div>
    
                        <!-- Kanban Card Footer -->
                        <div class="flex items-center gap-2 text-muted-text sm:gap-4">
                            <!-- Left-aligned items -->
                            <template x-if="card.items.length > 0">
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
                            <template x-if="card.avatars.length > 0">
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
                                    <div class="!ml-1.5 flex items-center justify-center text-xs text-muted-text" x-show="card.overflowCount > 0" x-text="`+${card.overflowCount}`"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <div class="relative flex items-center justify-between gap-4 px-4 py-2">
                    <button 
                        class="flex items-center gap-2 text-sm text-muted-text"
                        @click="openNewCardModal(columnName)"
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
    <div 
        x-show="showModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <!-- Modal Content -->
        <div 
            class="bg-white dark:bg-[#262626] rounded-lg p-6 w-full max-w-md shadow-md"      
            @click.away="showModal = false"
        >
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Add New Card</h2>
            
            <input
                type="text"
                x-model="newCardTitle"
                @input="errorMessage = ''"
                x-ref="newCardInput"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:foucs:ring-blue-950 dark:bg-[#262626]"
                placeholder="Enter title"
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
    function kanbanBoard(initialColumns, name) {
        return {
            columns: initialColumns,
            name: name,
            draggedCard: null,
            sourceColumn: null,
            sourceIndex: null,
            isDragging: false,
            iconCache: {},

            // For drop position
            dropIndex: null,
            dropColumn: null,

            // New state for model & new card input
            showModal: false,
            newCardTitle: '',
            newCardColumn: null,
            errorMessage: '',

            init() {
                this.name = JSON.parse(JSON.stringify(this.columns));
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

            handleDragStart(event, card, columnName, index) {
                this.isDragging = true;
                this.draggedCard = JSON.parse(JSON.stringify(card));
                this.sourceColumn = columnName;
                this.sourceIndex = index;
                event.dataTransfer.setData('text/plain', `${columnName}-${card.id}`);
                event.dataTransfer.effectAllowed = 'move';
            },

            handleDrop(event, targetColumnName) {
                if (!this.draggedCard || this.sourceColumn === targetColumnName) return;

                // Remove from source column
                this.columns[this.sourceColumn].splice(this.sourceIndex, 1);

                // Insert into target column at dropIndex or push at the end if dropindex is null or targetColumn changed
                if (this.dropColumn === targetColumnName && this.dropIndex !== null) {
                    this.columns[targetColumnName].splice(this.dropIndex, 0, this.draggedCard);
                } else {
                    this.columns[targetColumnName].push(this.draggedCard);
                }

                // Update Livewire
                this.name = JSON.parse(JSON.stringify(this.columns));

                // Reset
                this.draggedCard = null;
                this.sourceColumn = null;
                this.sourceIndex = null;
                this.isDragging = false;
                this.dropIndex = null;
                this.dropColumn = null;
            },

            updateDropIndex(columnName, index) {
                this.dropColumn = columnName;
                this.dropIndex = index;
            },

            openNewCardModal(columnName) {
                this.newCardColumn = columnName;
                this.newCardTitle = '';
                this.showModal = true;
                this.$nextTick(() => {
                    this.$refs.newCardInput?.focus();
                });
            },

            addNewCard() {
                // if (!this.newCardTitle.trim()) return;
                if (!this.newCardTitle.trim()) {
                    this.errorMessage = 'Title is required.';
                    return;
                }

                const newId = Date.now();
                const normalizedColumn = this.newCardColumn.toLowerCase().replace(/\s+/g, '_');
                const newCard = {
                    title: this.newCardTitle,
                    tags: [],
                    progress: null,
                    progressText: "",
                    showProgress: false,
                    items: [],
                    avatars: [],
                    overflowCount: 0,
                    uid: normalizedColumn + '-' + newId,
                }

                this.columns[this.newCardColumn].push(newCard);
                this.name = JSON.parse(JSON.stringify(this.columns));

                this.showModal = false;
                this.newCardTitle = '';
                this.newCardColumn = null;
                this.errorMessage = '';
            },

            capitalizeWords(str) {
                if (!str) return '';
                return str.replace(/_/g, ' ').split(' ').map(word => {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }).join(' ');
            }
        }
    }
</script>
