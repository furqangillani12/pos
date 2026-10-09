<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    use BranchScoped;

    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        $payrolls = $this->scopeBranch(Payroll::with('employee.user'))
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('net_salary', 'desc')
            ->get();

        $months = collect(range(1, 12))->map(fn($m) => [
            'value' => $m,
            'label' => Carbon::create(null, $m)->format('F'),
        ]);

        $years       = collect(range(now()->year - 2, now()->year + 1));
        $workingDays = $this->getWorkingDays($month, $year);

        return view('admin.payroll.index', compact('payrolls', 'month', 'year', 'months', 'years', 'workingDays'));
    }

    /** Salary is spread over every day of the month (client rule). */
    private function getWorkingDays($month, $year)
    {
        return Carbon::create($year, $month, 1)->daysInMonth;
    }

    /** Per-minute salary for the month (see SalaryCalculator). */
    private function calculatePayroll(Employee $employee, $month, $year)
    {
        $sheet  = \App\Services\SalaryCalculator::sheet($employee, (int) $year, (int) $month);
        $earned = $sheet['earned'];

        return [
            'employee_id'  => $employee->id,
            'month'        => $month,
            'year'         => $year,
            'present_days' => $sheet['present_days'],
            'absent_days'  => $sheet['absent_days'],
            'late_days'    => 0,
            'total_hours'  => round($sheet['worked_minutes'] / 60, 2),
            'hourly_rate'  => $sheet['per_hour'],
            'gross_salary' => round($sheet['salary'], 2),
            'deductions'   => round(max(0, $sheet['salary'] - $earned), 2),
            'net_salary'   => round($earned, 2),
            'status'       => 'unpaid',
        ];
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020|max:2030',
        ]);

        $month    = $request->input('month');
        $year     = $request->input('year');
        $branchId = $this->branchId();

        $employees = $this->scopeBranch(Employee::with(['user', 'attendances.sessions']))->get();
        $count     = 0;

        foreach ($employees as $employee) {
            // Already paid → keep that record as it was paid.
            if (Payroll::where('employee_id', $employee->id)->where('month', $month)->where('year', $year)->where('status', 'paid')->exists()) {
                continue;
            }
            $data = $this->calculatePayroll($employee, $month, $year);

            Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month'       => $month,
                    'year'        => $year,
                ],
                [
                    'branch_id'    => $branchId !== 'all' ? $branchId : null,
                    'present_days' => $data['present_days'],
                    'absent_days'  => $data['absent_days'],
                    'late_days'    => $data['late_days'],
                    'gross_salary' => $data['gross_salary'],
                    'deductions'   => $data['deductions'],
                    'net_salary'   => $data['net_salary'],
                    'total_hours'  => $data['total_hours'],
                    'hourly_rate'  => $data['hourly_rate'],
                    'status'       => 'unpaid',
                ]
            );
            $count++;
        }

        $monthName = Carbon::create(null, $month)->format('F');

        return redirect()
            ->route('admin.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Payroll generated for {$count} employees — {$monthName} {$year}");
    }

    public function payslip(Payroll $payroll)
    {
        $payroll->load('employee.user', 'employee.branch');
        $sheet = \App\Services\SalaryCalculator::sheet($payroll->employee, (int) $payroll->year, (int) $payroll->month);

        return view('admin.payroll.payslip', compact('payroll', 'sheet'));
    }

    /** Month sheet for any employee / month, without generating payroll first. */
    public function sheet(Request $request, Employee $employee)
    {
        $m = $request->input('month') ? Carbon::parse($request->input('month') . '-01') : now()->startOfMonth();
        $employee->load('user', 'branch');
        $sheet = \App\Services\SalaryCalculator::sheet($employee, $m->year, $m->month);
        $payroll = Payroll::where('employee_id', $employee->id)->where('month', $m->month)->where('year', $m->year)->first();

        return view('admin.payroll.payslip', compact('payroll', 'sheet'));
    }

    public function markPaid(Payroll $payroll)
    {
        $payroll->update(['status' => 'paid']);

        return redirect()
            ->route('admin.payroll.index', ['month' => $payroll->month, 'year' => $payroll->year])
            ->with('success', $payroll->employee->user->name . ' marked as paid');
    }

    public function markAllPaid(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year'  => 'required|integer',
        ]);

        $count = $this->scopeBranch(Payroll::where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', 'unpaid'))
            ->update(['status' => 'paid']);

        return redirect()
            ->route('admin.payroll.index', ['month' => $request->month, 'year' => $request->year])
            ->with('success', "{$count} payroll(s) marked as paid");
    }
}
