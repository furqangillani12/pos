@extends('layouts.admin')

@php
    use App\Services\SalaryCalculator as SC;
    $emp      = $sheet['employee'];
    $monthStr = $sheet['month']->format('F Y');
    $isPaid   = $payroll && $payroll->status === 'paid';
    $halves   = [array_slice($sheet['days'], 0, 15), array_slice($sheet['days'], 15)];
    $diff     = $sheet['difference'];
@endphp

@section('title', 'Salary slip: ' . ($emp->user->name ?? 'Employee') . ', ' . $monthStr)

@section('content')
<div class="max-w-5xl mx-auto space-y-4">

    {{-- Toolbar (screen only) --}}
    <div class="no-print flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.payroll.index', ['month' => $sheet['month']->month, 'year' => $sheet['month']->year]) }}" class="text-sm text-blue-600 hover:underline"><i class="fas fa-arrow-left text-xs"></i> Back to Payroll</a>
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('admin.payroll.sheet', $emp) }}" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $sheet['month']->format('Y-m') }}" class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm" onchange="this.form.submit()">
            </form>
            @if ($payroll && !$isPaid)
                <form method="POST" action="{{ route('admin.payroll.markPaid', $payroll) }}" onsubmit="return confirm('Mark this salary as paid?')">
                    @csrf
                    <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700"><i class="fas fa-check"></i> Mark paid</button>
                </form>
            @endif
            <button onclick="window.print()" class="rounded-lg bg-gray-800 px-3 py-1.5 text-sm font-semibold text-white hover:bg-gray-900"><i class="fas fa-print"></i> Print / PDF</button>
        </div>
    </div>

    {{-- ── The slip (one A4 page) ── --}}
    <div id="slip" class="rounded-xl bg-white shadow-sm border border-gray-200 p-5 text-[12px] text-gray-800">
        <div class="flex items-start justify-between border-b pb-3">
            <div>
                <div class="text-lg font-extrabold text-gray-900">{{ $emp->branch->name ?? config('app.name') }}</div>
                <div class="text-sm font-semibold text-gray-600">Salary slip · {{ $monthStr }}</div>
            </div>
            <div class="text-right">
                <div class="text-base font-bold">{{ $emp->user->name ?? '' }}</div>
                <div class="text-gray-500">{{ $emp->phone }}</div>
                <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-bold {{ $isPaid ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $isPaid ? 'PAID' : ($payroll ? 'UNPAID' : 'NOT GENERATED') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-2 py-3 text-center">
            <div class="rounded-lg bg-gray-50 p-2"><div class="text-[10px] uppercase text-gray-500">Monthly salary</div><div class="font-bold">Rs. {{ number_format($sheet['salary'], 0) }}</div></div>
            <div class="rounded-lg bg-gray-50 p-2"><div class="text-[10px] uppercase text-gray-500">Duty per day</div><div class="font-bold">{{ SC::hm((int) round($sheet['duty_hours'] * 60)) }} hrs</div></div>
            <div class="rounded-lg bg-gray-50 p-2"><div class="text-[10px] uppercase text-gray-500">Days in month</div><div class="font-bold">{{ $sheet['days_in_month'] }}</div></div>
            <div class="rounded-lg bg-gray-50 p-2"><div class="text-[10px] uppercase text-gray-500">Rate</div><div class="font-bold">Rs. {{ number_format($sheet['per_hour'], 2) }}/hr · {{ number_format($sheet['per_minute'], 4) }}/min</div></div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            @foreach ($halves as $half)
                <table class="w-full border border-gray-200 text-[11px]">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr><th class="px-1.5 py-1 text-left">Date</th><th class="px-1 py-1 text-left">In</th><th class="px-1 py-1 text-left">Out</th><th class="px-1 py-1 text-right">Break</th><th class="px-1 py-1 text-right">Worked</th><th class="px-1.5 py-1 text-right">Rs.</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($half as $d)
                            <tr class="border-t border-gray-100 {{ $d['date']->isFriday() ? 'bg-gray-50' : '' }}">
                                <td class="px-1.5 py-[3px] whitespace-nowrap">{{ $d['date']->format('d D') }}</td>
                                @if ($d['worked'] > 0 || $d['open'])
                                    <td class="px-1 py-[3px] font-mono">{{ $d['in'] ? \Carbon\Carbon::parse($d['in'])->format('g:i a') : '' }}</td>
                                    <td class="px-1 py-[3px] font-mono">{{ $d['out'] ? \Carbon\Carbon::parse($d['out'])->format('g:i a') : '' }}{{ $d['open'] ? '*' : '' }}</td>
                                    <td class="px-1 py-[3px] text-right font-mono text-gray-500">{{ $d['break'] ? SC::hm($d['break']) : '' }}</td>
                                    <td class="px-1 py-[3px] text-right font-mono font-semibold">{{ SC::hm($d['worked']) }}</td>
                                    <td class="px-1.5 py-[3px] text-right">{{ number_format($d['amount'], 0) }}</td>
                                @elseif ($d['status'] === 'future')
                                    <td colspan="5" class="px-1 py-[3px] text-gray-300"></td>
                                @else
                                    <td colspan="5" class="px-1 py-[3px] text-red-500">{{ $d['status'] === 'on_leave' ? 'Leave' : 'Absent' }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
        @if ($sheet['open_sessions'])
            <p class="mt-1 text-[10px] text-amber-700">* No check-out recorded that day; the open time is not counted. Add the check-out on the Attendance page to include it.</p>
        @endif

        <div class="mt-3 grid grid-cols-2 gap-3">
            <table class="w-full text-[12px]">
                <tr><td class="py-0.5 text-gray-500">Days present / absent</td><td class="py-0.5 text-right font-semibold">{{ $sheet['present_days'] }} / {{ $sheet['absent_days'] }}</td></tr>
                <tr><td class="py-0.5 text-gray-500">Duty time for the month</td><td class="py-0.5 text-right font-mono">{{ SC::hm($sheet['expected_minutes']) }}</td></tr>
                <tr><td class="py-0.5 text-gray-500">Time worked</td><td class="py-0.5 text-right font-mono font-semibold">{{ SC::hm($sheet['worked_minutes']) }}</td></tr>
                <tr><td class="py-0.5 text-gray-500">Breaks (between entries)</td><td class="py-0.5 text-right font-mono">{{ SC::hm($sheet['break_minutes']) }}</td></tr>
                <tr><td class="py-0.5 text-gray-500">{{ $diff >= 0 ? 'Extra time' : 'Short time' }}</td><td class="py-0.5 text-right font-mono {{ $diff >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $diff >= 0 ? '+' : '' }}{{ SC::hm($diff) }}</td></tr>
            </table>
            <div class="rounded-lg border-2 border-emerald-200 bg-emerald-50 p-3">
                <div class="flex justify-between"><span class="text-gray-600">Salary (full month)</span><span>Rs. {{ number_format($sheet['salary'], 0) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">{{ $diff >= 0 ? 'Extra minutes' : 'Short minutes' }}</span><span class="{{ $diff >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $diff >= 0 ? '+' : '-' }} Rs. {{ number_format(abs($sheet['earned'] - $sheet['salary']), 0) }}</span></div>
                <div class="mt-1 flex justify-between border-t border-emerald-200 pt-1 text-base font-extrabold text-emerald-800"><span>Payable</span><span>Rs. {{ number_format($sheet['earned'], 0) }}</span></div>
                <div class="mt-0.5 text-[10px] text-gray-500">= {{ number_format($sheet['worked_minutes']) }} minutes × Rs. {{ number_format($sheet['per_minute'], 4) }}</div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-10 text-center text-[11px] text-gray-500">
            <div class="border-t border-gray-400 pt-1">Employee signature</div>
            <div class="border-t border-gray-400 pt-1">Authorised signature</div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @page { size: A4 portrait; margin: 6mm; }
    @media print {
        .no-print, nav, aside, header, .mobile-header, .mobile-sidebar, .sidebar-overlay { display: none !important; }
        html, body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
        main { padding: 0 !important; margin: 0 !important; min-height: 0 !important; }
        .min-h-screen { min-height: 0 !important; }
        #slip { border: 0 !important; box-shadow: none !important; padding: 0 !important; }
        /* The layout's small-screen rules also hit print width — undo them here. */
        main, main.flex-1 { padding: 0 !important; padding-top: 0 !important; background: #fff !important; }
        #slip .grid.grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
        #slip .grid.grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
        #slip table th, #slip table td { padding: 2px 4px !important; font-size: 10.5px !important; line-height: 1.25 !important; }
        #slip .mt-8 { margin-top: 18px !important; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush
@endsection
