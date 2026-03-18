{{-- Partial: included by gantt/index.blade.php — receives $tasks, $scale, $header from parent --}}

@php
    use Carbon\Carbon;

    $allowedScales = ['day', 'week', 'month'];
    $scale = in_array($scale, $allowedScales) ? $scale : 'day';

    $allowedColors = ['primary', 'secondary', 'success', 'warning', 'danger', 'gray'];
    $normalizeColor = fn($color) => in_array($color, $allowedColors) ? $color : 'gray';

    $entries = [];
    foreach ($tasks as $taskIndex => $task) {
        $subtasks = $task['subtasks'] ?? [];
        $fallbackStart = collect($subtasks)->pluck('start')->filter()->map(fn($d) => Carbon::parse($d))->min()?->toDateTimeString();
        $fallbackEnd = collect($subtasks)->pluck('end')->filter()->map(fn($d) => Carbon::parse($d))->max()?->toDateTimeString();

        $entries[] = [
            '_parent' => $taskIndex,
            '_is_parent' => true,
            '_id' => 'parent-' . $taskIndex,
            '_task_id' => $task['id'] ?? null,
            'title' => $task['title'] ?? '',
            'text' => $task['text'] ?? null,
            'color' => $normalizeColor($task['color'] ?? null),
            'start' => ($task['start'] ?? $fallbackStart) ? Carbon::parse($task['start'] ?? $fallbackStart)->format('Y-m-d') : null,
            'end' => ($task['end'] ?? $fallbackEnd) ? Carbon::parse($task['end'] ?? $fallbackEnd)->format('Y-m-d') : null,
            'process' => $task['process'] ?? $task['progress'] ?? 0,
        ];

        foreach ($subtasks as $subtaskIndex => $subtask) {
            $entries[] = [
                '_parent' => $taskIndex,
                '_is_parent' => false,
                '_id' => 'sub-' . $taskIndex . '-' . $subtaskIndex,
                '_task_id' => $subtask['id'] ?? null,
                'title' => $subtask['title'] ?? '',
                'text' => $subtask['text'] ?? null,
                'color' => $normalizeColor($subtask['color'] ?? null),
                'start' => isset($subtask['start']) ? Carbon::parse($subtask['start'])->format('Y-m-d') : null,
                'end' => isset($subtask['end']) ? Carbon::parse($subtask['end'])->format('Y-m-d') : null,
                'process' => $subtask['process'] ?? $subtask['progress'] ?? 0,
            ];
        }
    }

    $allEntries = collect($entries);

    if ($allEntries->isEmpty()) {
        return;
    }

    $taskStart = $allEntries->pluck('start')->filter()->map(fn($d) => Carbon::parse($d))->min();
    $taskEnd = $allEntries->pluck('end')->filter()->map(fn($d) => Carbon::parse($d))->max();

    if (!$taskStart || !$taskEnd) {
        return;
    }

    $config = [
        'entries' => $allEntries->values()->toArray(),
        'scale' => $scale,
        'taskStartDate' => $taskStart->format('Y-m-d'),
        'taskEndDate' => $taskEnd->format('Y-m-d'),
        'today' => Carbon::today()->format('Y-m-d'),
    ];
@endphp

<div x-data="ganttChartComponent(@js($allEntries), @js($jsDates), @js($dayWidth))" x-ref="ganttComponent"
    @gantt-updated.window="$wire.call('handleGanttUpdate', $event.detail)"
    class="gantt-chart-colors h-[672px] w-full rounded-2xl bg-surface shadow-lg overflow-hidden">

    @once
        <style>
            .progress-fill {
                height: 100%;
                transition: width 0.2s ease;
                border-radius: 6px;
            }

            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .gantt-no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .gantt-bar {
                cursor: grab;
            }

            .gantt-bar:active {
                cursor: grabbing;
            }

            .gantt-bar:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .gantt-handle {
                opacity: 0;
                transition: opacity 0.15s ease;
            }

            .gantt-bar:hover .gantt-handle {
                opacity: 1;
            }

            .gantt-chart-colors {
                --color-primary: var(--primary);
                --color-secondary: var(--secondary-foreground);
                --color-success: var(--success);
                --color-warning: var(--warning);
                --color-danger: var(--danger);
                --color-gray: var(--muted-foreground);
            }
        </style>
    @endonce

    <div class="flex h-full w-full">
        {{-- ========== Sidebar ========== --}}
        <div class="w-64 shrink-0 border-r border-main-border bg-surface flex flex-col">
            {{-- Sidebar Header — must match timeline header height (h-10 + h-10 = 80px) --}}
            <div class="shrink-0 h-20 flex flex-col border-b border-main-border bg-accent">
                {{-- Scale Switcher (top row) --}}
                <div class="flex items-center gap-1 px-4 h-10 border-b border-main-border">
                    <template x-for="s in ['day', 'week', 'month']" :key="s">
                        <button @click="setScale(s)" type="button"
                            class="text-xs px-2.5 py-1 rounded-md transition-colors capitalize" :class="scale === s
                                    ? 'bg-primary text-white font-medium'
                                    : 'text-muted-text hover:text-main-text hover:bg-surface'">
                            <span x-text="s"></span>
                        </button>
                    </template>
                    {{-- Current date indicator (click to scroll to today) --}}
                    <button @click="_scrollTo(today)" type="button"
                        class="ml-auto text-xs font-medium text-primary hover:text-primary/80 transition-colors cursor-pointer">
                        <span x-text="todayLabel"></span>
                    </button>
                </div>
                {{-- Header title (bottom row) --}}
                <div class="flex items-center h-10 px-4">
                    @if($header)
                        <p class="text-sm font-semibold text-main-text truncate">{{ $header }}</p>
                    @endif
                </div>
            </div>

            {{-- Sidebar Body --}}
            <div x-ref="sidebarBody" class="flex-1 overflow-hidden gantt-no-scrollbar">
                @foreach($tasks as $taskIndex => $task)
                    {{-- Parent Row --}}
                    <button @click="toggleExpand({{ $taskIndex }})"
                        class="h-10 w-full flex items-center justify-between px-4 border-b border-main-border hover:bg-accent transition-colors"
                        type="button">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="h-3 w-3 rounded-full shrink-0"
                                style="background-color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})">
                            </div>
                            <span
                                class="text-sm {{ !empty($task['subtasks']) ? 'font-semibold' : 'font-medium' }} text-main-text truncate">
                                {{ $task['title'] }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(!empty($task['process']))
                                <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-accent"
                                    style="color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})">
                                    {{ $task['process'] }}%
                                </span>
                            @endif

                            @if(!empty($task['subtasks']))
                                <svg :class="expanded[{{ $taskIndex }}] ? 'rotate-180' : ''"
                                    class="h-4 w-4 transition-transform text-muted-text" stroke="currentColor" fill="none"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            @endif
                        </div>
                    </button>

                    {{-- Subtask Rows --}}
                    @if(!empty($task['subtasks']))
                        @foreach($task['subtasks'] as $subtask)
                            <div x-show="expanded[{{ $taskIndex }}]"
                                class="h-10 flex items-center gap-3 px-4 pl-10 border-b border-main-border hover:bg-accent/50 transition-colors">
                                <div class="h-2 w-2 rounded-full shrink-0"
                                    style="background-color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})">
                                </div>
                                <span class="text-sm text-main-text flex-1 min-w-0 truncate">{{ $subtask['title'] }}</span>
                                @if(!empty($subtask['process']))
                                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-accent"
                                        style="color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})">
                                        {{ $subtask['process'] }}%
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ========== Timeline ========== --}}
        <div x-ref="timeline" class="flex-1 overflow-auto gantt-no-scrollbar">
            {{-- Sticky Header (total h-20 = 80px to match sidebar) --}}
            <div class="sticky top-0 z-20" :style="`width: ${totalWidth}px`">
                {{-- Header Groups (Month/Year) --}}
                <div class="flex">
                    <template x-for="group in headerGroups" :key="group.key">
                        <div class="shrink-0 h-10 flex items-center justify-center border-r border-b border-main-border bg-accent px-2"
                            :style="`width: ${group.width}px`">
                            <span class="text-sm font-semibold text-main-text truncate" x-text="group.label"></span>
                        </div>
                    </template>
                </div>

                {{-- Column Headers --}}
                <div class="flex">
                    <template x-for="col in columns" :key="col.key">
                        <div class="shrink-0 h-10 flex flex-col items-center justify-center border-r border-b border-main-border"
                            :class="col.isToday ? 'bg-primary text-white' : (col.isWeekend ? 'bg-accent/50' : 'bg-surface')"
                            :style="`width: ${col.width}px`">
                            <span class="text-xs leading-tight"
                                :class="col.isToday ? 'text-white font-bold' : 'text-muted-text'"
                                x-text="col.label1"></span>
                            <span class="text-xs font-semibold leading-tight"
                                :class="col.isToday ? 'text-white' : 'text-muted-text'" x-text="col.label2"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Grid + Bars --}}
            <div class="relative" :style="`width: ${totalWidth}px; height: ${gridHeight}px`">
                {{-- Grid: vertical column lines + row background --}}
                <div class="absolute inset-0 flex pointer-events-none">
                    <template x-for="col in columns" :key="'g-' + col.key">
                        <div class="shrink-0 h-full border-r border-main-border"
                            :class="col.isToday ? 'bg-primary/5' : (col.isWeekend ? 'bg-accent/30' : '')"
                            :style="`width: ${col.width}px`">
                        </div>
                    </template>
                </div>

                {{-- Grid: horizontal row lines (full-width table rows) --}}
                <template x-for="(entry, idx) in visibleEntries" :key="'row-' + entry._id">
                    <div class="absolute left-0 w-full border-b border-main-border pointer-events-none"
                        :style="`top: ${(idx + 1) * rowHeight - 1}px`">
                    </div>
                </template>

                {{-- Today Line --}}
                <template x-if="todayX !== null">
                    <div class="absolute top-0 h-full w-0.5 bg-primary/60 z-20 pointer-events-none"
                        :style="`left: ${todayX}px`">
                    </div>
                </template>

                {{-- Task Bars (centered in each row cell) --}}
                <template x-for="(entry, idx) in visibleEntries" :key="entry._id">
                    <div class="absolute rounded-md overflow-hidden gantt-bar group z-10"
                        :class="hoveredEntryId === entry._id ? 'ring-2 ring-primary !z-30' : ''"
                        :style="getBarStyle(entry, idx)" @mouseenter="hoveredEntryId = entry._id"
                        @mouseleave="hoveredEntryId = null" @mousedown.prevent="startDrag(entry, $event)"
                        @dblclick.stop="$wire?.call('editWorkItem', entry._task_id)">

                        {{-- Left Resize Handle --}}
                        <div class="absolute left-0 top-0 h-full w-2 cursor-ew-resize gantt-handle rounded-l-md hover:bg-black/10 z-40"
                            @mousedown.stop.prevent="startResize(entry, 'start', $event)">
                        </div>

                        {{-- Right Resize Handle --}}
                        <div class="absolute right-0 top-0 h-full w-2 cursor-ew-resize gantt-handle rounded-r-md hover:bg-black/10 z-40"
                            @mousedown.stop.prevent="startResize(entry, 'end', $event)">
                        </div>

                        <!-- Progress Bar -->
                        <div class="relative w-full h-full overflow-hidden rounded-lg"
                            @mousedown.stop="startDrawProgress(entry, $event)" @mousemove="drawProgress($event)"
                            @mouseup.stop="stopDrawProgress()">

                            <div class="progress-fill px-3 flex items-center relative"
                                :style="`width: ${entry.process ?? 0}%; background-color: var(--color-${entry.color}); opacity: 0.9;`">

                                <!-- Progress Tooltip -->
                                <div x-show="hoveredProgressEntryId === entry._id || isDrawing" x-transition
                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse text-inverse-foreground text-xs px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">
                                    <span x-text="`${entry.process ?? 0}% complete`"></span>
                                    <div
                                        class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-full w-0 h-0 border-4 border-transparent border-t-inverse">
                                    </div>
                                </div>
                            </div>

                            <!-- Task Text -->
                            <template x-if="entry.text">
                                <div class="absolute inset-0 px-3 flex items-center pointer-events-none">
                                    <span class="text-xs font-medium text-inverse-foreground truncate"
                                        x-text="entry.text"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        function ganttChartComponent(allEntries, jsDates, dayWidth) {
            return {
                expanded: {},
                entries: allEntries,
                dates: jsDates,
                dayWidth: dayWidth,
                hoveredEntryId: null,
                hoveredProgressEntryId: null,
                draggedEntry: null,
                isDrawing: false,

                get visibleEntries() {
                    let top = -1;
                    return this.entries
                        .filter(e => e._is_parent || this.expanded[e._parent])
                        .map(e => {
                            top++;
                            return {
                                ...e,
                                _top: (top * 40) + 8,
                                _height: e._is_parent ? 32 : 24
                            };
                        });
                },

                _extendRight() {
                    this._loading = true;
                    this.endDate = this._addDays(this.endDate, this._cfg.extend);
                    this.$nextTick(() => { this._loading = false; });
                },

                _scrollTo(dateStr) {
                    const el = this.$refs.timeline;
                    if (!el) return;
                    const x = this._dateToX(dateStr);
                    el.scrollLeft = Math.max(0, x - el.clientWidth / 4);
                },

                // ── Interactions: Move Bar ──
                startDrag(entry, event) {
                    if (!entry.start || !entry.end) return;

                    const startX = event.clientX;
                    const origStart = entry.start;
                    const origEnd = entry.end;
                    const duration = this._diffDays(origStart, origEnd);
                    let moved = false;

                    const onMove = (e) => {
                        const dx = e.clientX - startX;
                        if (!moved && Math.abs(dx) < 4) return;
                        moved = true;

                        const deltaDays = Math.round(dx / this.ppd);
                        entry.start = this._addDays(origStart, deltaDays);
                        entry.end = this._addDays(origStart, deltaDays + duration);
                    };

                    const onUp = () => {
                        window.removeEventListener('mousemove', onMove);
                        window.removeEventListener('mouseup', onUp);
                        document.body.style.cursor = '';
                        if (moved) this._dispatch(entry);
                    };

                    document.body.style.cursor = 'grabbing';
                    window.addEventListener('mousemove', onMove);
                    window.addEventListener('mouseup', onUp);
                },

                // ── Interactions: Resize Bar ──
                startResize(entry, field, event) {
                    const startX = event.clientX;
                    const origDate = field === 'start' ? entry.start : entry.end;
                    const otherDate = field === 'start' ? entry.end : entry.start;
                    const otherTime = this._d(otherDate).getTime();

                    const onMove = (e) => {
                        const dx = e.clientX - startX;
                        const deltaDays = Math.round(dx / this.ppd);
                        const newDate = this._addDays(origDate, deltaDays);
                        const newTime = this._d(newDate).getTime();

                        if (field === 'start' && newTime >= otherTime) return;
                        if (field === 'end' && newTime <= otherTime) return;

                        entry[field] = newDate;
                    };

                    const onUp = () => {
                        window.removeEventListener('mousemove', onMove);
                        window.removeEventListener('mouseup', onUp);
                        this._dispatch(entry);
                    };

                    window.addEventListener('mousemove', onMove);
                    window.addEventListener('mouseup', onUp);
                },

                // ── Interactions: Progress Drag ──
                startProgressDrag(entry, event) {
                    const bar = event.target.closest('.gantt-bar');
                    if (!bar) return;

                    const onMove = (e) => {
                        const rect = bar.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        entry.process = Math.max(0, Math.min(100, Math.round((x / rect.width) * 100)));
                    };

                    const onUp = () => {
                        window.removeEventListener('mousemove', onMove);
                        window.removeEventListener('mouseup', onUp);
                        this._dispatch(entry);
                    };

                    window.addEventListener('mousemove', onMove);
                    window.addEventListener('mouseup', onUp);
                },

                // ── Dispatch Update ──
                _dispatch(entry) {
                    this.$el.dispatchEvent(new CustomEvent('gantt-updated', {
                        detail: {
                            id: entry._task_id,
                            _id: entry._id,
                            start: entry.start,
                            end: entry.end,
                            process: entry.process,
                        },
                        bubbles: true,
                    }));
                };

                window.addEventListener('mousemove', moveHandler);
                window.addEventListener('mouseup', upHandler);
            },

                startDrawProgress(entry, event) {
                this.draggedEntry = entry;
                this.isDrawing = true;
                this.hoveredProgressEntryId = entry._id;
                this.drawProgress(event);
            },

            drawProgress(event) {
                if (!this.isDrawing || !this.draggedEntry) return;

                const container = event.currentTarget;
                const rect = container.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));

                this.draggedEntry.process = Math.round(percent);
            },

            stopDrawProgress() {
                if (this.isDrawing && this.draggedEntry) {
                    this.$el.dispatchEvent(new CustomEvent('gantt-updated', {
                        detail: {
                            id: this.draggedEntry._task_id,
                            _id: this.draggedEntry._id,
                            start: this.draggedEntry.start,
                            end: this.draggedEntry.end,
                            process: this.draggedEntry.process
                        },
                        bubbles: true
                    }));
                }

                this.isDrawing = false;
                this.draggedEntry = null;
                this.hoveredProgressEntryId = null;
            }
        };
        }
    </script>
@endonce