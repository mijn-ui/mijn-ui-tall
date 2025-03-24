<div class="relative" x-data="{ dropdownOpen: false }">

    @isset($trigger)
        <div class="relative w-fit" x-on:click.outside="dropdownOpen = false">
            {{ $trigger }}
            @isset($content)
                {{ $content }}
            @endisset
        </div>
    @endisset

</div>
