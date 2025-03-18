@props(['description'])

<p id="-description" class="text-sm text-gray-600">
    {{ $description ?? $slot }}
</p>
