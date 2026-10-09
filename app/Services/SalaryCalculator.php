<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;

/**
 * Salary by the minute (client rules, Oct 2026):
 *  - Monthly salary is spread over EVERY day of the month (30/31) × the employee's
 *    daily duty hours (default 12). No paid leave, no fixed shift, no late marks.
 *  - Pay = minutes actually worked (all check-in/out sessions of the day) × that
 *    per-minute rate — extra minutes are paid at the same rate, short minutes aren't.
 */
class SalaryCalculator
{
    public static function sheet(Employee $employee, int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $salary    = (float) ($employee->salary ?? 0);
        $dutyHours = (float) ($employee->duty_hours ?: 12);
        $perMinute = $salary > 0 ? $salary / ($daysInMonth * $dutyHours * 60) : 0;

        $attendances = $employee->attendances()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->with('sessions')->get()
            ->keyBy(fn ($a) => Carbon::parse($a->date)->toDateString());

        // Days up to today count as "absent" when nothing was worked.
        $lastCountable = $end->isFuture() ? Carbon::today() : $end;

        $days = [];
        $totals = ['worked' => 0, 'break' => 0, 'amount' => 0.0, 'present' => 0, 'absent' => 0, 'open' => 0];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $key = $d->toDateString();
            $att = $attendances->get($key);
            $sessions = $att ? $att->sessions->sortBy('check_in')->values() : collect();

            $worked = 0; $break = 0; $open = false; $prevOut = null;
            foreach ($sessions as $s) {
                if (!$s->check_in) continue;
                $in = Carbon::parse($key . ' ' . $s->check_in);
                if ($prevOut && $in->gt($prevOut)) $break += $prevOut->diffInMinutes($in);
                if ($s->check_out) {
                    $out = Carbon::parse($key . ' ' . $s->check_out);
                    if ($out->gt($in)) $worked += $in->diffInMinutes($out);
                    $prevOut = $out;
                } else {
                    $open = true;
                }
            }
            $firstIn = $sessions->first()?->check_in;
            $lastOut = $sessions->filter(fn ($s) => $s->check_out)->last()?->check_out;
            $amount  = round($worked * $perMinute, 2);
            $leave   = $att && in_array($att->status, ['on_leave', 'absent'], true) && $worked === 0;

            $days[] = [
                'date'    => $d->copy(),
                'in'      => $firstIn ? Carbon::parse($firstIn)->format('h:i A') : null,
                'out'     => $lastOut ? Carbon::parse($lastOut)->format('h:i A') : null,
                'break'   => $break,
                'worked'  => $worked,
                'amount'  => $amount,
                'open'    => $open,
                'status'  => $worked > 0 ? 'present' : ($leave ? $att->status : ($d->lte($lastCountable) ? 'absent' : 'future')),
                'sessions'=> $sessions->count(),
            ];

            $totals['worked'] += $worked;
            $totals['break']  += $break;
            $totals['amount'] += $amount;
            if ($worked > 0) $totals['present']++;
            elseif ($d->lte($lastCountable)) $totals['absent']++;
            if ($open) $totals['open']++;
        }

        $expected = (int) round($daysInMonth * $dutyHours * 60);

        return [
            'employee'        => $employee,
            'month'           => $start,
            'days'            => $days,
            'days_in_month'   => $daysInMonth,
            'duty_hours'      => $dutyHours,
            'salary'          => $salary,
            'per_minute'      => $perMinute,
            'per_hour'        => round($perMinute * 60, 2),
            'expected_minutes'=> $expected,
            'worked_minutes'  => $totals['worked'],
            'break_minutes'   => $totals['break'],
            'difference'      => $totals['worked'] - $expected,
            'earned'          => round($totals['amount'], 2),
            'present_days'    => $totals['present'],
            'absent_days'     => $totals['absent'],
            'open_sessions'   => $totals['open'],
        ];
    }

    /** 754 minutes → "12:34". */
    public static function hm(int $minutes): string
    {
        $sign = $minutes < 0 ? '-' : '';
        $minutes = abs($minutes);
        return $sign . intdiv($minutes, 60) . ':' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);
    }
}
