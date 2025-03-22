{{-- @props(['sortable' => false, 'sorted' => false, 'direction' => 'asc']) --}}

<thead>
    <tr {{ $attributes->merge([
        'class' => 'bg-accent text-xs uppercase text-muted-text',
    ]) }}>
        {{ $slot }}
    </tr>
</thead>
