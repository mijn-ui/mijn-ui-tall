@props([
    'name' => null,
])
<th scope="col" 
    {{ $attributes->class([
        'px-4 py-3.5 text-left align-middle font-semibold text-muted-foreground first:pl-6 last:pr-6',
    ]) }}
    @if($name) x-show="visibleColumns.includes('{{ $name }}')" @endif
>
    {{ $slot }}
</th>
