<?php
// app/Http/Controllers/Admin/AttendanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Display all attendance records (filterable by date)
    public function index(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = Attendance::with(['employee.user', 'sessions'])
            ->whereDate('date', $date)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('admin.attendance.index', compact('attendances', 'date'));
    }

    // Show form to manually mark attendance
    public function create()
    {
        $employees = Employee::with('user')->get();
        return view('admin.attendance.create', compact('employees'));
    }

    // Bulk create form
    public function bulkCreate()
    {
        $employees = Employee::with('user')
            ->whereDoesntHave('attendances', function($query) {
                $query->whereDate('date', today());
            })
            ->get();

        return view('admin.attendance.bulk-create', compact('employees'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.date' => 'required|date',
            'attendances.*.status' => 'required|in:present,absent,late,on_leave,half_day,break',
            'attendances.*.check_in' => 'nullable|date_format:H:i',
            'attendances.*.check_out' => 'nullable|date_format:H:i',
            'attendances.*.notes' => 'nullable|string',
        ]);

        foreach ($request->attendances as $att) {
            $date = $att['date'] ?? $request->date;

            // Create or find attendance
            $attendance = Attendance::firstOrCreate(
                [
                    'employee_id' => $att['employee_id'],
                    'date' => $date,
                ],
                [
                    'status' => $att['status'],
                    'notes' => $att['notes'] ?? null,
                ]
            );

            // Always update status/notes
            $attendance->update([
                'status' => $att['status'],
                'notes' => $att['notes'] ?? $attendance->notes,
            ]);

            // Save session if check-in given
            if (!empty($att['check_in'])) {
                $ci = Carbon::parse($att['check_in']);
                $co = !empty($att['check_out']) ? Carbon::parse($att['check_out']) : null;

                if ($co && $co->lessThanOrEqualTo($ci)) {
                    continue; // skip invalid
                }

                $attendance->sessions()->create([
                    'check_in'  => $ci->format('H:i'),
                    'check_out' => $co ? $co->format('H:i') : null,
                ]);
            }
        }

        return redirect()
            ->route('admin.attendance.index')
            ->with('success', 'Bulk attendance recorded successfully');
    }

    // Store manual attendance record (supports multiple sessions per day)
    public function store(Request $request)
    {
        $basic = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'status'      => 'required|in:present,absent,late,on_leave,half_day',
            'notes'       => 'nullable|string'
        ]);

        // Check if attendance already exists for this employee/date
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $basic['employee_id'],
                'date'        => $basic['date'],
            ],
            [
                'status' => $basic['status'],
                'notes'  => isset($basic['notes']) ? $basic['notes'] : null,
            ]
        );

        // Update status/notes if provided (so they don’t get stuck with old values)
        $attendance->update([
            'status' => $basic['status'],
            'notes'  => isset($basic['notes']) ? $basic['notes'] : $attendance->notes,
        ]);

        // Sessions: accept sessions[] array with check_in/check_out times
        $sessions = $request->input('sessions', []);

        if (!empty($sessions) && is_array($sessions)) {
            foreach ($sessions as $s) {
                if (empty($s['check_in'])) continue;

                $checkIn  = Carbon::parse($s['check_in'])->format('H:i');
                $checkOut = null;

                if (!empty($s['check_out'])) {
                    $ci = Carbon::parse($s['check_in']);
                    $co = Carbon::parse($s['check_out']);

                    // Skip invalid (check_out before check_in)
                    if ($co->lessThanOrEqualTo($ci)) continue;

                    $checkOut = $co->format('H:i');
                }

                $attendance->sessions()->create([
                    'check_in'  => $checkIn,
                    'check_out' => $checkOut,
                ]);
            }
        }

        return redirect()
            ->route('admin.attendance.index')
            ->with('success', 'Attendance recorded successfully');
    }


    // Mark checkout: close the last open session for this attendance
    public function checkOut(Attendance $attendance)
    {
        $openSession = $attendance->sessions()->whereNull('check_out')->orderBy('id', 'desc')->first();

        if (!$openSession) {
            return back()->with('error', 'No open session found to check out.');
        }

        $openSession->update([
            'check_out' => now()->format('H:i'),
        ]);

        return back()->with('success', 'Check-out recorded successfully');
    }

    // Daily attendance report
    public function dailyReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = Attendance::with('employee.user', 'sessions')
            ->whereDate('date', $date)
            ->get()
            ->groupBy('status');

        $allEmployees = Employee::count();

        return view('admin.attendance.report', compact('attendances', 'date', 'allEmployees'));
    }

    // Monthly report (now includes total hours and calculated salary based on hours)
    public function monthlyReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $employees = Employee::with(['user', 'attendances' => function($query) use ($start, $end) {
            $query->whereBetween('date', [$start, $end])->with('sessions');
        }])->get();

        $workingDays = $this->getWorkingDays($start, $end);

        // Add totals to each employee model instance
        foreach ($employees as $employee) {
            $totalMinutes = 0;
            foreach ($employee->attendances as $attendance) {
                $totalMinutes += $attendance->total_worked_minutes;
            }
            $totalHours = round($totalMinutes / 60, 2);
            $hoursPerMonth = max($workingDays * 8, 1); // assume 8h working day
            $hourlyRate = $employee->salary ? ($employee->salary / $hoursPerMonth) : 0;
            $calculatedSalary = round($totalHours * $hourlyRate, 2);

            $employee->total_minutes = $totalMinutes;
            $employee->total_hours = $totalHours;
            $employee->hourly_rate = round($hourlyRate, 2);
            $employee->calculated_salary = $calculatedSalary;
        }

        return view('admin.attendance.monthly-report', compact('employees', 'month', 'workingDays'));
    }

    // Yearly report unchanged (keeps count-based metrics)
    public function yearlyReport(Request $request)
    {
        $year = $request->year ?? now()->format('Y');

        $report = [];
        for ($month = 1; $month <= 12; $month++) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();

            $report[$month] = [
                'name' => $start->format('F'),
                'present' => Attendance::whereBetween('date', [$start, $end])
                    ->where('status', 'present')
                    ->count(),
                'absent' => Attendance::whereBetween('date', [$start, $end])
                    ->where('status', 'absent')
                    ->count(),
                'late' => Attendance::whereBetween('date', [$start, $end])
                    ->where('status', 'late')
                    ->count(),
                'on_leave' => Attendance::whereBetween('date', [$start, $end])
                    ->where('status', 'on_leave')
                    ->count(),
                'working_days' => $this->getWorkingDays($start, $end)
            ];
        }

        return view('admin.attendance.yearly-report', compact('report', 'year'));
    }

    private function getWorkingDays($start, $end)
    {
        $days = 0;
        $cursor = $start->copy();
        while ($cursor <= $end) {
            if (!$cursor->isWeekend()) {
                $days++;
            }
            $cursor->addDay();
        }
        return $days;
    }
}
