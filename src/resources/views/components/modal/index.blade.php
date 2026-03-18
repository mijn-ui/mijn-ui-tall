@props([])

<span x-data="{ open: false, modalId: 'modal-' + Math.random().toString(36).substr(2, 9) }" {{$attributes}}>

    @isset($content)
        {{ $content }}
    @endisset

    @isset($trigger)
        {{ $trigger }}
    @endisset

</span>
