<?php
// app/Http/Controllers/Admin/AttendanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Display all attendance records (filterable by date)
    public function index(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = Attendance::with('employee.user')
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

    // app/Http/Controllers/Admin/AttendanceController.php

// Add these new methods
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
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.status' => 'required|in:present,late,on_leave,half_day',
            'attendances.*.check_in' => 'required|date_format:H:i',
        ]);

        $records = [];
        foreach ($validated['attendances'] as $attendance) {
            $records[] = [
                'employee_id' => $attendance['employee_id'],
                'date' => $validated['date'],
                'status' => $attendance['status'],
                'check_in' => $attendance['check_in'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Attendance::insert($records);

        return redirect()
            ->route('admin.attendance.index')
            ->with('success', 'Bulk attendance recorded successfully');
    }

    // Store manual attendance record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'status' => 'required|in:present,late,on_leave,half_day',
            'notes' => 'nullable|string'
        ]);

        // Prevent duplicate entries
        $existing = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Attendance already marked for this employee today');
        }

        Attendance::create($validated);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance recorded successfully');
    }

    // Mark check-out time
    public function checkOut(Attendance $attendance)
    {
        $attendance->update([
            'check_out' => now()->format('H:i'),
            'notes' => request('notes', $attendance->notes)
        ]);

        return back()->with('success', 'Check-out recorded successfully');
    }

    // Daily attendance report
    public function dailyReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = Attendance::with('employee.user')
            ->whereDate('date', $date)
            ->get()
            ->groupBy('status');

        $allEmployees = Employee::count();

        return view('admin.attendance.report', compact('attendances', 'date', 'allEmployees'));
    }

    // app/Http/Controllers/Admin/AttendanceController.php

    public function monthlyReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $employees = Employee::with(['user', 'attendances' => function($query) use ($start, $end) {
            $query->whereBetween('date', [$start, $end]);
        }])->get();

        $workingDays = $this->getWorkingDays($start, $end);

        return view('admin.attendance.monthly-report', compact('employees', 'month', 'workingDays'));
    }

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
        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $days++;
            }
            $start->addDay();
        }
        return $days;
    }
}
