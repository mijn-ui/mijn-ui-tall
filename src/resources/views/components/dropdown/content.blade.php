<x-slot:content>
    <div {{ $attributes }}>
        @isset($header)
            <div class="px-2 py-1.5 text-sm font-semibold">
                {{ $header }}
            </div>
        @endisset

        <div>
            {{$slot}}
        </div>
    </div>

</x-slot:content>
