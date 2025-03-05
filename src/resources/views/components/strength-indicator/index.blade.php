@props([
    'strength' => '',
    'name' => null,
    'errors' => null,
])

@php
    $errors = $errors ?? ($name ? $errors->get($name) : []);

        $strengthLevel = '';

    if (empty($errors)) {
        $strengthLevel = 'verystrong';
    } else {
        $missingRules = count($errors);

               $strengthLevel = match (true) {
            $missingRules >= 4 => 'weak', // 4 or more missing rules (e.g., empty field)
            $missingRules == 3 => 'weak', // 3 missing rules
            $missingRules == 2 => 'medium', // 2 missing rules
            $missingRules == 1 => 'medium', // 1 missing rule
            default => '', // Fallback (should not happen)
        };
    }


    $baseClasses = 'flex h-1 w-full min-w-40 max-w-xs items-center gap-1';

    $strengthClasses = [
        '' => ['bg-gray-300', 'bg-gray-300', 'bg-gray-300', 'bg-gray-300'],
        'weak' => ['bg-red-500', 'bg-gray-300', 'bg-gray-300', 'bg-gray-300'],
        'medium' => ['bg-yellow-500', 'bg-yellow-500', 'bg-gray-300', 'bg-gray-300'],
        'strong' => ['bg-yellow-500', 'bg-yellow-500', 'bg-yellow-500', 'bg-gray-300'],
        'verystrong' => ['bg-green-500', 'bg-green-500', 'bg-green-500', 'bg-green-500'],
    ][$strengthLevel];
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }}>
    @foreach ($strengthClasses as $class)
        <div class="size-full rounded-full {{ $class }}"></div>
    @endforeach
</div>
