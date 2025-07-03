@props([
    'title' => '',
    'tags' => [],
    'progress' => 0,
    'progressText' => null,
    'progressColor' => 'primary',
    'showProgress' => true,
])

<h5 class="w-10/12 text-sm font-medium">{{ $title }}</h5>

@if (count($tags) > 0)
<div class="flex flex-wrap">
    @foreach ($tags as $tag)
        <span class="inline-flex items-center justify-center rounded-full border px-2.5 py-0.5 text-xs hover:bg-accent">
            {{ $tag }}
        </span>
    @endforeach
</div>
@endif

<!-- Progress Section -->
@if ($showProgress)
<div class="space-y-1">
    <div class="flex items-center justify-between text-xs text-muted-text">
        <h5>CheckList</h5>
        <p>{{ $progressText ?? "{$progress}%" }}</p>
    </div>
    <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted">
        <div class="h-full bg-{{ $progressColor }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}" role="progressbar" style="transform: scaleX({{ $progress / 100 }}); transform-origin: left center"></div>
    </div>
</div>
@endif
