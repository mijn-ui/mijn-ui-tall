{{-- Partial: included by gantt/index.blade.php — receives $members, $scale, $header from parent --}}

@php
use Carbon\Carbon;

$allowedScales = ['day', 'week', 'month'];
$scale = in_array($scale, $allowedScales) ? $scale : 'day';

$memberList = [];
foreach ($members as $idx => $member) {
    $memberList[] = [
        '_id' => 'member-' . $idx,
        'id' => $member['id'] ?? null,
        'name' => $member['name'] ?? '',
        'avatar' => $member['avatar'] ?? null,
        'records' => (array) ($member['records'] ?? []),
    ];
}

if (empty($memberList)) {
    return;
}

// Determine date range from all records, fallback to current month
$allDates = collect($memberList)->pluck('records')->flatMap(fn($r) => array_keys($r))->filter()->unique()->sort();

if ($allDates->isEmpty()) {
    $rangeStart = Carbon::today()->startOfMonth();
    $rangeEnd = Carbon::today()->endOfMonth();
} else {
    $rangeStart = Carbon::parse($allDates->first());
    $rangeEnd = Carbon::parse($allDates->last());
}

$config = [
    'members' => $memberList,
    'scale' => $scale,
    'rangeStart' => $rangeStart->format('Y-m-d'),
    'rangeEnd' => $rangeEnd->format('Y-m-d'),
    'today' => Carbon::today()->format('Y-m-d'),
];
@endphp

<div x-data="attendanceChart(@js($config))"
     x-ref="attendanceRoot"
     @attendance-updated.window="$wire?.call('handleAttendanceUpdate', $event.detail)"
     class="h-full w-full overflow-hidden">

    <style>
        .att-no-scrollbar::-webkit-scrollbar { display: none; }
        .att-no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .att-cell { transition: background-color 0.15s ease; }
        .att-cell:hover { background-color: rgba(0,0,0,0.04); }
    </style>

    <div class="flex h-full w-full">
        {{-- ========== Sidebar ========== --}}
        <div class="w-64 shrink-0 border-r border-main-border bg-surface flex flex-col">
            {{-- Sidebar Header --}}
            <div class="shrink-0 h-20 flex flex-col border-b border-main-border bg-accent">
                {{-- Today button --}}
                <div class="flex items-center px-4 h-10 border-b border-main-border">
                    <button @click="_scrollTo(today)" type="button"
                            class="ml-auto text-xs font-medium text-primary hover:text-primary/80 transition-colors cursor-pointer">
                        <span x-text="todayLabel"></span>
                    </button>
                </div>
                {{-- Header title (bottom row) --}}
                <div class="flex items-center h-10 px-4">
                    @if($header)
                        <p class="text-sm font-semibold text-main-text truncate">{{ $header }}</p>
                    @endif
                </div>
            </div>

            {{-- Member List --}}
            <div x-ref="sidebarBody" class="flex-1 overflow-hidden att-no-scrollbar">
                @foreach($members as $idx => $member)
                    <div class="h-10 w-full flex items-center gap-3 px-4 border-b border-main-border">
                        @if(!empty($member['avatar']))
                            <img src="{{ $member['avatar'] }}" alt="" class="h-6 w-6 rounded-full shrink-0 object-cover">
                        @else
                            <div class="h-6 w-6 rounded-full shrink-0 bg-primary/10 flex items-center justify-center">
                                <span class="text-xs font-semibold text-primary">{{ strtoupper(substr($member['name'] ?? '?', 0, 1)) }}</span>
                            </div>
                        @endif
                        <span class="text-sm font-medium text-main-text truncate">{{ $member['name'] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========== Timeline ========== --}}
        <div x-ref="timeline" class="flex-1 overflow-auto att-no-scrollbar">
            {{-- Sticky Header --}}
            <div class="sticky top-0 z-20" :style="`width: ${totalWidth}px`">
                {{-- Header Groups (Month/Year) --}}
                <div class="flex">
                    <template x-for="group in headerGroups" :key="group.key">
                        <div class="shrink-0 h-10 flex items-center justify-center border-r border-b border-main-border bg-accent px-2"
                             :style="`width: ${group.width}px`">
                            <span class="text-sm font-semibold text-main-text truncate" x-text="group.label"></span>
                        </div>
                    </template>
                </div>

                {{-- Column Headers --}}
                <div class="flex">
                    <template x-for="col in columns" :key="col.key">
                        <div class="shrink-0 h-10 flex flex-col items-center justify-center border-r border-b border-main-border"
                             :class="col.isToday ? 'bg-primary text-white' : (col.isWeekend ? 'bg-accent/50' : 'bg-surface')"
                             :style="`width: ${col.width}px`">
                            <span class="text-xs leading-tight"
                                  :class="col.isToday ? 'text-white font-bold' : 'text-muted-text'"
                                  x-text="col.label1"></span>
                            <span class="text-xs font-semibold leading-tight"
                                  :class="col.isToday ? 'text-white' : 'text-muted-text'"
                                  x-text="col.label2"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Grid + Cells --}}
            <div class="relative" :style="`width: ${totalWidth}px; height: ${gridHeight}px`">
                {{-- Grid background (vertical column lines) --}}
                <div class="absolute inset-0 flex pointer-events-none">
                    <template x-for="col in columns" :key="'g-' + col.key">
                        <div class="shrink-0 h-full border-r border-main-border"
                             :class="col.isToday ? 'bg-primary/5' : (col.isWeekend ? 'bg-accent/30' : '')"
                             :style="`width: ${col.width}px`">
                        </div>
                    </template>
                </div>

                {{-- Horizontal row lines --}}
                <template x-for="(member, mIdx) in members" :key="'row-' + member._id">
                    <div class="absolute left-0 w-full border-b border-main-border pointer-events-none"
                         :style="`top: ${(mIdx + 1) * rowHeight - 1}px`">
                    </div>
                </template>

                {{-- Today Line --}}
                <template x-if="todayX !== null">
                    <div class="absolute top-0 h-full w-0.5 bg-primary/60 z-20 pointer-events-none"
                         :style="`left: ${todayX}px`">
                    </div>
                </template>

                {{-- Clickable Cells --}}
                <template x-for="cell in cellGrid" :key="cell.key">
                    <div class="absolute flex items-center justify-center att-cell cursor-pointer z-10 group"
                         :style="`top: ${cell.top}px; left: ${cell.left}px; width: ${cell.width}px; height: ${rowHeight}px;`"
                         @click="toggleAttendance(members[cell.mIdx], cell.date)">

                        {{-- Present (check) --}}
                        <template x-if="getStatus(members[cell.mIdx], cell.date) === true">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </template>

                        {{-- Absent (cross) --}}
                        <template x-if="getStatus(members[cell.mIdx], cell.date) === false">
                            <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                            </svg>
                        </template>

                        {{-- No record: show faint check on hover --}}
                        <template x-if="getStatus(members[cell.mIdx], cell.date) === null">
                            <svg class="w-4 h-4 text-muted-text/30 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </template>

                        {{-- Clear button: shown on hover when cell has a record --}}
                        <template x-if="getStatus(members[cell.mIdx], cell.date) !== null">
                            <button class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-surface border border-main-border flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-50 hover:bg-danger hover:border-danger hover:text-white text-muted-text shadow-sm"
                                    @click.stop="clearAttendance(members[cell.mIdx], cell.date)"
                                    type="button">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function attendanceChart(config) {
    return {
        // ── Data ──
        members: config.members,
        scale: config.scale || 'day',
        today: config.today,
        rangeStart: config.rangeStart,
        rangeEnd: config.rangeEnd,
        _loading: false,

        // ── Date Range (reactive) ──
        startDate: '',
        endDate: '',

        // ── Scale Configs ──
        _scales: {
            day:   { ppd: 48, buffer: 15, extend: 30 },
            week:  { ppd: 16, buffer: 60, extend: 60 },
            month: { ppd: 4,  buffer: 180, extend: 90 },
        },

        get _cfg() { return this._scales[this.scale]; },
        get ppd() { return this._cfg.ppd; },

        // ── Init ──
        init() {
            const buf = this._cfg.buffer;
            this.startDate = this._addDays(this.rangeStart, -buf);
            this.endDate = this._addDays(this.rangeEnd, buf);

            this.$nextTick(() => {
                this._scrollTo(this.today);
                this._bindScroll();
            });
        },

        // ── Date Helpers ──
        _d(str) { return new Date(str + 'T12:00:00'); },

        _addDays(str, n) {
            const d = this._d(str);
            d.setDate(d.getDate() + n);
            return d.toISOString().slice(0, 10);
        },

        _diffDays(a, b) {
            return Math.round((this._d(b) - this._d(a)) / 86400000);
        },

        // ── Day Range ──
        get dayRange() {
            const total = this._diffDays(this.startDate, this.endDate);
            const days = [];
            for (let i = 0; i <= total; i++) {
                days.push(this._addDays(this.startDate, i));
            }
            return days;
        },

        get totalWidth() { return this.dayRange.length * this.ppd; },

        // ── Columns (always per-day for attendance cells) ──
        get dayCols() {
            const ppd = this.ppd;
            return this.dayRange.map((ds, i) => ({
                date: ds,
                x: i * ppd,
                width: ppd,
            }));
        },

        // ── Display Columns (respects scale for headers) ──
        get columns() {
            const range = this.dayRange;
            const ppd = this.ppd;

            if (this.scale === 'day') {
                const dayNames = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
                return range.map((ds, i) => {
                    const d = this._d(ds);
                    const dow = d.getDay();
                    return {
                        key: ds, date: ds, x: i * ppd, width: ppd,
                        label1: String(d.getDate()).padStart(2, '0'),
                        label2: dayNames[dow],
                        isToday: ds === this.today,
                        isWeekend: dow === 0 || dow === 6,
                    };
                });
            }

            const cols = [];
            let gKey = null, gStart = 0, gCount = 0;

            for (let i = 0; i < range.length; i++) {
                const d = this._d(range[i]);
                let key = this.scale === 'week'
                    ? (() => { const m = new Date(d); m.setDate(m.getDate() - ((m.getDay() + 6) % 7)); return m.toISOString().slice(0, 10); })()
                    : range[i].slice(0, 7);

                if (key !== gKey) {
                    if (gKey !== null) cols.push(this._groupCol(gKey, gStart, gCount, range));
                    gKey = key; gStart = i; gCount = 1;
                } else { gCount++; }
            }
            if (gKey !== null) cols.push(this._groupCol(gKey, gStart, gCount, range));
            return cols;
        },

        _groupCol(key, startIdx, dayCount, range) {
            const ppd = this.ppd;
            const d = this._d(range[startIdx]);
            const endD = this._d(range[startIdx + dayCount - 1]);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            let label1, label2;

            if (this.scale === 'week') {
                label1 = `${d.getDate()} - ${endD.getDate()}`;
                label2 = months[d.getMonth()];
            } else {
                label1 = months[d.getMonth()];
                label2 = String(d.getFullYear());
            }

            return {
                key, date: range[startIdx], x: startIdx * ppd, width: dayCount * ppd,
                label1, label2,
                isToday: range.slice(startIdx, startIdx + dayCount).includes(this.today),
                isWeekend: false,
            };
        },

        // ── Header Groups ──
        get headerGroups() {
            const groups = [];
            let label = null, width = 0;
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

            for (const col of this.columns) {
                const d = this._d(col.date);
                const l = this.scale === 'month'
                    ? String(d.getFullYear())
                    : `${months[d.getMonth()]} ${d.getFullYear()}`;

                if (l !== label) {
                    if (label !== null) groups.push({ key: label + '-' + groups.length, label, width });
                    label = l; width = col.width;
                } else { width += col.width; }
            }
            if (label !== null) groups.push({ key: label + '-' + groups.length, label, width });
            return groups;
        },

        // ── Row / Grid ──
        rowHeight: 40,
        get gridHeight() { return this.members.length * this.rowHeight; },

        // ── Today ──
        get todayLabel() {
            const d = this._d(this.today);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            return `${dayNames[d.getDay()]}, ${months[d.getMonth()]} ${d.getDate()}`;
        },

        get todayX() {
            if (!this.today) return null;
            const days = this._diffDays(this.startDate, this.today);
            if (days < 0 || days > this.dayRange.length) return null;
            return days * this.ppd + this.ppd / 2;
        },

        // ── Flat cell grid (avoids nested x-for scope issues) ──
        get cellGrid() {
            const cells = [];
            const cols = this.dayCols;
            for (let mIdx = 0; mIdx < this.members.length; mIdx++) {
                const member = this.members[mIdx];
                const top = mIdx * this.rowHeight;
                for (let c = 0; c < cols.length; c++) {
                    cells.push({
                        key: member._id + '-' + cols[c].date,
                        mIdx,
                        date: cols[c].date,
                        top,
                        left: cols[c].x,
                        width: cols[c].width,
                    });
                }
            }
            return cells;
        },

        // ── Attendance Logic ──
        getStatus(member, date) {
            if (!(date in member.records)) return null;
            return member.records[date];
        },

        toggleAttendance(member, date) {
            const current = this.getStatus(member, date);
            const next = current === true ? false : true;

            member.records[date] = next;

            // Force Alpine reactivity
            this.members = [...this.members];

            this._dispatchAttendance(member, date, next);
        },

        clearAttendance(member, date) {
            delete member.records[date];
            this.members = [...this.members];
            this._dispatchAttendance(member, date, null);
        },

        _dispatchAttendance(member, date, status) {
            this.$el.dispatchEvent(new CustomEvent('attendance-updated', {
                detail: {
                    member_id: member.id,
                    date: date,
                    status: status,
                },
                bubbles: true,
            }));
        },

        // ── Scroll / Infinite ──
        _bindScroll() {
            const el = this.$refs.timeline;
            if (!el) return;
            el.addEventListener('scroll', () => this._onScroll(), { passive: true });
        },

        _onScroll() {
            const el = this.$refs.timeline;
            if (!el || this._loading) return;

            if (this.$refs.sidebarBody) {
                this.$refs.sidebarBody.scrollTop = el.scrollTop;
            }

            const threshold = 400;
            if (el.scrollLeft < threshold) this._extendLeft();
            if (el.scrollWidth - el.scrollLeft - el.clientWidth < threshold) this._extendRight();
        },

        _extendLeft() {
            this._loading = true;
            const days = this._cfg.extend;
            const oldWidth = this.totalWidth;
            this.startDate = this._addDays(this.startDate, -days);
            this.$nextTick(() => {
                this.$refs.timeline.scrollLeft += (this.totalWidth - oldWidth);
                this._loading = false;
            });
        },

        _extendRight() {
            this._loading = true;
            this.endDate = this._addDays(this.endDate, this._cfg.extend);
            this.$nextTick(() => { this._loading = false; });
        },

        _scrollTo(dateStr) {
            const el = this.$refs.timeline;
            if (!el) return;
            const x = this._diffDays(this.startDate, dateStr) * this.ppd;
            el.scrollLeft = Math.max(0, x - el.clientWidth / 3);
        },

        // ── Scale Switch ──
        setScale(newScale) {
            if (this.scale === newScale) return;
            this.scale = newScale;
            const buf = this._cfg.buffer;
            this.startDate = this._addDays(this.rangeStart, -buf);
            this.endDate = this._addDays(this.rangeEnd, buf);
            this.$nextTick(() => this._scrollTo(this.today));
        },
    };
}
</script>
