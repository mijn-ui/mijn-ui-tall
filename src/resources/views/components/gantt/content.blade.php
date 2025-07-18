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
        'text' => $task['text'] ?? null,
        'color' => $normalizeColor($task['color'] ?? null),
        'start' => $task['start'] ?? $fallbackStart,
        'end' => $task['end'] ?? $fallbackEnd,
        'process' => $task['process'] ?? 0,  // percent 0-100
    ]);

    foreach ($subtasks as $subtaskIndex => $subtask) {
        $entries[] = array_merge($subtask, [
            '_parent' => $taskIndex,
            '_is_parent' => false,
            '_id' => 'sub-' . $taskIndex . '-' . $subtaskIndex,
            'color' => $normalizeColor($subtask['color'] ?? null),
            'process' => $subtask['process'] ?? 0,  // percent 0-100
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

<div x-data="() => ({
    expanded: {},
    entries: {{ $allEntries->toJson() }},
    dates: {{ $jsDates->toJson() }},
    dayWidth: {{ $dayWidth }},
    get visibleEntries() {
        let top = -1;
        return this.entries.filter(e => e._is_parent || this.expanded[e._parent])
            .map(e => ({ ...e, _top: (++top + 1) * 40 - 24 }));
    },
    getBarStyle(entry) {
        const startIdx = this.dates.indexOf(entry.start?.slice(0, 10));
        const endIdx = this.dates.indexOf(entry.end?.slice(0, 10));
        if (startIdx === -1 || endIdx === -1) return 'display: none';
        const left = startIdx * this.dayWidth;
        const width = Math.max((endIdx - startIdx + 1) * this.dayWidth, this.dayWidth);
        return `top: ${entry._top}px; left: ${left}px; width: ${width}px;`;
    }
})" class="h-[672px] w-full max-w-[1056px] rounded-2xl bg-surface">
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

    <div class="relative flex h-full w-full overflow-hidden rounded-lg">
        <!-- Sidebar -->
        <div
            class="sticky left-0 top-0 z-10 hidden w-60 shrink-0 border-r border-main-border bg-surface p-0 sm:block overflow-y-auto no-scrollbar">
            @isset($header)
                {{ $header }}
            @endisset
            @foreach($tasks as $taskIndex => $task)
                <div>
                    <button @click="expanded[{{ $taskIndex }}] = !expanded[{{ $taskIndex }}]"
                            class="flex w-full items-center justify-between px-3 py-2 border-b border-main-border font-medium h-10 
                            {{ !empty($task['subtasks']) ? 'bg-accent' : '' }}"
                        type="button">
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full"
                                :style="`background-color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})`">
                            </div>
                            <p class="text-sm text-main-text">{{ $task['title'] }}</p>
                            @if(!empty($task['process']))
                                <span class="ml-2 text-xs font-semibold"
                                    :style="`color: var(--color-{{ $normalizeColor($task['color'] ?? null) }})`">
                                </span>
                            @endif
                        </div>
                        @if(!empty($task['subtasks']))
                            <span :class="{ 'rotate-0': expanded[{{ $taskIndex }}], 'rotate-180': !expanded[{{ $taskIndex }}] }"
                                class="transition-transform">
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </span>
                        @endif
                    </button>

                    @if(!empty($task['subtasks']))
                        <div x-show="expanded[{{ $taskIndex }}]" x-transition>
                            @foreach($task['subtasks'] as $subtask)
                                <div class="flex items-center gap-2 h-10 px-6 py-2 border-b border-main-border">
                                    <div class="h-2 w-2 rounded-full"
                                        :style="`background-color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})`">
                                    </div>
                                    <p class="text-sm text-main-text">{{ $subtask['title'] }}</p>
                                    @if(!empty($subtask['process']))
                                        <span class="ml-2 text-xs font-semibold"
                                            :style="`color: var(--color-{{ $normalizeColor($subtask['color'] ?? null) }})`">
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
            <!-- Header -->
            <div class="sticky left-0 top-0 h-7 w-full border-b border-main-border bg-accent px-3 py-1 z-10">
                <p class="text-sm font-semibold text-muted-text">{{ now()->format('M Y') }}</p>
            </div>

            <div class="flex w-full items-center">
                @foreach ($dates as $date)
                    <div
                        class="w-[{{ $dayWidth }}px] flex-shrink-0 text-center border-r border-b border-main-border text-sm text-muted-text">
                        {{ $date->format('d') }}<span class="font-semibold">-{{ strtoupper($date->format('D')[0]) }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Timeline Bars -->
            <div class="[&amp;>div]:flex-shrink-0 relative flex h-full w-fit items-center">
                @for ($i = 0; $i < $dates->count(); $i++)
                    <div class="w-[{{ $dayWidth }}px] h-full border-r border-main-border"></div>
                @endfor
                <template x-for="entry in visibleEntries" :key="entry._id">
                    <div class="absolute border border-main-border bg-gray-300 rounded-lg"
                        :style="getBarStyle(entry) + (entry.text ? 'height: 1rem;' : 'height: 0.5rem;')">
                        <div class="progress-fill px-2"
                            :style="`width: ${entry.process ?? 0}%; background-color: var(--color-${entry.color}); opacity: 0.8;`">
                        </div>
                        <template x-if="entry.text">
                            <div
                                class="absolute inset-0 px-2 text-white text-xs font-semibold">
                                <span x-text="entry.text"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>