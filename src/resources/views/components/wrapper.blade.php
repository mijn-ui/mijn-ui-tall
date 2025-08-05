<div class="relative min-h-screen flex">

    @isset($sidebar)
        {{ $sidebar }}
    @endisset

    <div class="flex-1 ">
        @isset($header)
            {{ $header }}
        @endisset
        <div class="pl-4">
            {{ $slot }}
        </div>
    </div>


</div>
