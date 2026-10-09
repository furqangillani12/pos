@extends('layouts.admin')

@section('title', 'Attendance')

@php
    use App\Services\SalaryCalculator as SC;
    $isToday = $date->isToday();
@endphp

@section('content')
<div class="space-y-5" x-data="attBoard()">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Attendance</h2>
            <p class="text-sm text-gray-500">{{ $date->format('l, d M Y') }} @if ($isToday)· <span class="font-mono" x-text="clock"></span>@endif</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" class="flex items-center gap-1">
                <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->subDay()->toDateString()]) }}" class="rounded-lg border px-2 py-1.5 text-sm text-gray-600 hover:bg-gray-50" title="Previous day"><i class="fas fa-chevron-left"></i></a>
                <input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                @if (!$isToday)
                    <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->addDay()->toDateString()]) }}" class="rounded-lg border px-2 py-1.5 text-sm text-gray-600 hover:bg-gray-50" title="Next day"><i class="fas fa-chevron-right"></i></a>
                    <a href="{{ route('admin.attendance.index') }}" class="rounded-lg border px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50">Today</a>
                @endif
            </form>
            <a href="{{ route('admin.attendance.create', ['date' => $date->toDateString()]) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200"><i class="fas fa-pen-to-square"></i> Add / fix times</a>
            <a href="{{ route('admin.attendance.bulk-create', ['date' => $date->toDateString()]) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200"><i class="fas fa-users"></i> Bulk</a>
            <a href="{{ route('admin.attendance.monthly-report', ['month' => $date->format('Y-m')]) }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"><i class="fas fa-calendar-days"></i> Month report</a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm"><i class="fas fa-circle-check mr-1"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm"><i class="fas fa-circle-exclamation mr-1"></i> {{ session('error') }}</div>
    @endif

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl bg-white border border-gray-100 p-4 shadow-sm"><div class="text-xs text-gray-500">Staff</div><div class="text-2xl font-bold text-gray-800">{{ $summary['total'] }}</div></div>
        <div class="rounded-xl bg-white border border-green-100 p-4 shadow-sm"><div class="text-xs text-green-700"><span class="mr-1 inline-block h-2 w-2 rounded-full bg-green-500"></span>{{ $isToday ? 'Working now' : 'Still open' }}</div><div class="text-2xl font-bold text-green-700">{{ $summary['in'] }}</div></div>
        <div class="rounded-xl bg-white border border-blue-100 p-4 shadow-sm"><div class="text-xs text-blue-700">Checked out</div><div class="text-2xl font-bold text-blue-700">{{ $summary['out'] }}</div></div>
        <div class="rounded-xl bg-white border border-gray-100 p-4 shadow-sm"><div class="text-xs text-gray-500">{{ $isToday ? 'Not in yet' : 'Absent' }}</div><div class="text-2xl font-bold text-gray-500">{{ $summary['none'] }}</div></div>
    </div>

    {{-- Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($board as $row)
            @php
                $e = $row['employee'];
                $target = (int) round(($e->duty_hours ?: 12) * 60);
                $stateCls = ['in' => 'border-green-300', 'out' => 'border-blue-200', 'leave' => 'border-amber-200', 'none' => 'border-gray-200'][$row['state']];
            @endphp
            <div class="rounded-xl bg-white border-2 {{ $stateCls }} p-4 shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 font-bold text-gray-600">{{ strtoupper(mb_substr($e->user->name ?? '?', 0, 1)) }}</div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $e->user->name ?? 'Employee' }}</div>
                            <div class="text-xs">
                                @if ($row['state'] === 'in')
                                    <span class="text-green-700"><span class="mr-1 inline-block h-2 w-2 animate-pulse rounded-full bg-green-500"></span>In since {{ $row['openSince']->format('h:i A') }}</span>
                                @elseif ($row['state'] === 'out')
                                    <span class="text-blue-700">Checked out</span>
                                @elseif ($row['state'] === 'leave')
                                    <span class="text-amber-700">On leave</span>
                                @else
                                    <span class="text-gray-400">{{ $isToday ? 'Not checked in' : 'Absent' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-mono text-lg font-bold text-gray-800"
                             x-data="{ c: {{ $row['closed'] }}, s: {{ $row['openSince'] && $isToday ? $row['openSince']->timestamp : 'null' }} }"
                             x-text="fmt(c + (s ? Math.max(0, Math.floor((now / 1000 - s) / 60)) : 0))">{{ SC::hm($row['closed']) }}</div>
                        <div class="text-[10px] text-gray-400">of {{ SC::hm($target) }} hrs</div>
                    </div>
                </div>

                @if ($row['sessions']->count())
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($row['sessions'] as $s)
                            <span class="rounded-md border border-gray-200 bg-gray-50 px-2 py-0.5 font-mono text-[11px] text-gray-700">
                                {{ \Carbon\Carbon::parse($s->check_in)->format('h:i A') }} → {{ $s->check_out ? \Carbon\Carbon::parse($s->check_out)->format('h:i A') : 'now' }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-3 flex flex-wrap items-center gap-2 border-t pt-3">
                    @if ($isToday)
                        @if ($row['state'] === 'in')
                            <form method="POST" action="{{ route('admin.attendance.checkout', $row['attendance']) }}">
                                @csrf
                                <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700"><i class="fas fa-right-from-bracket"></i> Check out</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.attendance.quick-checkin') }}">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ $e->id }}">
                                <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700"><i class="fas fa-fingerprint"></i> Check in</button>
                            </form>
                        @endif
                    @endif
                    <a href="{{ route('admin.attendance.create', ['date' => $date->toDateString(), 'employee_id' => $e->id]) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200"><i class="fas fa-pen"></i> Fix times</a>
                    @can('manage payroll')
                        <a href="{{ route('admin.payroll.sheet', ['employee' => $e->id, 'month' => $date->format('Y-m')]) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200"><i class="fas fa-file-invoice"></i> Month sheet</a>
                    @endcan
                    @if ($row['attendance'])
                        <form method="POST" action="{{ route('admin.attendance.destroy', $row['attendance']) }}" class="ml-auto" onsubmit="return confirm('Delete this day\'s attendance for {{ addslashes($e->user->name ?? '') }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600" title="Delete this day"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($board->isEmpty())
        <div class="rounded-xl bg-white border p-10 text-center text-gray-400">No employees in this branch yet.</div>
    @endif
</div>

<script>
    window.attBoard = function () {
        return {
            now: Date.now(),
            init() { setInterval(() => this.now = Date.now(), 30000); setInterval(() => this.tick = Date.now(), 1000); },
            tick: Date.now(),
            get clock() { return new Date(this.tick).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); },
            fmt(m) { m = Math.max(0, Math.round(m)); return Math.floor(m / 60) + ':' + String(m % 60).padStart(2, '0'); },
        };
    };
</script>
@endsection
