@props([
    'label' => null,
    'value' => 0,
    'minLabel' => '',
    'maxLabel' => '',
    'maxValue' => 100,
])

<div x-data="{
    value: 0,
    target: {{$value}},
    max: {{$maxValue}},
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

    <div class="flex items-center justify-between text-sm font-medium text-foreground">
        <h5>{{ $label }}</h5>
        <p x-text="Math.floor((value / max) * 100) + '%'"></p>
    </div>

    <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted">
        <div x-cloak class="h-full bg-primary transition-all duration-100 ease-in-out"
            :style="'width: ' + (value / max * 100) + '%'">
        </div>
    </div>

    <div class="text-xs flex items-center justify-between text-muted-foreground">
        <p>{{ $minLabel }}</p>
        <p>{{ $maxLabel }}</p>
    </div>
</div>
