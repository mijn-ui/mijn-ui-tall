@props([
    'items' => [],
    'avatars' => [],
    'overflowCount' => null,
])

@php
    $base = 'https://cdn.jsdelivr.net/npm/heroicons@2.2.0/24/outline/';
@endphp

<!-- Kanban Card Footer -->
<div class="flex items-center gap-2 text-muted-text sm:gap-4">
    <!-- Left-aligned items -->
    @if (count($items) > 0)
        @foreach($items as $item)
            <div class="flex items-center gap-1">
                @if (!empty($item['icon']))
                    @php
                        $svgUrl = $base . $item['icon'] . '.svg';
                        $svg = @file_get_contents($svgUrl);
                    @endphp
                    @if ($svg)
                        <span class="w-5 h-5">{!! $svg !!}</span>
                    @else
                        <span class="text-red-500">?</span>
                    @endif
                @endif
                <span class="text-xs">{{ $item['text'] ?? '' }}</span>
            </div>
        @endforeach
    @endif

    <!-- Right-aligned avatars -->
    @if (count($avatars) > 0)
        <div class="flex w-full items-center justify-end -space-x-2">
            @foreach($avatars as $avatar)
            <div
                class="relative flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted text-xs ring-1 ring-muted-text/75">
                @if (isset($avatar['image']))
                    <img alt="avatar" class="h-full w-full object-cover" src="{{ $avatar['image'] }}" />
                @elseif (isset($avatar['initials']))
                    <span>{{ $avatar['initials'] }}</span>
                @endif
            </div>
            @endforeach
            @if ($overflowCount)
                <div class="!ml-1.5 flex items-center justify-center text-xs text-muted-text">+{{ $overflowCount }}</div>
            @endif
        </div>
    @endif
</div>
