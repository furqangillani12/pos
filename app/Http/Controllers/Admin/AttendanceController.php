<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Employee;
use App\Traits\BranchScoped;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use BranchScoped;

    /** Today board: every employee with their check-in state and time worked. */
    public function index(Request $request)
    {
        $date = Carbon::parse($request->date ?? today()->toDateString());

        $employees = $this->scopeBranch(Employee::with('user'))->get()
            ->sortBy(fn ($e) => strtolower($e->user->name ?? ''))->values();

        $attendances = Attendance::with('sessions')
            ->whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('date', $date)->get()->keyBy('employee_id');

        $board = $employees->map(function ($e) use ($attendances, $date) {
            $att = $attendances->get($e->id);
            $sessions = $att ? $att->sessions->sortBy('check_in')->values() : collect();
            $open = $sessions->first(fn ($s) => !$s->check_out);
            $closed = $sessions->sum(function ($s) use ($date) {
                if (!$s->check_in || !$s->check_out) return 0;
                $in = Carbon::parse($date->toDateString() . ' ' . $s->check_in);
                $out = Carbon::parse($date->toDateString() . ' ' . $s->check_out);
                return $out->gt($in) ? $in->diffInMinutes($out) : 0;
            });
            return [
                'employee'   => $e,
                'attendance' => $att,
                'sessions'   => $sessions,
                'open'       => $open,
                'closed'     => (int) $closed,
                'openSince'  => $open ? Carbon::parse($date->toDateString() . ' ' . $open->check_in) : null,
                'state'      => $open ? 'in' : ($sessions->count() ? 'out' : ($att && $att->status === 'on_leave' ? 'leave' : 'none')),
            ];
        });

        $summary = [
            'in'    => $board->where('state', 'in')->count(),
            'out'   => $board->where('state', 'out')->count(),
            'none'  => $board->whereIn('state', ['none', 'leave'])->count(),
            'total' => $board->count(),
        ];

        return view('admin.attendance.index', compact('board', 'date', 'summary'));
    }

    public function create()
    {
        $employees = $this->scopeBranch(Employee::with('user'))->get();
        return view('admin.attendance.create', compact('employees'));
    }

    public function bulkCreate(Request $request)
    {
        $date = $request->date ?? today()->format('Y-m-d');

        $employees = $this->scopeBranch(Employee::with('user'))
            ->whereDoesntHave('attendances', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->get();

        $markedEmployees = $this->scopeBranch(Employee::with(['user', 'attendances' => function ($q) use ($date) {
            $q->whereDate('date', $date)->with('sessions');
        }]))
            ->whereHas('attendances', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->get();

        return view('admin.attendance.bulk-create', compact('employees', 'markedEmployees', 'date'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date'                          => 'required|date',
            'attendances'                   => 'required|array',
            'attendances.*.employee_id'     => 'required|exists:employees,id',
            'attendances.*.status'          => 'required|in:present,absent,late,on_leave,half_day,break',
            'attendances.*.notes'           => 'nullable|string',
        ]);

        $date     = $request->date;
        $branchId = $this->branchId();

        foreach ($request->attendances as $att) {
            $attendance = Attendance::firstOrCreate(
                [
                    'employee_id' => $att['employee_id'],
                    'date'        => $date,
                ],
                [
                    'status'    => $att['status'],
                    'branch_id' => $branchId !== 'all' ? $branchId : null,
                    'notes'     => $att['notes'] ?? null,
                ]
            );

            $attendance->update([
                'status' => $att['status'],
                'notes'  => $att['notes'] ?? $attendance->notes,
            ]);

            if (!empty($att['sessions']) && is_array($att['sessions'])) {
                foreach ($att['sessions'] as $s) {
                    if (empty($s['check_in'])) continue;

                    $ci = Carbon::parse($s['check_in']);
                    $co = !empty($s['check_out']) ? Carbon::parse($s['check_out']) : null;

                    if ($co && $co->lessThanOrEqualTo($ci)) continue;

                    $attendance->sessions()->create([
                        'check_in'  => $ci->format('H:i'),
                        'check_out' => $co ? $co->format('H:i') : null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.attendance.index', ['date' => $date])
            ->with('success', 'Bulk attendance recorded successfully');
    }

    public function store(Request $request)
    {
        $basic = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'status'      => 'required|in:present,absent,late,on_leave,half_day',
            'notes'       => 'nullable|string',
        ]);

        $branchId = $this->branchId();

        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $basic['employee_id'],
                'date'        => $basic['date'],
            ],
            [
                'status'    => $basic['status'],
                'branch_id' => $branchId !== 'all' ? $branchId : null,
                'notes'     => $basic['notes'] ?? null,
            ]
        );

        $attendance->update([
            'status' => $basic['status'],
            'notes'  => $basic['notes'] ?? $attendance->notes,
        ]);

        $sessions = $request->input('sessions', []);

        if (!empty($sessions) && is_array($sessions)) {
            foreach ($sessions as $s) {
                if (empty($s['check_in'])) continue;

                $checkIn  = Carbon::parse($s['check_in'])->format('H:i');
                $checkOut = null;

                if (!empty($s['check_out'])) {
                    $ci = Carbon::parse($s['check_in']);
                    $co = Carbon::parse($s['check_out']);
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
            ->route('admin.attendance.index', ['date' => $basic['date']])
            ->with('success', 'Attendance recorded successfully');
    }

    public function checkOut(Attendance $attendance)
    {
        $openSession = $attendance->sessions()->whereNull('check_out')->orderBy('id', 'desc')->first();

        if (!$openSession) {
            return back()->with('error', 'No open session found to check out.');
        }

        $openSession->update([
            'check_out' => now()->format('H:i'),
        ]);

        return back()->with('success', 'Check-out recorded at ' . now()->format('h:i A'));
    }

    public function quickCheckIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $date     = today()->format('Y-m-d');
        $now      = now()->format('H:i');
        $branchId = $this->branchId();

        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date'        => $date,
            ],
            [
                'status'    => 'present', // flexible shift: no late marks
                'branch_id' => $branchId !== 'all' ? $branchId : null,
            ]
        );

        $openSession = $attendance->sessions()->whereNull('check_out')->first();
        if ($openSession) {
            return back()->with('error', 'Already checked in with an open session.');
        }

        $attendance->sessions()->create([
            'check_in' => $now,
        ]);

        $employeeName = Employee::with('user')->find($request->employee_id)->user->name ?? '';

        return back()->with('success', "{$employeeName} checked in at " . now()->format('h:i A'));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->sessions()->delete();
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted.');
    }

    public function dailyReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = $this->scopeBranch(Attendance::with('employee.user', 'sessions'))
            ->whereDate('date', $date)
            ->get()
            ->groupBy('status');

        $allEmployees = $this->scopeBranch(Employee::query())->count();

        return view('admin.attendance.report', compact('attendances', 'date', 'allEmployees'));
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = Carbon::parse($month)->startOfMonth();
        $end   = Carbon::parse($month)->endOfMonth();

        $employees = $this->scopeBranch(Employee::with(['user', 'attendances' => function ($query) use ($start, $end) {
            $query->whereBetween('date', [$start, $end])->with('sessions');
        }]))->get();

        // Days counted so far this month (all days, no weekends off — client rule).
        $workingDays = (int) ($end->isFuture() ? today()->day : $start->daysInMonth);

        foreach ($employees as $employee) {
            $sheet = \App\Services\SalaryCalculator::sheet($employee, $start->year, $start->month);
            $employee->total_minutes     = $sheet['worked_minutes'];
            $employee->total_hours       = round($sheet['worked_minutes'] / 60, 2);
            $employee->hourly_rate       = $sheet['per_hour'];
            $employee->calculated_salary = $sheet['earned'];
        }

        return view('admin.attendance.monthly-report', compact('employees', 'month', 'workingDays'));
    }

    public function yearlyReport(Request $request)
    {
        $year = $request->year ?? now()->format('Y');

        $report = [];
        for ($month = 1; $month <= 12; $month++) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end   = Carbon::create($year, $month, 1)->endOfMonth();

            $report[$month] = [
                'name'         => $start->format('F'),
                'present'      => $this->scopeBranch(Attendance::whereBetween('date', [$start, $end])->where('status', 'present'))->count(),
                'absent'       => $this->scopeBranch(Attendance::whereBetween('date', [$start, $end])->where('status', 'absent'))->count(),
                'late'         => $this->scopeBranch(Attendance::whereBetween('date', [$start, $end])->where('status', 'late'))->count(),
                'on_leave'     => $this->scopeBranch(Attendance::whereBetween('date', [$start, $end])->where('status', 'on_leave'))->count(),
                'working_days' => $this->getWorkingDays($start, $end),
            ];
        }

        return view('admin.attendance.yearly-report', compact('report', 'year'));
    }

    private function getWorkingDays($start, $end)
    {
        $days   = 0;
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
