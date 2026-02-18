@props([
    'label' => null,
    'value' => 0,
    'minLabel' => '',
    'maxLabel' => '',
    'maxValue' => 100,
    'animate' => false,
])

<div x-data="{
    value: 0,
    target: {{$value}},
    max: {{$maxValue}},
    animate: {{ $animate ? 'true' : 'false' }},
    interval: null,
    start() {
        this.interval = setInterval(() => {
            if (this.value >= this.target) {
                clearInterval(this.interval)
                return
            }
            this.value++
        }, 20)
    }
}" x-init="start()" {{ $attributes->merge(['class' => 'w-80 space-y-1']) }}>

    <style>
        @keyframes progress-water {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-water {
            position: relative;
            overflow: hidden;
        }
        .animate-water::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            animation: progress-water 2s infinite ease-in-out;
        }
    </style>

    <div class="flex items-center justify-between text-sm font-medium text-foreground">
        <h5>{{ $label }}</h5>
        <p x-text="Math.floor((value / max) * 100) + '%'"></p>
    </div>

    <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted">
        <div x-cloak 
             class="h-full bg-primary transition-all duration-300 ease-linear"
             :class="animate ? 'animate-water' : ''"
             :style="'width: ' + (value / max * 100) + '%'">
        </div>
    </div>

    <div class="text-xs flex items-center justify-between text-muted-foreground">
        <p>{{ $minLabel }}</p>
        <p>{{ $maxLabel }}</p>
    </div>
</div>
