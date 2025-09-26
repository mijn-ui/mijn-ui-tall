<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">

    @isset($trigger)
        {{ $trigger }}
    @endisset
    
    @isset($content)
        {{ $content }}
    @endisset

</div>
