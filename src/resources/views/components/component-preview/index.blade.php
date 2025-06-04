<div class="bg-surface p-8 rounded-lg shadow-lg text-center mb-10">
    <div class="py-20">
        @foreach ($code as $c)
        {!! Illuminate\Support\Facades\Blade::render($c) !!}
        @endforeach
    </div>


    <!-- Code Preview Section -->
    <div class="mt-8 text-start">
        <pre class="text-dark">
        <code class="language-html rounded">{{implode("\n",$code)}}</code></pre>
    </div>
</div>
