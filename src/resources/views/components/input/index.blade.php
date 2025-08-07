@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'type' => 'text',
    'variant' => 'default',
    'placeholder' => '',
    'label' => null,
    'description' => null,
    'id' => '',
    'floatingLabel' => null,
    'icon' => null,
    'startIcon' => null,
    'endIcon' => null,
    'strength' => null,
    'viewable' => false,
    'clearable' => false,
    'invalid' => null,
    'wrapperClass' => 'w-full'
])

@php
    $id = $id ?: $name;
    $invalid ??= $name && $errors->has($name);

    $startIcon ??= $icon;
    $hasStartIcon = filled($startIcon);
    $hasEndIcon = filled($endIcon) || $viewable || $clearable;
    $hasFloatingLabel = filled($floatingLabel);

    // Determine variant classes
    $variantClasses = match (true) {
        $variant === 'danger' || $invalid => "border-danger rounded-md border
          focus-visible:border-border-danger-subtle
          focus-visible:outline-none
          focus-visible:ring-2
          focus-visible:ring-primary
          focus-visible:ring-offset-2
          focus-visible:ring-offset-background ring-danger",
        $variant === 'underline' => 'border-b focus-visible:border-b-primary outline-none',
        default
            => "rounded-md border focus-visible:border-border-primary-subtle focus-visible:outline-none focus-visible:ring-2
          focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background ring-primary",
    };

    $inputClasses = collect([
        'peer',
        'bg-secondary flex h-10 w-full px-3 py-2 text-sm',
        'placeholder:text-muted-foreground',
        'file:bg-secondary transition duration-300 file:border-0 file:text-sm file:font-medium',
        $variantClasses,
        $hasStartIcon ? 'pl-8' : 'pl-3',
        $hasEndIcon ? 'pr-8' : 'pr-3',
        $hasFloatingLabel ? 'peer' : '',
    ])->join(' ');
@endphp


<mijnui:with-field :$label :$description :$name>
    <div {{ $attributes->merge(['class' => "relative flex items-center $wrapperClass"]) }} data-mijnui-input>

        {{-- Start Icon --}}
        @if ($hasStartIcon)
            <div class="absolute left-2 top-1/2 -translate-y-1/2 transform text-muted-foreground">
                @if (is_string($startIcon))
                    <i class="{{ $startIcon }}"></i>
                @else
                    {{ $startIcon }}
                @endif
            </div>
        @endif

        {{-- Input --}}
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->except('class') }}
            placeholder="{{ $placeholder ?: ' ' }}" @if ($disabled ?? false) disabled @endif
            class="{{ $inputClasses }}" />

        {{-- End Icon / Viewable / Clearable --}}
        @if ($hasEndIcon)
            <div
                class="absolute right-2 top-1/2 -translate-y-1/2 transform flex items-center gap-1 text-muted-foreground">
                @if ($endIcon)
                    @if (is_string($endIcon))
                        <i class="{{ $endIcon }}"></i>
                    @else
                        {{ $endIcon }}
                    @endif
                @endif

                @if ($viewable)
                    <mijnui:input.viewable />
                @endif

                @if ($clearable)
                    <mijnui:input.clearable />
                @endif
            </div>
        @endif

        {{-- Floating Label --}}
        @if ($hasFloatingLabel)
            <label for="{{ $id }}"
                class="absolute start-2 top-2 z-10 max-w-fit origin-[0] -translate-y-4 scale-75 transform cursor-text px-2 text-sm font-medium text-muted-text duration-300 bg-main
                       peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:scale-100
                       peer-focus:top-2 peer-focus:-translate-y-4 peer-focus:scale-75 peer-focus:px-2
                       rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4">
                {{ $floatingLabel }}
            </label>
        @endif
    </div>

    {{-- Strength Indicator --}}
    @if ($strength)
        <mijnui:strength-indicator :$name />
    @endif
</mijnui:with-field>
