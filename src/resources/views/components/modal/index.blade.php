@props(['name'])

<span x-data="{ open: false }" {{$attributes}}>

    @isset($content)
        {{ $content }}
    @endisset

    @isset($trigger)
        {{ $trigger }}
    @endisset

</span>
