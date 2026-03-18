@props([
    'label' => null,
    'value' => 0,
    'maxValue' => 100,
    'animate' => false,
])

<div x-data="{
    value: 0,
    target: @js($value),
    max: @js($maxValue),
    animate: @js($animate),
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
}" x-init="start()" {{ $attributes->merge(['class' => 'w-full space-y-1']) }}>

    @once
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
    @endonce

    <div class="flex items-center justify-between text-sm font-medium text-foreground">
        <h5>{{ $label }}</h5>
        <p x-text="Math.min(Math.floor((value / max) * 100), 100) + '%'"></p>
    </div>

    <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted"
         role="progressbar"
         :aria-valuenow="value"
         aria-valuemin="0"
         :aria-valuemax="max"
         :aria-label="'{{ $label }}' || 'Progress'">
        <div x-cloak
             class="h-full bg-primary transition-all duration-300 ease-linear"
             :class="animate ? 'animate-water' : ''"
             :style="'width: ' + Math.min(value / max * 100, 100) + '%'">
        </div>
    </div>
</div>
