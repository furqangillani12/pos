<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\SalaryCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Self-service attendance for the logged-in employee: one big Check In /
 * Check Out button, today's time (with breaks), and this month's sheet.
 */
class MyAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;
        abort_unless($employee, 403, 'Your login is not linked to an employee record.');

        $today = Attendance::with('sessions')->where('employee_id', $employee->id)
            ->whereDate('date', today())->first();
        $open  = $today?->sessions->whereNull('check_out')->sortByDesc('id')->first();

        // A session left open on an earlier day (forgot to check out).
        $stale = \App\Models\AttendanceSession::whereNull('check_out')
            ->whereHas('attendance', fn ($q) => $q->where('employee_id', $employee->id)->whereDate('date', '<', today()))
            ->with('attendance')->latest('id')->first();

        $month = $request->input('month') ? Carbon::parse($request->input('month') . '-01') : today()->startOfMonth();
        $sheet = SalaryCalculator::sheet($employee, $month->year, $month->month);
        $todayRow = collect($sheet['days'])->first(fn ($d) => $d['date']->isToday());

        // Minutes already closed today + the running session (shown live by JS).
        $closedToday = (int) ($todayRow['worked'] ?? 0);
        $openSince   = $open ? Carbon::parse(today()->toDateString() . ' ' . $open->check_in) : null;

        return view('admin.attendance.my', compact('employee', 'today', 'open', 'stale', 'sheet', 'closedToday', 'openSince', 'month'));
    }

    public function checkIn()
    {
        $employee = auth()->user()->employee;
        abort_unless($employee, 403);

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => today()->toDateString()],
            ['status' => 'present', 'branch_id' => $employee->branch_id]
        );
        if ($attendance->sessions()->whereNull('check_out')->exists()) {
            return back()->with('error', 'You are already checked in.');
        }
        $attendance->update(['status' => 'present']);
        $attendance->sessions()->create(['check_in' => now()->format('H:i')]);

        return back()->with('success', 'Checked in at ' . now()->format('h:i A') . '. Have a good day!');
    }

    public function checkOut()
    {
        $employee = auth()->user()->employee;
        abort_unless($employee, 403);

        $attendance = Attendance::where('employee_id', $employee->id)->whereDate('date', today())->first();
        $open = $attendance?->sessions()->whereNull('check_out')->latest('id')->first();
        if (!$open) {
            return back()->with('error', 'You are not checked in.');
        }
        $now = now()->format('H:i');
        $open->update(['check_out' => $now > $open->check_in ? $now : $open->check_in]);

        return back()->with('success', 'Checked out at ' . now()->format('h:i A') . '.');
    }
}
