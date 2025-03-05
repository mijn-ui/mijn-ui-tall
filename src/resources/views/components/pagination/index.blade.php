@props([
    'data' => [],
    'hasLinks' => true,
    'hasGuide' => true,
    'hasPerPage' => true,
])

<nav {{ $attributes->merge([
    'class' => 'flex flex-col items-center justify-end gap-2 space-x-2 py-2 sm:flex-row',
]) }}>

    @if ($hasGuide)
        <p class="flex-1 text-sm text-muted-foreground">
            Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results
        </p>
    @endif

    @if ($hasLinks)

            <mijnui:pagination.previous current="{{ $data->currentPage() }}" />
            @if ($data->lastPage() > 1)
                <mijnui:pagination.link page="1" current="{{ $data->currentPage() }}" />

                @if ($data->currentPage() > 3)
                    <mijnui:pagination.eclipse />
                @endif

                <ul class="flex flex-row items-center gap-1">
                @for ($i = max(2, $data->currentPage() - 1); $i <= min($data->lastPage() - 1, $data->currentPage() + 1); $i++)
                    <li>
                    <mijnui:pagination.link page="{{ $i }}" current="{{ $data->currentPage() }}" />
                    </li>
                @endfor
                </ul>

                @if ($data->currentPage() < $data->lastPage() - 2)
                    <mijnui:pagination.eclipse />
                @endif

                @if ($data->lastPage() > 1)
                    <mijnui:pagination.link page="{{ $data->lastPage() }}" current="{{ $data->currentPage() }}" />
                @endif
            @endif
            <mijnui:pagination.next current="{{ $data->currentPage() }}" lastPage="{{ $data->lastPage() }}" />
    @endif


</nav>

<script>
    function changePerPage(page) {
        let url = new URL(window.location.href);
        url.searchParams.set('perPage', page);
        window.location.href = url.toString();
    }

    function changePage(page) {
        let url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }
</script>
