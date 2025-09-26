@props([
    'class' => '',
    'direction' => 'row', // 'row' or 'column'
    'gap' => '4', // Tailwind gap (e.g., 0, 1, 2, 3, 4, 6, 8)
    'preview' => true,
    'centered' => true,
    'padding' => '20',
    'code' => [],
    'align' => 'start', // 'start', 'center', 'end'
])

@php
    $base = 'bg-surface p-8 rounded-lg shadow-lg mb-10';

    $validDirection = in_array($direction, ['row', 'column']) ? $direction : 'row';

    $gapClass =
        [
            '0' => 'gap-0',
            '1' => 'gap-1',
            '2' => 'gap-2',
            '3' => 'gap-3',
            '4' => 'gap-4',
            '6' => 'gap-6',
            '8' => 'gap-8',
        ][$gap] ?? 'gap-4';

    $containerClass = "flex $gapClass " . ($validDirection == 'row' ? 'flex-wrap' : 'flex-col items-center');

    $justifyClass = $centered ? 'justify-center' : '';

    $alignClass =
        [
            'start' => 'items-start',
            'center' => 'items-center',
            'end' => 'items-end',
        ][$align] ?? 'items-start';

    $combinedClass = "$containerClass $justifyClass $alignClass";
@endphp

<div {{ $attributes->class([$base, $class]) }}>
    <mijnui:tab defaultValue="preview">
        <mijnui:tab.list>
            <mijnui:tab.item value="preview">Preview</mijnui:tab.item>
            <mijnui:tab.item value="code">Code</mijnui:tab.item>
        </mijnui:tab.list>

        {{-- Preview Content --}}
        <mijnui:tab.content value="preview">
            <div class="py-{{ $padding }} px-4 border rounded-lg mt-4">
                <div class="{{ $combinedClass }}">
                    @if (is_array($code))
                        @foreach ($code as $c)
                            {!! \Illuminate\Support\Facades\Blade::render($c) !!}
                        @endforeach
                    @else
                        {!! \Illuminate\Support\Facades\Blade::render($code) !!}
                    @endif
                </div>
            </div>
        </mijnui:tab.content>

        {{-- Code View Content --}}
        <mijnui:tab.content value="code">
            @if ($preview)
                <div class="mt-8 text-start relative" x-data="{ copied: false }">
                    <button
                        x-on:click="
                navigator.clipboard.writeText($el.nextElementSibling.querySelector('code').innerText);
                copied = true;
                setTimeout(() => copied = false, 2000);"
                        class="absolute right-8 top-4 p-2 hover:bg-gray-900/70 rounded-md text-sm focus:outline-none transition"
                        title="Copy to clipboard">
                        <template x-if="!copied">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                        </template>
                        <template x-if="copied">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-green-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </button>
                    <pre
                        class="p-4 overflow-x-auto rounded-lg text-dark max-h-96 overflow-y-auto scrollbar scrollbar-thumb-gray-300 scrollbar-track-gray-100 scrollbar-w-2 scrollbar-h-2"><code class="rounded-lg language-html">{{ is_array($code) ? implode("\n" . "\n", $code) : $code }}</code></pre>
                </div>
            @endif
        </mijnui:tab.content>
    </mijnui:tab>
</div>
