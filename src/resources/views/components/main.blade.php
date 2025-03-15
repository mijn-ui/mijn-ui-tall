<main x-data :class="$store.sidebar.isOpen ? 'pl-52' : 'pl-0'"
       class="pt-14 transition-all duration-300">
    {{$slot}}
</main>
