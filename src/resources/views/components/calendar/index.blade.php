<div class="rounded-lg border border-main-border bg-surface p-3">
    <div class="relative flex flex-col sm:flex-row">
        <!-- /* --------------------------- CALENDAR NAV --------------------------- */ -->
        <nav>
            <button
                wire:click="previousMonth"
                class="absolute left-1 top-0 z-10 inline-flex h-7 w-7 items-center justify-center gap-1 rounded-md border border-main-border bg-transparent p-0 text-sm opacity-50 transition-colors duration-150 hover:bg-accent hover:text-accent-text hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main active:brightness-90"
                aria-label="Go to the Previous Month">
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                    stroke-linejoin="round" class="h-4 w-4" height="1em" width="1em"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>

            <button
                wire:click="nextMonth"
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
                <span class="text-sm font-medium" role="status">{{ $this->currentDate->format('F Y') }}</span>
            </div>

            <!-- /* ---------------------------- CALENDAR TABLE --------------------------- */ -->
            <table role="grid" aria-label="{{ $this->currentDate->format('F Y') }}"
                class="w-full border-collapse space-y-1">
                <!-- CALENDAR TABLE HEADER -->
                <thead>
                    <tr class="flex">
                        @foreach (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $day)
                            <th aria-label="{{ $day }}"
                                class="flex h-9 w-9 items-center justify-center text-[0.8rem] font-normal text-muted-text"
                                scope="col">
                                {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <!-- CALENDAR TABLE BODY -->
                <tbody>
                    @php
                        $inActiveMonthCss = 'text-muted-text text-muted-text/80 hover:bg-accent';
                        $activeMonthCss = 'hover:bg-accent hover:text-accent-text active:brightness-90';
                        $todayCss = 'bg-primary text-primary-text';
                    @endphp
                    @foreach ($this->getDays() as $week)
                        <tr class="mt-0.5 flex w-full">
                            @foreach ($week as $day)
                                @php
                                    // Build the aria-label
                                    $ariaLabel = $day['date']->format('l, F jS, Y');
                                    if ($day['isToday']) {
                                        $ariaLabel = 'Today, ' . $ariaLabel;
                                    }
                                    if ($day['isSelected']) {
                                        $ariaLabel .= ', selected';
                                    }
                                    $isLastDayOfMonth = $day['date']->isLastOfMonth();
                                @endphp
                                <td data-day="{{ $day['date']->format('Y-m-d') }}"
                                    @if ($isLastDayOfMonth) data-month="{{ $day['date']->format('Y-m') }}" @endif
                                    @if (!$day['isCurrentMonth']) data-outside="true" @endif
                                    @if ($day['isToday']) data-today="true" @endif
                                    @if ($day['isSelected']) aria-selected="true" data-selected="true" @endif>
                                    <button
                                        wire:click="selectDate('{{ $day['date']->format('Y-m-d') }}')"
                                        class="relative h-9 w-9 rounded-md p-0 text-center text-sm 
                                        @if (!$day['isCurrentMonth']) {{ $inActiveMonthCss }} @endif 
                                        @if ($day['isToday']) {{ $todayCss }} 
                                        @elseif($day['isCurrentMonth']) {{ $activeMonthCss }} @endif
                                        @if ($day['isSelected'])
                                        border border-primary
                                        @endif
                                        focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-main"
                                        aria-label="{{ $ariaLabel }}"
                                        @if ($day['isToday']) tabindex="0" @endif>
                                        {{ $day['date']->day }}
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
