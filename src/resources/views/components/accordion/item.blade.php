@props(['open' => false])

<div x-data="{ open: @json($open) }" class="w-full border-b">
    {{ $slot }}
</div>
