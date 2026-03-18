@props([
    'variant' => 'default',
    'header' => null,
    'tasks' => [],
    'members' => [],
    'scale' => 'day',
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
