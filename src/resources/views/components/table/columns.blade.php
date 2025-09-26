<thead>
    <tr {{ $attributes->merge([
        'class' => 'bg-secondary text-xs uppercase text-muted-text border-b border-b-background-alt',
    ]) }}>
        {{ $slot }}
    </tr>
</thead>
