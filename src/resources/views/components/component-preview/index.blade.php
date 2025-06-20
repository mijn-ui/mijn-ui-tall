@props([
    'class' => '',
    'direction' => 'row', // 'row' or 'column'
    'gap' => '4', // Tailwind gap size (e.g., 0, 1, 2, 3, 4, 6, 8)
    'preview' => true,
    'centered' => true,
    'padding' => '20',
    'code' => [],
])

@php
    $base = 'bg-surface p-8 rounded-lg shadow-lg mb-10';

    $direction = in_array($direction, ['row', 'column']) ? $direction : 'row';


    $gapClass = [
        '0' => 'gap-0',
        '1' => 'gap-1',
        '2' => 'gap-2',
        '3' => 'gap-3',
        '4' => 'gap-4',
        '6' => 'gap-6',
        '8' => 'gap-8',
    ][$gap] ?? 'gap-4';


    $containerClass = $direction === 'row'
        ? "flex flex-wrap {$gapClass}"
        : "flex flex-col {$gapClass}";

    $centerClass = $centered ? 'justify-center' : '';
@endphp


<div {{ $attributes->class([$base, $class]) }}>
    <div class="py-{{ $padding }}">
        <div class="{{ $containerClass }} {{ $centerClass }}">
            @foreach ($code as $c)
                @if($direction === 'column')
                    <div class="">
                        @endif

                        {!! Illuminate\Support\Facades\Blade::render($c) !!}

                        @if($direction === 'column')
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    @if($preview)
        <div class="mt-8 text-start">
            <pre class="text-dark p-4 rounded-lg overflow-x-auto">
                <code class="language-html rounded-lg">{{ implode("\n", $code) }}</code>
            </pre>
        </div>
    @endif
</div>
