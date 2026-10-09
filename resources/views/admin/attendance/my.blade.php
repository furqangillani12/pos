@extends('layouts.admin')

@section('title', 'My Attendance')

@php
    use App\Services\SalaryCalculator as SC;
    $isIn = (bool) $open;
    $targetToday = (int) round($sheet['duty_hours'] * 60);
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-5"
     x-data="myAttendance({ closed: {{ $closedToday }}, since: {{ $openSince ? $openSince->timestamp : 'null' }}, target: {{ $targetToday }} })">

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm"><i class="fas fa-circle-check mr-1"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm"><i class="fas fa-circle-exclamation mr-1"></i> {{ session('error') }}</div>
    @endif
    @if ($stale)
        <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm">
            <i class="fas fa-triangle-exclamation mr-1"></i>
            You did not check out on {{ \Carbon\Carbon::parse($stale->attendance->date)->format('d M') }} (checked in {{ \Carbon\Carbon::parse($stale->check_in)->format('h:i A') }}). That time is not counted. Please ask your manager to add the check-out time.
        </div>
    @endif

    {{-- ── Check in / out ── --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-6 text-center">
        <div class="text-sm text-gray-500">Assalam-o-Alaikum,</div>
        <div class="text-2xl font-bold text-gray-900">{{ $employee->user->name }}</div>
        <div class="mt-1 text-sm text-gray-500">{{ now()->format('l, d F Y') }} · <span x-text="clock" class="font-mono font-semibold text-gray-700"></span></div>

        <div class="mt-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-semibold {{ $isIn ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
            <span class="h-2.5 w-2.5 rounded-full {{ $isIn ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
            @if ($isIn)
                Checked in since {{ $openSince->format('h:i A') }}
            @else
                Not checked in
            @endif
        </div>

        <form method="POST" action="{{ $isIn ? route('my-attendance.check-out') : route('my-attendance.check-in') }}"
              class="mt-6" @submit="busy = true">
            @csrf
            <button type="submit" :disabled="busy"
                class="mx-auto flex h-44 w-44 flex-col items-center justify-center rounded-full text-white shadow-xl transition active:scale-95 disabled:opacity-60
                       {{ $isIn ? 'bg-gradient-to-br from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700' : 'bg-gradient-to-br from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700' }}">
                <i class="fas {{ $isIn ? 'fa-right-from-bracket' : 'fa-fingerprint' }} text-4xl"></i>
                <span class="mt-2 text-xl font-extrabold tracking-wide">{{ $isIn ? 'CHECK OUT' : 'CHECK IN' }}</span>
                <span class="text-xs opacity-80">{{ $isIn ? 'end / break' : ($today && $today->sessions->count() ? 'back from break' : 'start your day') }}</span>
            </button>
        </form>

        {{-- Today --}}
        <div class="mt-6 grid grid-cols-3 gap-3 text-left">
            <div class="rounded-xl bg-blue-50 p-3">
                <div class="text-[11px] uppercase tracking-wide text-blue-700">Worked today</div>
                <div class="text-xl font-bold text-blue-900 font-mono" x-text="fmt(workedToday)"></div>
            </div>
            <div class="rounded-xl bg-purple-50 p-3">
                <div class="text-[11px] uppercase tracking-wide text-purple-700">Duty target</div>
                <div class="text-xl font-bold text-purple-900 font-mono">{{ SC::hm($targetToday) }}</div>
            </div>
            <div class="rounded-xl p-3" :class="workedToday >= target ? 'bg-green-50' : 'bg-amber-50'">
                <div class="text-[11px] uppercase tracking-wide" :class="workedToday >= target ? 'text-green-700' : 'text-amber-700'" x-text="workedToday >= target ? 'Extra' : 'Remaining'"></div>
                <div class="text-xl font-bold font-mono" :class="workedToday >= target ? 'text-green-900' : 'text-amber-900'" x-text="fmt(Math.abs(target - workedToday))"></div>
            </div>
        </div>
        <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-gray-100">
            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 transition-all" :style="'width:' + Math.min(100, workedToday / target * 100) + '%'"></div>
        </div>

        @if ($today && $today->sessions->count())
            <div class="mt-4 text-left">
                <div class="text-xs font-semibold text-gray-500 mb-1">Today's entries</div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($today->sessions->sortBy('check_in') as $s)
                        <span class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-mono text-gray-700">
                            {{ \Carbon\Carbon::parse($s->check_in)->format('h:i A') }} → {{ $s->check_out ? \Carbon\Carbon::parse($s->check_out)->format('h:i A') : 'now' }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ── This month ── --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b px-5 py-3">
            <div class="font-semibold text-gray-800">{{ $sheet['month']->format('F Y') }}</div>
            <form method="GET" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $month->format('Y-m') }}" max="{{ now()->format('Y-m') }}" class="rounded-lg border border-gray-300 px-2 py-1 text-sm" onchange="this.form.submit()">
            </form>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-5">
            <div><div class="text-[11px] uppercase text-gray-500">Worked</div><div class="text-lg font-bold">{{ SC::hm($sheet['worked_minutes']) }} <span class="text-xs font-normal text-gray-400">hrs</span></div></div>
            <div><div class="text-[11px] uppercase text-gray-500">Days present</div><div class="text-lg font-bold">{{ $sheet['present_days'] }} <span class="text-xs font-normal text-gray-400">/ {{ $sheet['days_in_month'] }}</span></div></div>
            <div><div class="text-[11px] uppercase text-gray-500">Rate</div><div class="text-lg font-bold">Rs. {{ number_format($sheet['per_hour'], 2) }} <span class="text-xs font-normal text-gray-400">/ hr</span></div></div>
            <div><div class="text-[11px] uppercase text-gray-500">Earned</div><div class="text-lg font-bold text-emerald-700">Rs. {{ number_format($sheet['earned'], 0) }}</div></div>
        </div>
        <div class="max-h-96 overflow-y-auto border-t">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-gray-50 text-xs text-gray-500">
                    <tr><th class="px-4 py-2 text-left">Date</th><th class="px-2 py-2 text-left">In</th><th class="px-2 py-2 text-left">Out</th><th class="px-2 py-2 text-right">Break</th><th class="px-2 py-2 text-right">Worked</th><th class="px-4 py-2 text-right">Amount</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach (array_reverse($sheet['days']) as $d)
                        @continue($d['status'] === 'future')
                        <tr class="{{ $d['date']->isToday() ? 'bg-blue-50/60' : '' }}">
                            <td class="px-4 py-2 whitespace-nowrap">{{ $d['date']->format('D d') }}</td>
                            @if ($d['worked'] > 0 || $d['open'])
                                <td class="px-2 py-2 font-mono text-xs">{{ $d['in'] }}</td>
                                <td class="px-2 py-2 font-mono text-xs">{{ $d['out'] ?? '-' }}@if ($d['open'])<span class="text-amber-600"> (open)</span>@endif</td>
                                <td class="px-2 py-2 text-right font-mono text-xs text-gray-500">{{ $d['break'] ? SC::hm($d['break']) : '' }}</td>
                                <td class="px-2 py-2 text-right font-mono">{{ SC::hm($d['worked']) }}</td>
                                <td class="px-4 py-2 text-right">Rs. {{ number_format($d['amount'], 0) }}</td>
                            @else
                                <td colspan="5" class="px-2 py-2 text-xs text-red-500">{{ $d['status'] === 'on_leave' ? 'Leave' : 'Absent' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    window.myAttendance = function (cfg) {
        return {
            busy: false,
            now: Date.now(),
            target: cfg.target,
            init() { setInterval(() => this.now = Date.now(), 1000); },
            get clock() { return new Date(this.now).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }); },
            // Closed minutes today + the running session.
            get workedToday() {
                const running = cfg.since ? Math.max(0, Math.floor((this.now / 1000 - cfg.since) / 60)) : 0;
                return cfg.closed + running;
            },
            fmt(m) { m = Math.max(0, Math.round(m)); return Math.floor(m / 60) + ':' + String(m % 60).padStart(2, '0'); },
        };
    };
</script>
@endsection
