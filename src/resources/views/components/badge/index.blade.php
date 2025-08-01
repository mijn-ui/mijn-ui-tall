@props([
    'title' => null,
    'color' => 'primary',
    'variant' => 'default',
    'size' => 'xs',
    'rounded' => 'sm',
    'fronticon' => null,
    'backicon' => null,
    'onClick' => null
])

@php
    $base =
        'inline-flex items-center font-semibold transition duration-300 ease-in-out focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50';

    $colorClasses = $colorClasses = [
        'default' => [
             'primary' =>
                'bg-primary text-primary-foreground hover:bg-primary/80 focus-visible:ring-primary active:bg-primary/70',
            'secondary' =>
                'bg-secondary text-secondary-foreground hover:bg-secondary/80 focus-visible:ring-secondary active:bg-secondary/70',
            'success' =>
                'bg-success text-success-foreground hover:bg-success/80 focus-visible:ring-success active:bg-success/70',
            'info' => 'bg-info text-info-foreground hover:bg-info/80 focus-visible:ring-info active:bg-info/70',
            'warning' =>
                'bg-warning text-warning-foreground hover:bg-warning/80 focus-visible:ring-warning active:bg-warning/70',
            'danger' =>
                'bg-danger text-danger-foreground hover:bg-danger/80 focus-visible:ring-danger active:bg-danger/70',
        ],

        'subtle' => [
             'primary' =>
                'border border-border-primary-subtle bg-primary-subtle text-primary-foreground-subtle hover:bg-primary-subtle/80 focus-visible:ring-primary-subtle active:bg-primary-subtle/70',
            'secondary' =>
                'border border-border-secondary-subtle bg-secondary-subtle text-secondary-foreground-subtle hover:bg-secondary-subtle/80 focus-visible:ring-secondary-subtle active:bg-secondary-subtle/70',
            'success' =>
                'border border-border-success-subtle bg-success-subtle text-success-foreground-subtle hover:bg-success-subtle/80 focus-visible:ring-success-subtle active:bg-success-subtle/70',
            'info' =>
                'border border-border-info-subtle bg-info-subtle text-info-foreground-subtle hover:bg-info-subtle/80 focus-visible:ring-info-subtle active:bg-info-subtle/70',
            'warning' =>
                'border border-border-warning-subtle bg-warning-subtle text-warning-foreground-subtle hover:bg-warning-subtle/80 focus-visible:ring-warning-subtle active:bg-warning-subtle/70',
            'danger' =>
                'border border-border-danger-subtle bg-danger-subtle text-danger-foreground-subtle hover:bg-danger-subtle/80 focus-visible:ring-danger-subtle active:bg-danger-subtle/70',
        ],

        'outline' => [
             'primary' =>
                'border border-primary text-primary bg-transparent hover:bg-primary/10 focus-visible:ring-primary active:bg-primary/20',
            'secondary' =>
                'border border-border-secondary text-secondary-foreground bg-transparent hover:bg-secondary/10 focus-visible:ring-secondary active:bg-secondary/20',
            'success' =>
                'border border-success text-success bg-transparent hover:bg-success/10 focus-visible:ring-success active:bg-success/20',
            'info' =>
                'border border-info text-info bg-transparent hover:bg-info/10 focus-visible:ring-info active:bg-info/20',
            'warning' =>
                'border border-warning text-warning bg-transparent hover:bg-warning/10 focus-visible:ring-warning active:bg-warning/20',
            'danger' =>
                'border border-danger text-danger bg-transparent hover:bg-danger/10 focus-visible:ring-danger active:bg-danger/20',
        ],

        'ghost' => [
            'default' =>
                'bg-transparent text-default hover:bg-default/10 focus-visible:ring-default active:bg-default/20',
            'primary' =>
                'bg-transparent text-primary hover:bg-primary/10 focus-visible:ring-primary active:bg-primary/20',
            'secondary' =>
                'bg-transparent text-secondary hover:bg-secondary/10 focus-visible:ring-secondary active:bg-secondary/20',
            'success' =>
                'bg-transparent text-success hover:bg-success/10 focus-visible:ring-success active:bg-success/20',
            'info' => 'bg-transparent text-info hover:bg-info/10 focus-visible:ring-info active:bg-info/20',
            'warning' =>
                'bg-transparent text-warning hover:bg-warning/10 focus-visible:ring-warning active:bg-warning/20',
            'danger' => 'bg-transparent text-danger hover:bg-danger/10 focus-visible:ring-danger active:bg-danger/20',
        ],
    ][$variant][$color];

    $sizeClasses = [
        'xs' => 'text-xs px-1 py-0.5',
        'sm' => 'text-sm px-2 py-0.5',
        'md' => 'text-sm px-2.5 py-1',
        'lg' => 'text-base px-3 py-1.5',
    ][$size ?? 'xs'];

    $roundedClasses = [
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ][$rounded ?? 'md'];

    $finalClasses = "$base $colorClasses $sizeClasses $roundedClasses";
@endphp

<span {{ $attributes->merge(['class' => $finalClasses]) }}>
    @if ($fronticon)
        <mijnui:icon :name="$fronticon" class="pl-1 shrink-0" :size="$size" />
    @endif

    @if ($title)
        <span class="font-semibold px-1">{{ $title }}</span>
    @else
        <span class="inline-flex gap-1 items-center">
            {{ $slot }}
        </span>
    @endif

    @if ($backicon)
        <mijnui:icon x-on:click="{{$onClick}}" :name="$backicon" class="pr-1 shrink-0 {{$onClick ? 'cursor-pointer' : ''}}"  :size="$size" />
    @endif
</span>
