@props(['id' => 'alert-dialog'])

<div x-data="{ open: false }" {{ $attributes }}>
    {{ $slot }}
</div>
