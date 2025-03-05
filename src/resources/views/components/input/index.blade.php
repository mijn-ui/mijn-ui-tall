@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'class' => '',
    'type' => 'text',
    'variant' => 'default',
    'placeholder' => '',
    'label' => null,
    'description' => null,
    'id' => '',
    'floatingLabel' => null,
    'icon' => null,
    'iconStart' => null,
    'iconEnd' => null,
    'strength' => null,
    'viewable' => null,
    'clearable' => null,
    'invalid' => null,
])

@php
    // Error handling
    $invalid ??= ($name && $errors->has($name));

    // Icon handling
    $iconStart ??= $icon;
    $hasStartIcon = !empty($iconStart);
    $hasEndIcon = !empty($iconEnd) || $viewable;
    $hasFloatingLabel = !empty($floatingLabel);

    // Base input classes
    $baseClasses = "flex h-10 w-full rounded-md border bg-transparent px-3 py-2 text-sm outline-none placeholder:text-muted-text autofill:shadow-[inset_0_0_0px_1000px_hsl(var(--surface))] autofill:[-webkit-text-fill-color:hsl(hsl(--main-text))_!important] focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring ";
    $baseClasses .= $invalid ? "border-danger bg-main focus-visible:ring-danger focus-visible:ring-offset-2 focus-visible:ring-offset-main" : "";

    // Dynamic classes for icons and floating label
    $inputClasses = $baseClasses .
        ($hasStartIcon ? ' pl-8' : ' pl-3') .
        ($hasEndIcon ? ' pr-8' : ' pr-3') .
        ($hasFloatingLabel ? ' peer' : '');
@endphp

<mijnui:with-field :$label :$description :$name>
    <div class="relative flex items-center" data-mijnui-input>
        <!-- Start Icon -->
        @if($hasStartIcon)
            <div class="absolute left-2 top-1/2 -translate-y-1/2 transform">
                @if(is_string($iconStart))
                    <i class="{{ $iconStart }}"></i>
                @else
                    {{ $iconStart }}
                @endif
            </div>
        @endif

        <!-- Input Field -->
        <input
            {{ $attributes->merge(['class' => $inputClasses]) }}
            @isset($name) name="{{ $name }}" @endisset
            id="{{ $id }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
        />

        <!-- End Icon -->
        @if($hasEndIcon)
            <div class="absolute right-2 top-1/2 -translate-y-1/2 transform">
                @if(is_string($iconEnd))
                    <i class="{{ $iconEnd }}"></i>
                @else
                    {{ $iconEnd }}
                @endif
            </div>
        @endif

        <!-- Viewable Toggle -->
        @if($viewable)
            <div class="absolute right-2 top-1/2 -translate-y-1/2 transform">
                <mijnui:input.viewable />
            </div>
        @endif

        <!-- Clearable Button -->
        @if($clearable)
            <div class="absolute right-2 top-1/2 -translate-y-1/2 transform">
                <mijnui:input.clearable />
            </div>
        @endif

        <!-- Floating Label -->
        @if($hasFloatingLabel)
            <label
                for="{{ $id }}"
                class="absolute start-2 top-2 z-10 max-w-fit origin-[0] -translate-y-4 scale-75 transform cursor-text px-2 text-sm font-medium text-muted-text duration-300 bg-main peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:scale-100 peer-focus:top-2 peer-focus:-translate-y-4 peer-focus:scale-75 peer-focus:px-2 rtl:peer-focus:left-auto rtl:peer-focus:translate-x-1/4"
            >
                {{ $floatingLabel }}
            </label>
        @endif
    </div>

    <!-- Strength Indicator -->
    @if(!empty($strength))
        <mijnui:strength-indicator :$name />
    @endif
</mijnui:with-field>
