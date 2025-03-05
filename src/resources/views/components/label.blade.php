@props(['for' => null])

<label for="{{ $for }}" class="text-sm">
    {{ $slot }}
</label>
