@props(['tasks' => [], 'scale' => 'day'])

@php
use Carbon\Carbon;

$allowedColors = ['primary', 'secondary', 'success', 'warning', 'danger', 'gray'];
$normalizeColor = fn($color) => in_array($color, $allowedColors) ? $color : 'gray';

$entries = [];
foreach ($tasks as $taskIndex => $task) {
    $subtasks = $task['subtasks'] ?? [];
    $fallbackStart = collect($subtasks)->pluck('start')->filter()->map(fn($d) => Carbon::parse($d))->min()?->toDateTimeString();
    $fallbackEnd = collect($subtasks)->pluck('end')->filter()->map(fn($d) => Carbon::parse($d))->max()?->toDateTimeString();

    $entries[] = array_merge($task, [
        '_parent' => $taskIndex,
        '_is_parent' => true,
        '_id' => 'parent-' . $taskIndex,
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
            'color' => $normalizeColor($subtask['color'] ?? null),
            'process' => $subtask['process'] ?? 0,
        ]);
    }
}
$allEntries = collect($entries);

$startDate = $allEntries->pluck('start')->map(fn($d) => Carbon::parse($d))->min()->startOfDay();
$endDate = $allEntries->pluck('end')->map(fn($d) => Carbon::parse($d))->max()->endOfDay();
$totalDays = $startDate->diffInDays($endDate) + 1;
$dayWidth = 63;

$dates = collect(range(0, $totalDays - 1))->map(fn($i) => $startDate->copy()->addDays($i));
$jsDates = $dates->map(fn($d) => $d->format('Y-m-d'))->values();
@endphp

<div x-data="ganttChartComponent({{ $allEntries->toJson() }}, {{ $jsDates->toJson() }}, {{ $dayWidth }})" x-ref="ganttComponent"
    class="flex h-full w-full bg-surface">
    <style>
        .progress-fill {
            height: 100%;
            transition: width 0.4s ease;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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

    <!-- Sidebar -->
    <div class="sticky left-0 top-0 z-20 hidden w-60 shrink-0 flex-col border-r border-main-border bg-surface sm:flex">
        @isset($header)
            {{ $header }}
        @endisset

        <div class="flex-1 overflow-y-auto no-scrollbar">
            @foreach($tasks as $taskIndex => $task)
                <div class="border-b border-main-border">
                    <!-- Parent Task -->
                    <button @click="expanded[{{ $taskIndex }}] = !expanded[{{ $taskIndex }}]"
                        class="flex w-full items-center justify-between px-4 py-3 hover:bg-accent transition-colors {{ !empty($task['subtasks']) ? 'bg-accent/50' : '' }}"
                        type="button">
                        <div class="flex flex-1 items-center gap-3 min-w-0">
                            <div class="h-2.5 w-2.5 shrink-0 rounded-full"
                                :style="`background-color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})`">
                            </div>
                            <p class="truncate text-sm font-medium text-main-text">{{ $task['title'] }}</p>
                        </div>
                        @if(!empty($task['subtasks']))
                            <span :class="{ 'rotate-0': expanded[{{ $taskIndex }}], 'rotate-180': !expanded[{{ $taskIndex }}] }"
                                class="ml-2 shrink-0 transition-transform duration-200">
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </span>
                        @endif
                    </button>

                    <!-- Subtasks -->
                    @if(!empty($task['subtasks']))
                        <div x-show="expanded[{{ $taskIndex }}]" x-transition class="bg-surface/50">
                            @foreach($task['subtasks'] as $subtask)
                                <div class="flex items-center gap-3 border-t border-main-border/50 px-4 py-2.5 hover:bg-accent/30 transition-colors">
                                    <div class="h-2 w-2 shrink-0 rounded-full"
                                        :style="`background-color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})`">
                                    </div>
                                    <p class="truncate text-sm text-main-text">{{ $subtask['title'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Timeline -->
    <div class="relative flex h-full w-full flex-col overflow-hidden">
        <!-- Month Header -->
        <div class="sticky left-0 top-0 z-10 border-b border-main-border bg-accent px-4 py-2">
            <p class="text-xs font-semibold text-muted-text uppercase tracking-wide">{{ now()->format('M Y') }}</p>
        </div>

        <!-- Date Header -->
        <div class="sticky left-0 top-8 z-10 flex border-b border-main-border bg-surface">
            @foreach ($dates as $date)
                <div class="flex w-[63px] shrink-0 flex-col items-center justify-center border-r border-main-border/50 py-2">
                    <p class="text-xs font-semibold text-main-text">{{ $date->format('d') }}</p>
                    <p class="text-xs text-muted-text">{{ strtoupper($date->format('D')[0]) }}</p>
                </div>
            @endforeach
        </div>

        <!-- Timeline Bars Container -->
        <div class="relative flex flex-1 overflow-auto no-scrollbar">
            <div class="relative w-fit">
                <!-- Grid Lines -->
                <div class="absolute inset-0 flex pointer-events-none">
                    @for ($i = 0; $i < $dates->count(); $i++)
                        <div class="w-[63px] shrink-0 border-r border-main-border/30"></div>
                    @endfor
                </div>

                <!-- Task Bars -->
                <template x-for="entry in visibleEntries" :key="entry._id">
                    <div class="absolute border border-main-border bg-gray-300 rounded-md transition-all duration-300 ease-in-out"
                        :class="hoveredEntryId === entry._id ? 'outline outline-2 outline-blue-500 shadow-lg' : 'shadow-sm'"
                        :style="getBarStyle(entry) + (entry.text ? 'height: 1.75rem;' : 'height: 0.5rem;')"
                        @mouseenter="hoveredEntryId = entry._id"
                        @mouseleave="clearHover()">

                        <!-- Left Resize Handle -->
                        <div class="absolute left-0 top-0 h-full w-1.5 cursor-ew-resize z-10 hover:bg-blue-400/50"
                            @mousedown.prevent="startResize(entry, 'start', $event)"></div>

                        <!-- Right Resize Handle -->
                        <div class="absolute right-0 top-0 h-full w-1.5 cursor-ew-resize z-10 hover:bg-blue-400/50"
                            @mousedown.prevent="startResize(entry, 'end', $event)"></div>

                        <!-- Progress Bar -->
                        <div class="relative w-full h-full progress-wrapper"
                            @mousedown="startDrawProgress(entry, $event)"
                            @mousemove="drawProgress($event)"
                            @mouseup="stopDrawProgress()"
                            @mouseenter="hoveredProgressEntryId = entry._id"
                            @mouseleave="hoveredProgressEntryId = null">
                            <div class="progress-fill px-2 relative pointer-events-none flex items-center"
                                :style="`width: ${entry.process ?? 0}%; background-color: var(--color-${entry.color}); opacity: 0.8;`">
                                <div x-show="hoveredProgressEntryId === entry._id" x-transition
                                    class="absolute -top-7 right-0 bg-black text-white text-xs px-2 py-1 rounded shadow-lg z-50 whitespace-nowrap">
                                    <span x-text="`${entry.process ?? 0}%`"></span>
                                </div>
                            </div>

                            <!-- Task Text -->
                            <template x-if="entry.text">
                                <div class="absolute inset-0 px-2 flex items-center text-white text-xs font-semibold pointer-events-none truncate">
                                    <span x-text="entry.text"></span>
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
            resizing: null,
            hoveredProgressEntryId: null,
            hoveredEntryId: null,
            draggedEntry: null,
            isDrawing: false,

            get visibleEntries() {
                let top = -1;
                return this.entries
                    .filter(e => e._is_parent || this.expanded[e._parent])
                    .map(e => ({ ...e, _top: (++top + 1) * 48 - 20 }));
            },

            getBarStyle(entry) {
                const startIdx = this.dates.indexOf(entry.start?.slice(0, 10));
                const endIdx = this.dates.indexOf(entry.end?.slice(0, 10));
                if (startIdx === -1 || endIdx === -1) return 'display: none';
                const left = startIdx * this.dayWidth;
                const width = Math.max((endIdx - startIdx + 1) * this.dayWidth, this.dayWidth);
                return `top: ${entry._top}px; left: ${left}px; width: ${width}px;`;
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

                    if (field === 'start' && newDate >= referenceDate) return;
                    if (field === 'end' && newDate <= referenceDate) return;

                    const isoDate = newDate.toISOString().slice(0, 10);
                    if (field === 'start') {
                        entry.start = isoDate;
                    } else {
                        entry.end = isoDate;
                    }
                };

                const upHandler = () => {
                    window.removeEventListener('mousemove', moveHandler);
                    window.removeEventListener('mouseup', upHandler);
                    this.resizing = null;
                    this.$el.dispatchEvent(new CustomEvent('gantt-updated', {
                        detail: entry,
                        bubbles: true
                    }));
                };

                window.addEventListener('mousemove', moveHandler);
                window.addEventListener('mouseup', upHandler);
            },

            clearHover() {
                this.hoveredEntryId = null;
            },

            startDrawProgress(entry, event) {
                this.draggedEntry = entry;
                this.isDrawing = true;
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
                this.isDrawing = false;
                this.draggedEntry = null;
            },
        };
    }
</script>
