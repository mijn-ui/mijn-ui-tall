@props(['paginate'])

<div>
    <div class="relative w-full overflow-auto rounded-2xl border border-main-border bg-surface">
        <table class="w-full text-left text-sm text-main-text rtl:text-right">
            {{ $slot }}
        </table>


    </div>
    <mijnui:pagination :data="$paginate" />
</div>
