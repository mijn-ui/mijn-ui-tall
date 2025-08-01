@props([
    'placeholder' => 'Type a command or search...',
    'groups' => [],
])

<div
    x-data="{
        query: '',
        groups: @js($groups),
        selectedIndex: -1,
        flatItems() {
            return this.filteredGroups.flatMap(g => g.items)
        },
        get filteredGroups() {
            const q = this.query.toLowerCase()
            return this.groups
                .map(group => {
                    const items = group.items.filter(item => item.toLowerCase().includes(q))
                    return items.length ? { heading: group.heading, items } : null
                })
                .filter(Boolean)
        },
        get filteredItems() {
            return this.filteredGroups.flatMap(group => group.items)
        },
        selectItem(item) {
            alert(`You selected: ${item}`)
        },
        onKeyDown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault()
                this.selectedIndex = (this.selectedIndex + 1) % this.filteredItems.length
                this.scrollToSelected()
            }
            else if (e.key === 'ArrowUp') {
                e.preventDefault()
                this.selectedIndex = (this.selectedIndex - 1 + this.filteredItems.length) % this.filteredItems.length
                this.scrollToSelected()
            }
            else if (e.key === 'Enter' && this.selectedIndex >= 0) {
                this.selectItem(this.filteredItems[this.selectedIndex])
            }
        },
        scrollToSelected() {
            this.$nextTick(() => {
                const el = this.$refs[`item-${this.selectedIndex}`]
                if (el) el.scrollIntoView({ block: 'nearest' })
            })
        }
    }"
    @keydown.window.prevent.stop="onKeyDown($event)"
    class="w-full max-w-md rounded-md border bg-background-alt shadow-md
           text-foreground overflow-hidden [&_h4.cmdk-group-heading]:text-muted-foreground
           [&_h4.cmdk-group-heading]:px-2 [&_h4.cmdk-group-heading]:py-1.5
           [&_h4.cmdk-group-heading]:text-xs [&_h4.cmdk-group-heading]:font-medium"
>
    <!-- Input -->
    <div class="p-2 border-b border-border">
        <input
            type="text"
            x-model="query"
            :placeholder="placeholder"
            class="w-full px-3 py-2 text-sm border border-border rounded text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
            autocomplete="off"
            spellcheck="false"
            @keydown.arrow-down.prevent="$event.target.dispatchEvent(new KeyboardEvent('keydown', {key:'ArrowDown'}))"
            @keydown.arrow-up.prevent="$event.target.dispatchEvent(new KeyboardEvent('keydown', {key:'ArrowUp'}))"
            @keydown.enter.prevent="$event.target.dispatchEvent(new KeyboardEvent('keydown', {key:'Enter'}))"
        />
    </div>

    <!-- List -->
    <ul class="max-h-60 overflow-y-auto">
        <template x-if="filteredItems.length === 0">
            <li class="p-3 text-sm text-muted-foreground select-none">No results found.</li>
        </template>

        <template x-for="(group, groupIndex) in filteredGroups" :key="group.heading">
            <li class="p-1">
                <h4
                    class="text-xs text-muted-foreground font-medium"
                    x-text="group.heading"
                ></h4>

                <template x-for="(item, itemIndex) in group.items" :key="item">
                    <li
                        x-ref="'item-' + (filteredGroups.slice(0, groupIndex).reduce((acc,g) => acc + g.items.length, 0) + itemIndex)"
                        @click="selectItem(item)"
                        class="relative flex cursor-default select-none items-center px-2 py-1.5 text-sm outline-none
                               data-[selected='true']:bg-secondary
                               data-[disabled]:pointer-events-none data-[disabled='true']:opacity-50"
                        :data-selected="(filteredGroups.slice(0, groupIndex).reduce((acc,g) => acc + g.items.length, 0) + itemIndex) === selectedIndex"
                        tabindex="-1"
                        x-text="item"
                    ></li>
                </template>

                <template x-if="groupIndex !== filteredGroups.length - 1">
                    <hr class="my-2 border-t border-border" />
                </template>
            </li>
        </template>
    </ul>
</div>
