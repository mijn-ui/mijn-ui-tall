<div class="relative" x-data="{ dropdownOpen: false }" x-on:click.outside="dropdownOpen = false">
    @isset($trigger)
        {{ $trigger }}
    @endisset
    @isset($content)
        {{ $content }}
    @endisset

</div>
