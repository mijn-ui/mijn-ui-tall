@props([
    'label' => '',
    'disabled' => false,
])

<div class="px-2 py-1 text-xs font-semibold text-muted-foreground select-none">
    {{ $label }}
</div>

<div class="{{ $disabled ? 'opacity-50 pointer-events-none' : '' }}">
    {{ $slot }}
</div>
