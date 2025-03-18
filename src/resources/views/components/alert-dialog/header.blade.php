@props(['title'])

<h2 id="-title" class="text-lg font-semibold">
    {{ $title ?? $slot }}
</h2>
