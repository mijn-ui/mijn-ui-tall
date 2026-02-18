@props([
    'label' => '',
    'disabled' => false,
])

<div class="px-2 py-1.5 text-xs font-medium text-muted-foreground select-none">
    {{ $label }}
</div>

<div class="{{ $disabled ? 'opacity-50 pointer-events-none' : '' }}">
    {{ $slot }}
</div>
