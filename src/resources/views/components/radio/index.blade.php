@props([
    'id' => 'radio-button-' . Str::random(6),
    'name' => 'example',
    'value' => '',
    'checked' => false,
    'label' => 'Option',
])

<!-- RadioGroup Item -->
<div class="flex items-center gap-2">
    <!-- RadioGroup Item Box -->
    <div class="inline-flex items-center">
        <label class="relative flex cursor-pointer items-center rounded-full p-0" for="{{ $id }}">
            <input
                type="radio"
                class="before:content[''] peer relative h-5 w-5 cursor-pointer appearance-none rounded-full border border-main-text text-primary transition-all before:absolute before:left-2/4 before:top-2/4 before:block before:h-12 before:w-12 before:-translate-x-2/4 before:-translate-y-2/4 before:rounded-full before:opacity-0 before:transition-opacity checked:border-primary checked:before:bg-primary hover:before:opacity-0"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $value }}"
                {{ $checked ? 'checked' : '' }}

            />
            <span
                class="pointer-events-none absolute left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 text-primary opacity-0 transition-opacity peer-checked:opacity-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="currentColor">
                    <circle data-name="ellipse" cx="8" cy="8" r="8"></circle>
                </svg>
            </span>
        </label>
    </div>
    <!-- RadioGroup Item Label -->
    <label class="text-sm" for="{{ $id }}">{{ $label }}</label>
</div>
