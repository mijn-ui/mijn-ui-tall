@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'locale' => 'en',
    'placeholder' => 'Pick Date',
    'displayFormat' => 'DD/MM/YYYY',
    'range' => false,
    'disabledDates' => [],
    'clearable' => true
])

@php
    $allowedFormats = [
        'YYYY-MM-DD', 'DD/MM/YYYY', 'MM-DD-YYYY', 'MM/DD/YYYY',
        'DD-MM-YYYY', 'YYYY/MM/DD', 'YYYY.MM.DD', 'DD.MM.YYYY',
        'MM.DD.YYYY', 'DD MMM YYYY', 'MMM DD, YYYY', 'MMMM D, YYYY',
        'YYYYMMDD', 'DDMMYYYY'
    ];
    $allowedLocales = ['en', 'my'];

    $errors = [];

    if (!in_array($displayFormat, $allowedFormats)) {
        $errors[] = "Invalid displayFormat '{$displayFormat}', falling back to 'YYYY-MM-DD'.";
        $displayFormat = 'YYYY-MM-DD';
    }

    if (!in_array($locale, $allowedLocales)) {
        $errors[] = "Invalid locale '{$locale}', falling back to 'en'.";
        $locale = 'en';
    }

    $disabledDates = array_filter($disabledDates, fn($d) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $d));
@endphp

<script src="{{ asset('vendor/mijnui/js/calendar/dayjs.min.js') }}"></script>

<div
    x-data="calendarComponent({
        locale: '{{ $locale }}',
        range: {{ $range ? 'true' : 'false' }},
        displayFormat: '{{ $displayFormat }}',
        disabledDates: @json($disabledDates),
        wireValue: @entangle($name),
        clearable: {{ $clearable }}
    })"
    x-init="initComponent()"
    x-cloak
    class="w-full relative"
>
    @if ($errors)
        <div class="text-red-500 text-xs mt-1 space-y-1">
            @foreach ($errors as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <!-- Input Trigger -->
    <div class="relative">
        <input id="{{ $name }}" type="text"
               @click="open = !open"
               x-bind:value="displayValue"
               placeholder="{{ $placeholder ?: ' ' }}" 
               class="rounded-md border focus-visible:border-border-primary-subtle focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background ring-primary peer bg-secondary flex h-10 w-full px-3 py-2 text-sm placeholder:text-muted-foreground transition duration-300" 
        />

        <!-- Clear Button -->
        <button
            type="button"
            x-show="clearable && (selected || startDate)"
            @click="clearSelection()"
            class="absolute right-2 top-2 text-gray-500 hover:text-gray-700"
        >
            ✕
        </button>
    </div>

    <!-- Hidden input for Livewire -->
    <input type="hidden"
           name="{{ $name }}"
           x-bind:value="range ? JSON.stringify([startDate, endDate]) : selected">

    <!-- Calendar Dropdown -->
    <div x-show="open" x-transition @click.outside="open = false"
         class="absolute top-full left-0 mt-1 w-full min-h-[220px] max-w-[280px] z-[9999]">
        <template x-if="isInitialized">
            <div class="rounded-lg border bg-surface p-3 shadow-xl bg-gray-100 mt-1">
                <!-- Navigation -->
                <nav class="flex items-center justify-between w-full mb-2">
                    <button @click="changeMonth(-1)" type="button"
                            class="h-7 w-7 flex items-center justify-center rounded-md border hover:bg-accent">‹</button>
                    <div class="text-sm font-medium" x-text="formatMonthYear(displayDate)"></div>
                    <button @click="changeMonth(1)" type="button"
                            class="h-7 w-7 flex items-center justify-center rounded-md border hover:bg-accent">›</button>
                </nav>

                <!-- Weekdays -->
                <div class="flex w-full">
                    <template x-for="day in weekdays" :key="day">
                        <div class="flex-1 text-center text-xs text-muted-text" x-text="day"></div>
                    </template>
                </div>

                <!-- Calendar Grid -->
                <template x-for="(week, wIndex) in weeks" :key="wIndex">
                    <div class="flex w-full mt-0.5">
                        <template x-for="day in week" :key="day.date">
                            <div :class="dayWrapperClasses(day)">
                                <button type="button" @click="selectDate(day)" :disabled="day.disabled"  :class="dayButtonClasses(day)"
                                        :aria-label="ariaLabel(day)"
                                        x-text="dayjs(day.date).date()">
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

<script>
function calendarComponent({ locale, range, displayFormat, disabledDates, wireValue, clearable }) {
    return {
        open: false,
        locale,
        range,
        displayFormat,
        disabledDates,
        wireValue,
        clearable,
        isInitialized: false,

        currentDate: null,
        displayDate: null,
        weeks: [],
        weekdays: [],
        selected: null,
        startDate: null,
        endDate: null,
        displayValue: '',

        async initComponent() {
            await this.initDayjs();
            this.currentDate = dayjs();
            this.displayDate = this.currentDate;
            this.initCalendar();
            this.isInitialized = true;
            console.log(this.clearable)
        },

        async initDayjs() {
            const load = src => new Promise((res, rej) => {
                const s = document.createElement('script');
                s.src = src;
                s.onload = res;
                s.onerror = rej;
                document.head.appendChild(s);
            });

            await Promise.all([
                load('/vendor/mijnui/js/calendar/localeData.js'),
                load('/vendor/mijnui/js/calendar/advancedFormat.js'),
                load(`/vendor/mijnui/js/calendar/locale/${this.locale}.js`)
            ]);

            dayjs.extend(window.dayjs_plugin_localeData);
            dayjs.extend(window.dayjs_plugin_advancedFormat);
            dayjs.locale(this.locale);
        },

        initCalendar() {
            if (this.range && this.wireValue && Array.isArray(this.wireValue)) {
                [this.startDate, this.endDate] = this.wireValue;
            } else if (this.wireValue) {
                this.selected = this.wireValue;
            }

            this.setWeekdays();
            this.generateCalendar();
            this.updateDisplayValue();
        },

        setWeekdays() {
            const all = dayjs().localeData().weekdaysMin();
            const firstDay = dayjs().localeData().firstDayOfWeek();
            this.weekdays = [...all.slice(firstDay), ...all.slice(0, firstDay)];
        },

        formatMonthYear(date) {
            return dayjs(date).format('MMMM YYYY');
        },

        ariaLabel(day) {
            let label = dayjs(day.date).format('dddd, MMMM Do, YYYY');
            if (day.isToday) label = 'Today, ' + label;
            if (day.isSelected) label += ', selected';
            return label;
        },

        changeMonth(offset) {
            this.displayDate = dayjs(this.displayDate).add(offset, 'month');
            this.generateCalendar();
        },

        generateCalendar() {
            const firstDay = dayjs().localeData().firstDayOfWeek();
            const start = dayjs(this.displayDate).startOf('month');
            const end = dayjs(this.displayDate).endOf('month');

            let startDate = start.subtract((7 + start.day() - firstDay) % 7, 'day');
            let endDate = end.add((6 - ((end.day() - firstDay + 7) % 7)), 'day');

            const today = dayjs();
            const days = [];
            let date = startDate;

            while (date.isBefore(endDate) || date.isSame(endDate, 'day')) {
                const formatted = date.format('YYYY-MM-DD');
                days.push({
                    date: formatted,
                    isToday: date.isSame(today, 'day'),
                    isCurrentMonth: date.month() === dayjs(this.displayDate).month(),
                    disabled: this.disabledDates.includes(formatted),
                    isSelected: this.isSelected(formatted),
                    inRange: this.inRange(formatted)
                });
                date = date.add(1, 'day');
            }

            this.weeks = [];
            for (let i = 0; i < days.length; i += 7) {
                this.weeks.push(days.slice(i, i + 7));
            }
        },

        isSelected(date) {
            if (this.range) return date === this.startDate || date === this.endDate;
            return date === this.selected;
        },

        inRange(date) {
            if (!this.range || !this.startDate || !this.endDate) return false;
            return dayjs(date).isAfter(this.startDate, 'day') && dayjs(date).isBefore(this.endDate, 'day');
        },

        selectDate(day) {
            if (day.disabled) return;

            if (this.range) {
                if (!this.startDate || (this.startDate && this.endDate)) {
                    this.startDate = day.date;
                    this.endDate = null;
                } else {
                    this.endDate = day.date;
                    if (dayjs(this.endDate).isBefore(this.startDate, 'day')) {
                        [this.startDate, this.endDate] = [this.endDate, this.startDate];
                    }
                    this.open = false; 
                }
                this.wireValue = [this.startDate, this.endDate].filter(Boolean);
            } else {
                this.selected = day.date;
                this.wireValue = this.selected;
                this.open = false;
            }

            this.updateDisplayValue();
            this.generateCalendar();
        },

        updateDisplayValue() {
            if (this.range && this.startDate) {
                this.displayValue = this.endDate
                    ? `${dayjs(this.startDate).format(this.displayFormat)} – ${dayjs(this.endDate).format(this.displayFormat)}`
                    : dayjs(this.startDate).format(this.displayFormat);
            } else if (this.selected) {
                this.displayValue = dayjs(this.selected, 'YYYY-MM-DD').format(this.displayFormat);
            } else {
                this.displayValue = '';
            }
        },

        clearSelection() {
            if (this.range) {
                this.startDate = null;
                this.endDate = null;
                this.wireValue = [];
            } else {
                this.selected = null;
                this.wireValue = null;
            }
            this.updateDisplayValue();
        },

        dayWrapperClasses(day) {
            const d = day.date;
            const isStart = this.range && this.startDate === d;
            const isEnd = this.range && this.endDate === d;
            const isIn = this.range && this.startDate && this.endDate && dayjs(d).isAfter(this.startDate, 'day') && dayjs(d).isBefore(this.endDate, 'day');

            const classes = ['flex-1 flex items-center justify-center'];

            if (isStart || isEnd || isIn) classes.push('bg-gray-200');

            if (isStart && isEnd) classes.push('rounded-full');
            else if (isStart) classes.push('rounded-l-full');
            else if (isEnd) classes.push('rounded-r-full');

            return classes.join(' ').trim();
        },

        dayButtonClasses(day) {
            const d = day.date;
            const isStart = this.range && this.startDate === d;
            const isEnd = this.range && this.endDate === d;
            const isSingleSelected = !this.range && day.isSelected;

            const classes = ['h-9 w-9 flex items-center justify-center text-sm'];

            if (day.disabled) classes.push('text-muted-text opacity-50 cursor-not-allowed bg-transparent');
            else if (isStart || isEnd || isSingleSelected) classes.push('bg-primary text-white');
            else classes.push('bg-transparent');

            classes.push('rounded-lg');
            if (day.isToday) classes.push('ring-1 ring-primary');

            return classes.join(' ').trim();
        }
    }
}
</script>
