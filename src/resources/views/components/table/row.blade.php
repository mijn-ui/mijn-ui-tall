{{--@props(['key'])--}}

<tr {{$attributes->merge(['class' => 'hover:bg-secondary'])}}>
    {{ $slot }}
</tr>
