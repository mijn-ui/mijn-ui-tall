<div class="relative">
    <button
        @click="open = false"
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
        aria-label="Close"
    >
        &times;
    </button>

    {{ $slot }}
</div>
<?php
