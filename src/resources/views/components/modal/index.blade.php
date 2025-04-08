@props(['name'])

<div x-data="{ open: false }" {{$attributes}}>

    @isset($content)
        {{ $content }}
    @endisset

    @isset($trigger)
        {{ $trigger }}
    @endisset

</div>
