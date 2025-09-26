@props([
    'name' => null,
    'label' => null,
    'description' => null,
    'placeholder' => '',
])

<mijnui:with-field :$label :$description :$name>
    <textarea
        {{ $attributes->merge([
            'class' =>
                'flex min-h-32 w-full rounded-md border bg-secondary px-3 py-2 text-sm outline-none transition duration-300 placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50',
        ]) }}
        placeholder="{{ $placeholder }}">{{ $slot }}</textarea>
</mijnui:with-field>
