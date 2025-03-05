@props([
    'label' => '',
    'description' => '',
    ])



<div>
    <div class="flex items-center gap-2">
        <div class="inline-flex items-center gap-2">
            <label for="checkbox" class="relative flex items-center">
                <input
                    id="checkbox"
                    class="before:content[''] peer relative h-5 w-5 cursor-pointer appearance-none rounded-[4px] border border-main-text transition-all checked:border-primary checked:bg-primary"
                    type="checkbox"
                    checked
                />
                <span
                    class="pointer-events-none absolute left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 opacity-0 transition-opacity peer-checked:opacity-100"
                >
                  <!-- checkbox Icon -->
                  <svg
                      class="text-primary-text"
                      stroke="currentColor"
                      fill="none"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="h-4 w-4"
                      height="1em"
                      width="1em"
                      xmlns="http://www.w3.org/2000/svg"
                  >
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
        </span>
            </label>
        </div>
        <?php if (is_string($label) && $label !== '' ): ?>
        <label
            for="checkbox"
            class="font-sans font-medium leading-none text-main-text"
        >
            {{ $label }}
        </label>
        <?php endif; ?>
    </div>
    <?php if (is_string($description) && $description !== '' ): ?>
    <p class="pl-7 text-sm text-muted-text">
        {{ $description }}
    </p>
    <?php endif; ?>
</div>
