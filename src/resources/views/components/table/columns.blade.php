<thead>
    <tr {{ $attributes->class([
    'h-8 border-b border-border bg-muted/20 text-xs font-semibold uppercase tracking-wider text-muted-foreground transition-colors'
]) }}>
        {{ $slot }}
    </tr>
</thead>