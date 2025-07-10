@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'locale' => 'en',
    'placeholder' => 'Pick Date'
])

<script src="{{ asset('vendor/mijnui/js/calendar/dayjs.min.js') }}"></script>

<div 
    x-data="calendarComponent('{{ $locale }}', @entangle($name))" 
    x-init="
        const loadScript = (src) => {
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = src;
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        };

        Promise.all([
            loadScript('{{ asset('vendor/mijnui/js/calendar/localeData.js') }}'),
            loadScript('{{ asset('vendor/mijnui/js/calendar/advancedFormat.js') }}'),
            loadScript('{{ asset("vendor/mijnui/js/calendar/locale/{$locale}.js") }}')
        ]).then(() => {
            dayjs.extend(window.dayjs_plugin_localeData);
            dayjs.extend(window.dayjs_plugin_advancedFormat);
            dayjs.locale('{{ $locale }}');
            $nextTick(() => init());
        }).catch(error => {
            console.error('Error loading dayjs scripts:', error);
        });
    "
    class="w-full relative" id="datepicker-container">
    <mijnui:input
        :placeholder="$placeholder"
        class="w-4 mb-1 cursor-pointer"
        readonly 
        @click="open = !open" 
        x-bind:value="selectedDateValue"
        {{ $attributes->except('wire:model') }}
    />
    <input type="hidden" {{ $attributes->only('wire:model') }}>
    <div x-show="open" x-transition @click.outside="open = false" class="absolute z-50" id="datepicker-calendar">
        <div class="rounded-lg border border-main-border bg-surface p-3">
            <div class="relative flex flex-col sm:flex-row">
                <!-- /* --------------------------- CALENDAR NAV --------------------------- */ -->
                <nav>
                    <button
                        @click="changeMonth(-1)"
                        class="absolute left-1 top-0 z-10 inline-flex h-7 w-7 items-center justify-center gap-1 rounded-md border border-main-border bg-transparent p-0 text-sm opacity-50 transition-colors duration-150 hover:bg-accent hover:text-accent-text hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90"
                        aria-label="Go to the Previous Month">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                            stroke-linejoin="round" class="h-4 w-4" height="1em" width="1em"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </button>
        
                    <button
                        @click="changeMonth(1)"
                        class="absolute right-1 top-0 z-10 inline-flex h-7 w-7 items-center justify-center gap-1 rounded-md border border-main-border bg-transparent p-0 text-sm opacity-50 transition-colors duration-150 hover:bg-accent hover:text-accent-text hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90"
                        aria-label="Go to the Next Month">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                            stroke-linejoin="round" class="h-4 w-4" height="1em" width="1em"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                </nav>
        
                <!-- /* ---------------------------- CALENDAR TITLE --------------------------- */ -->
                <div>
                    <div class="relative flex items-center justify-center py-2">
                        <span class="text-sm font-medium" role="status" x-text="formatMonthYear(displayDate)"></span>
                    </div>
        
                    <!-- /* ---------------------------- CALENDAR TABLE --------------------------- */ -->
                    <table role="grid" x-bind:aria-label="formatMonthYear(displayDate)"
                        class="w-full border-collapse space-y-1">
                        <!-- CALENDAR TABLE HEADER -->
                        <thead>
                            <tr class="flex">
                                <template x-for="day in weekdays" :key="day">
                                    <th 
                                        :aria-label="day"
                                        class="flex h-9 w-9 items-center justify-center text-[0.8rem] font-normal text-muted-text"
                                        scope="col"
                                        x-text="day"
                                        ></th>
                                </template>
                            </tr>
                        </thead>
        
                        <!-- CALENDAR TABLE BODY -->
                        <tbody>
                            <template 
                                x-for="(week, weekIndex) in weeks" 
                                :key="weekIndex"
                            >
                                <tr class="mt-0.5 flex w-full">
                                    <template
                                        x-for="day in week" 
                                        :key="day.date"
                                    >
                                        <td 
                                            :data-day="day.date"
                                            :data-month="day.isLastDayOfMonth ? dayjs(day.date).format('YYYY-MM') : null"
                                            :data-outside="!day.isCurrentMonth ? true : null"
                                            :data-today="day.isToday ? true : null"
                                            :aria-selected="day.isSelected ? true : null"
                                            :data-selected="day.isSelected ? true : null"
                                        >
                                            <button
                                                @click="selectDate(day.date)"
                                                class="relative h-9 w-9 rounded-md p-0 text-center text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main"
                                                :class="[
                                                    !day.isCurrentMonth || dayjs(day.date).year() !== currentYear  ? 'text-muted-text text-muted-text/80 hover:bg-accent' : '',
                                                    day.isToday ? 'bg-primary text-primary-text' : '',
                                                    day.isCurrentMonth && !day.isToday ? 'hover:bg-accent hover:text-accent-text active:brightness-90' : '',
                                                    day.isSelected ? 'border border-primary' : ''
                                                ]"
                                                :aria-label="getArialLabel(day)"
                                                :tabindex="day.isToday ? 0 : null"
                                                x-text="dayjs(day.date).date()"
                                                >
                                            </button>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calendarComponent(locale, wireSelectedDate) {
        return {
            open: false,
            selectedDate: wireSelectedDate,
            selectedDateValue: '',
            currentDate: null,
            displayDate: null,
            weekdays: [],
            weeks: [],
            currentYear: null,

            init() {
                this.currentDate = dayjs();
                this.currentYear = dayjs().year();
                this.displayDate = this.selectedDate && dayjs(this.selectedDate).isValid() ? dayjs(this.selectedDate) : dayjs();

                // Initialize with placeholder (don't auto-select today)
                if (this.selectedDate && dayjs(this.selectedDate).isValid()) {
                    this.selectedDateValue = dayjs(this.selectedDate).format('YYYY-MM-DD');
                    this.displayDate = dayjs(this.selectedDate);
                }

                this.setWeekdays();
                this.generateCalendar();
            },

            setWeekdays() {
                const all = dayjs().localeData().weekdaysMin();
                const firstDay = dayjs().localeData().firstDayOfWeek();
                this.weekdays = [
                    ...all.slice(firstDay), ...all.slice(0, firstDay)
                ];
            },

            formatMonthYear(date) {
                return date.format('MMMM YYYY');
            },

            getArialLabel(day) {
                let label = dayjs(day.date).format('dddd, MMMM Do, YYYY');
                if (day.isToday) label = 'Today, ' + label;
                if (day.isSelected) label += ', selected';
                return label
            },

            changeMonth(offset) {
                this.displayDate = this.displayDate.add(offset, 'month');
                this.generateCalendar();
            },

            generateCalendar() {
                const firstDayOfWeek = dayjs().localeData().firstDayOfWeek();
                const start = this.displayDate.startOf('month');
                const end = this.displayDate.endOf('month');

                let startDate = start.subtract((7 + start.day() - firstDayOfWeek) % 7, 'day');
                let endDate = end.add((6 - ((end.day() - firstDayOfWeek + 7) % 7)), 'day');

                const today = dayjs();
                const selected = this.selectedDate ? dayjs(this.selectedDate) : null;

                const days = [];
                let date = startDate;

                while (date.isBefore(endDate) || date.isSame(endDate, 'day')) {
                    days.push({
                        date: date.format('YYYY-MM-DD'),
                        isCurrentMonth: date.month() === this.currentDate.month(),
                        isToday: date.isSame(today, 'day'),
                        isSelected: selected && date.isSame(selected, 'day'),
                        isLastDayOfMonth: date.isSame(date.endOf('month'), 'day')
                    });
                    date = date.add(1, 'day');
                }

                this.weeks = [];
                for (let i = 0; i < days.length; i += 7) {
                    this.weeks.push(days.slice(i, i + 7));
                }
            },

            selectDate(dateStr) {
                this.selectedDate = dateStr;
                this.selectedDateValue = dateStr;
                this.generateCalendar();
                this.open = false;
                // this.$wire.set(selectedDate, dateStr);
            }
        }
    }
</script>
