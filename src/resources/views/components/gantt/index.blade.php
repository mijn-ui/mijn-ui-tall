@props([
    'variant' => 'default',
    'header' => null,
    'tasks' => [],
    'members' => [],
    'scale' => 'month',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'h-[672px] w-full rounded-2xl bg-surface p-4 ' . $class]) }}>
    <div class="relative flex h-full w-full overflow-hidden rounded-lg border border-main-border">
        @if($variant === 'attendance')
            @include('mijnui::components.gantt.attendance')
        @else
            @include('mijnui::components.gantt.content')
        @endif
    </div>
</div>

@once
<script>
function ganttChart({ tasks, scale }) {
    return {
        tasks: tasks,
        scale: scale,
        chartData: [],
        init() {
            if (!this.tasks.length) return;

            const parseDate = d => new Date(d);
            const toDays = ms => ms / (1000 * 60 * 60 * 24);

            const min = parseDate(Math.min(...this.tasks.map(t => parseDate(t.start))));
            const max = parseDate(Math.max(...this.tasks.map(t => parseDate(t.end))));

            const total = toDays(max - min) || 1;

            this.chartData = this.tasks.map((task, i) => {
                const start = parseDate(task.start);
                const end = parseDate(task.end);

                const offset = toDays(start - min) / total * 100;
                const width = toDays(end - start) / total * 100;

                return {
                    ...task,
                    offset: offset.toFixed(2),
                    width: width.toFixed(2),
                    top: 16 + i * 38,
                };
            });
        }
    };
}
</script>
@endonce
