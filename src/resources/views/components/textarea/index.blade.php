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
                'border-input disabled:opacity-disabled flex min-h-[80px] w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-muted-text focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed',
        ]) }}
        placeholder="{{ $placeholder }}">{{ $slot }}</textarea>
</mijnui:with-field>
