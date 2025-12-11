@props(['tasks' => [], 'scale' => 'day'])

@php
use Carbon\Carbon;

$allowedColors = ['primary', 'secondary', 'success', 'warning', 'danger', 'gray'];
$normalizeColor = fn($color) => in_array($color, $allowedColors) ? $color : 'gray';

// Flatten tasks with parent index and preserve `process` percent for bars
$entries = [];
foreach ($tasks as $taskIndex => $task) {
    $subtasks = $task['subtasks'] ?? [];
    $fallbackStart = collect($subtasks)->pluck('start')->filter()->map(fn($d) => Carbon::parse($d))->min()?->toDateTimeString();
    $fallbackEnd = collect($subtasks)->pluck('end')->filter()->map(fn($d) => Carbon::parse($d))->max()?->toDateTimeString();

    $entries[] = array_merge($task, [
        '_parent' => $taskIndex,
        '_is_parent' => true,
        '_id' => 'parent-' . $taskIndex,
        '_task_id' => $task['id'] ?? null,
        'text' => $task['text'] ?? null,
        'color' => $normalizeColor($task['color'] ?? null),
        'start' => $task['start'] ?? $fallbackStart,
        'end' => $task['end'] ?? $fallbackEnd,
        'process' => $task['process'] ?? 0,
    ]);

    foreach ($subtasks as $subtaskIndex => $subtask) {
        $entries[] = array_merge($subtask, [
            '_parent' => $taskIndex,
            '_is_parent' => false,
            '_id' => 'sub-' . $taskIndex . '-' . $subtaskIndex,
            '_task_id' => $subtask['id'] ?? null,
            'color' => $normalizeColor($subtask['color'] ?? null),
            'process' => $subtask['process'] ?? 0,
        ]);
    }
}
$allEntries = collect($entries);

if ($allEntries->isEmpty()) {
    return;
}

$startDate = $allEntries->pluck('start')->filter()->map(fn($d) => Carbon::parse($d))->min()?->startOfDay();
$endDate = $allEntries->pluck('end')->filter()->map(fn($d) => Carbon::parse($d))->max()?->endOfDay();

if (!$startDate || !$endDate) {
    return;
}

$totalDays = $startDate->diffInDays($endDate) + 1;
$dayWidth = 100;

$dates = collect(range(0, $totalDays - 1))->map(fn($i) => $startDate->copy()->addDays($i));
$jsDates = $dates->map(fn($d) => $d->format('Y-m-d'))->values();
@endphp

<div x-data="ganttChartComponent(@js($allEntries), @js($jsDates), {{ $dayWidth }})"
     x-ref="ganttComponent"
     @gantt-updated.window="$wire.call('handleGanttUpdate', $event.detail)"
     class="h-[672px] w-full rounded-2xl bg-surface shadow-lg overflow-hidden">

    <style>
        .progress-fill {
            height: 100%;
            transition: width 0.2s ease;
            border-radius: 6px;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .gantt-bar {
            transition: all 0.2s ease;
            cursor: move;
        }

        .gantt-bar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .resize-handle {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .gantt-bar:hover .resize-handle {
            opacity: 1;
        }

        :root {
            --color-primary: #3b82f6;
            --color-secondary: #64748b;
            --color-success: #22c55e;
            --color-warning: #eab308;
            --color-danger: #ef4444;
            --color-gray: #6b7280;
        }
    </style>

    <div class="relative flex h-full w-full overflow-hidden">
        <!-- Left Sidebar -->
        <div class="sticky left-0 top-0 z-20 w-64 shrink-0 border-r border-main-border bg-surface overflow-y-auto no-scrollbar">
            @isset($header)
                <div class="sticky top-0 z-30 bg-accent border-b border-main-border">
                    {{ $header }}
                </div>
            @endisset

            @foreach($tasks as $taskIndex => $task)
                <div>
                    <!-- Parent Task -->
                    <button @click="expanded[{{ $taskIndex }}] = !expanded[{{ $taskIndex }}]"
                            class="flex w-full items-center justify-between px-4 py-3 border-b border-main-border hover:bg-accent transition-colors
                            {{ !empty($task['subtasks']) ? 'font-semibold' : 'font-medium' }}"
                            type="button">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="h-3 w-3 rounded-full flex-shrink-0"
                                 :style="`background-color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})`">
                            </div>
                            <p class="text-sm text-main-text truncate">{{ $task['title'] }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(!empty($task['process']))
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-accent"
                                      :style="`color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})`">
                                    {{ $task['process'] }}%
                                </span>
                            @endif

                            @if(!empty($task['subtasks']))
                                <span :class="{ 'rotate-180': expanded[{{ $taskIndex }}], 'rotate-0': !expanded[{{ $taskIndex }}] }"
                                      class="transition-transform">
                                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </button>

                    <!-- Subtasks -->
                    @if(!empty($task['subtasks']))
                        <div x-show="expanded[{{ $taskIndex }}]"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            @foreach($task['subtasks'] as $subtask)
                                <div class="flex items-center gap-3 px-4 py-2.5 pl-10 border-b border-main-border hover:bg-accent/50 transition-colors">
                                    <div class="h-2 w-2 rounded-full flex-shrink-0"
                                         :style="`background-color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})`">
                                    </div>
                                    <p class="text-sm text-main-text flex-1 min-w-0 truncate">{{ $subtask['title'] }}</p>
                                    @if(!empty($subtask['process']))
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-accent"
                                              :style="`color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})`">
                                            {{ $subtask['process'] }}%
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Right Timeline -->
        <div class="relative flex h-full w-full flex-col overflow-auto no-scrollbar">
            <!-- Month/Year Header -->
            <div class="sticky left-0 top-0 h-10 w-full border-b border-main-border bg-accent px-4 py-2 z-20 flex items-center justify-between">
                <p class="text-sm font-semibold text-main-text">{{ $startDate->format('F Y') }}</p>
                <p class="text-xs text-muted-text">{{ $totalDays }} days</p>
            </div>

            <!-- Date Headers -->
            <div class="flex w-full items-center sticky top-10 z-10 bg-surface">
                @foreach ($dates as $date)
                    <div class="w-[{{ $dayWidth }}px] flex-shrink-0 text-center border-r border-b border-main-border py-2
                                {{ $date->isToday() ? 'bg-primary/10' : '' }}">
                        <div class="text-xs {{ $date->isToday() ? 'text-primary font-bold' : 'text-muted-text' }}">
                            {{ $date->format('d') }}
                        </div>
                        <div class="text-xs font-semibold {{ $date->isToday() ? 'text-primary' : 'text-muted-text' }}">
                            {{ strtoupper($date->format('D')) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Timeline Grid & Bars -->
            <div class="relative flex h-full w-fit">
                <!-- Grid Lines -->
                @for ($i = 0; $i < $dates->count(); $i++)
                    <div class="w-[{{ $dayWidth }}px] h-full border-r border-main-border {{ $dates[$i]->isToday() ? 'bg-primary/5' : '' }}"></div>
                @endfor

                <!-- Task Bars -->
                <template x-for="entry in visibleEntries" :key="entry._id">
                    <div class="absolute rounded-lg gantt-bar"
                         :class="hoveredEntryId === entry._id ? 'ring-2 ring-primary z-30' : 'z-10'"
                         :style="getBarStyle(entry)"
                         @mouseenter="hoveredEntryId = entry._id"
                         @mouseleave="hoveredEntryId = null"
                         @click="$wire.call('editWorkItem', entry._task_id)">

                        <!-- Left Resize Handle -->
                        <div class="absolute left-0 top-0 h-full w-2 cursor-ew-resize z-40 resize-handle hover:bg-black/20 rounded-l-lg"
                             @mousedown.stop.prevent="startResize(entry, 'start', $event)">
                        </div>

                        <!-- Right Resize Handle -->
                        <div class="absolute right-0 top-0 h-full w-2 cursor-ew-resize z-40 resize-handle hover:bg-black/20 rounded-r-lg"
                             @mousedown.stop.prevent="startResize(entry, 'end', $event)">
                        </div>

                        <!-- Progress Bar -->
                        <div class="relative w-full h-full overflow-hidden rounded-lg"
                             @mousedown.stop="startDrawProgress(entry, $event)"
                             @mousemove="drawProgress($event)"
                             @mouseup.stop="stopDrawProgress()">

                            <div class="progress-fill px-3 flex items-center relative"
                                 :style="`width: ${entry.process ?? 0}%; background-color: var(--color-${entry.color}); opacity: 0.9;`">

                                <!-- Progress Tooltip -->
                                <div x-show="hoveredProgressEntryId === entry._id || isDrawing"
                                     x-transition
                                     class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded shadow-lg whitespace-nowrap z-50">
                                    <span x-text="`${entry.process ?? 0}% complete`"></span>
                                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-full w-0 h-0 border-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </div>

                            <!-- Task Text -->
                            <template x-if="entry.text">
                                <div class="absolute inset-0 px-3 flex items-center pointer-events-none">
                                    <span class="text-xs font-medium text-white truncate" x-text="entry.text"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

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

            getBarStyle(entry) {
                const startIdx = this.dates.indexOf(entry.start?.slice(0, 10));
                const endIdx = this.dates.indexOf(entry.end?.slice(0, 10));

                if (startIdx === -1 || endIdx === -1) return 'display: none;';

                const left = startIdx * this.dayWidth + 4;
                const width = Math.max((endIdx - startIdx + 1) * this.dayWidth - 8, this.dayWidth - 8);
                const height = entry._height;

                return `top: ${entry._top}px; left: ${left}px; width: ${width}px; height: ${height}px; border: 2px solid var(--color-${entry.color});`;
            },

            startResize(entry, field, e) {
                const initialMouseX = e.clientX;
                const initialDate = new Date(field === 'start' ? entry.start : entry.end);
                const referenceDate = new Date(field === 'start' ? entry.end : entry.start);

                const moveHandler = (ev) => {
                    const deltaX = ev.clientX - initialMouseX;
                    const deltaDays = Math.round(deltaX / this.dayWidth);
                    const newDate = new Date(initialDate);
                    newDate.setDate(newDate.getDate() + deltaDays);

                    // Prevent invalid ranges
                    if (field === 'start' && newDate >= referenceDate) return;
                    if (field === 'end' && newDate <= referenceDate) return;

                    const isoDate = newDate.toISOString().slice(0, 10);
                    entry[field] = isoDate;
                };

                const upHandler = () => {
                    window.removeEventListener('mousemove', moveHandler);
                    window.removeEventListener('mouseup', upHandler);

                    this.$el.dispatchEvent(new CustomEvent('gantt-updated', {
                        detail: {
                            id: entry._task_id,
                            _id: entry._id,
                            start: entry.start,
                            end: entry.end,
                            process: entry.process
                        },
                        bubbles: true
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
