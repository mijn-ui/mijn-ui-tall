@props([
    'name' => null,
])
<td 
    {{ $attributes->class([
        'px-4 py-3 align-middle text-foreground first:pl-6 last:pr-6',
    ]) }}
    @if($name) x-show="visibleColumns.includes('{{ $name }}')" @endif
>
    {{ $slot }}
</td>
