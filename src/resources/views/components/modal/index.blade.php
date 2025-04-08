@props(['name'])

<div x-data="{ open: false }">

    @isset($content)
        {{ $content }}
    @endisset

    @isset($trigger)
        {{ $trigger }}
    @endisset

</div>
