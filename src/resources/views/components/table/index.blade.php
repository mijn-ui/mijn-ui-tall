@props([
    'paginate' => null,
    'perPage' => null,
])

<div>
    <div class = 'relative w-full overflow-auto rounded-2xl border border-main-border bg-surface'>
        <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-main-text rtl:text-right']) }}>
            {{ $slot }}
        </table>
    </div>

    <?php if($paginate) :?>
    <mijnui:pagination :data="$paginate" :$perPage />
    <?php endif;?>
</div>
