@props([
    'data' => [],
    'hasLinks' => true,
    'hasGuide' => true,
    'hasPerPage' => true,
    'perPage' => null,
    'perPageOptions' => ['10', '25', '50', '100', '200'],
])

@php

    if (!method_exists($data, 'firstItem')) {
        throw new \Exception('data for pagination must be type of Illuminate\Pagination\LengthAwarePaginator');
    }

    $c_page = request()->query('page') ?? 1;
    $c_perPage = request()->query('perPage') ?? $perPage;

@endphp

<nav {{ $attributes->merge([
    'class' => 'flex flex-col items-center justify-between gap-2 py-2 sm:flex-row',
]) }}>

    <?php if($hasGuide): ?>
    <p class="text-sm text-muted-foreground">
        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results
    </p>
    <?php endif;?>

    <?php if($hasLinks && $data->hasPages()): ?>

    <div class="flex items-center gap-2">
        <mijnui:pagination.previous current="{{ $data->currentPage() }}" />
        <?php if($data->lastPage() > 1): ?>
            <mijnui:pagination.link page="1" current="{{ $data->currentPage() }}" />

            <?php if($data->currentPage() > 3):?>
            <mijnui:pagination.eclipse />
            <?php endif;?>

            <ul class="flex flex-row items-center gap-1">
                @for ($i = max(2, $data->currentPage() - 1); $i <= min($data->lastPage() - 1, $data->currentPage() + 1); $i++)
                    <li>
                        <mijnui:pagination.link page="{{ $i }}" current="{{ $data->currentPage() }}" />
                    </li>
                @endfor
            </ul>

            <?php if($data->currentPage() < $data->lastPage() - 2): ?>
            <mijnui:pagination.eclipse />
            <?php endif; ?>

            <?php if ($data->lastPage() > 1): ?>
            <mijnui:pagination.link page="{{ $data->lastPage() }}" current="{{ $data->currentPage() }}" />
            <?php endif; ?>
        <?php endif;?>
        <mijnui:pagination.next current="{{ $data->currentPage() }}" lastPage="{{ $data->lastPage() }}" />
    </div>

    <?php endif;?>

    <?php if($hasPerPage): ?>
    {{-- <select wire:model.live="perPage">
        @foreach ($perPageOptions as $perPageOption)
            <option value="{{ $perPageOption }}">{{ $perPageOption }}</option>
        @endforeach
    </select> --}}
    <mijnui:select wire:model.live="perPage"
        x-on:change="
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            params.set('perPage', $el.value)
            window.history.pushState({}, '', `${url.pathname}?${params.toString()}`);
        ">
        @foreach ($perPageOptions as $perPageOption)
            <mijnui:select.option value="{{ $perPageOption }}">{{ $perPageOption }}</mijnui:select.option>
        @endforeach
    </mijnui:select>
    <?php endif;?>

</nav>
