<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Show payroll list for all employees
     */
    public function index()
    {
        $payrolls = Payroll::with('employee.user')->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(15);
        return view('admin.payroll.index', compact('payrolls'));
    }

    /**
     * Calculate payroll for one employee for a month/year
     */
    private function calculatePayroll(Employee $employee, $month, $year)
    {
        // Get all attendances for this month
        $attendances = $employee->attendances()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('sessions')
            ->get();

        // ✅ Calculate total worked minutes from all sessions
        $totalWorkedMinutes = $attendances->sum(function ($attendance) {
            return $attendance->sessions->sum(function ($session) {
                if ($session->check_in && $session->check_out) {
                    return Carbon::parse($session->check_in)->diffInMinutes(Carbon::parse($session->check_out));
                }
                return 0;
            });
        });

        // Convert to hours
        $totalWorkedHours = round($totalWorkedMinutes / 60, 2);

        // Expected working hours (26 working days × 8 hours)
        $expectedMonthlyHours = 26 * 8;

        // Hourly rate
        $hourlyRate = $expectedMonthlyHours > 0
            ? $employee->salary / $expectedMonthlyHours
            : 0;

        // Gross salary (full salary without deductions)
        $grossSalary = $employee->salary;

        // Salary earned according to hours worked
        $calculatedSalary = $hourlyRate * $totalWorkedHours;

        // Count days
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays  = $attendances->where('status', 'absent')->count();
        $lateDays    = $attendances->where('status', 'late')->count();

        // Deductions (if any: absent days or fewer worked hours)
        $deductions = $grossSalary - $calculatedSalary;

        // Net salary (after deductions)
        $netSalary = $calculatedSalary;

        return [
            'employee_id'       => $employee->id,
            'employee_name'     => $employee->user->name,
            'salary'            => $employee->salary,
            'hourly_rate'       => round($hourlyRate, 2),
            'total_hours'       => $totalWorkedHours,
            'gross_salary'      => round($grossSalary, 2),
            'deductions'        => round($deductions, 2),
            'net_salary'        => round($netSalary, 2),
            'present_days'      => $presentDays,
            'absent_days'       => $absentDays,
            'late_days'         => $lateDays,
            'month'             => $month,
            'year'              => $year,
            'status'            => 'unpaid', // default
        ];
    }

    /**
     * Generate and save payroll records for the given month
     */
    public function generate(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $employees = Employee::with('attendances.sessions')->get();

        foreach ($employees as $employee) {
            $data = $this->calculatePayroll($employee, $month, $year);

            Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month'       => $month,
                    'year'        => $year,
                ],
                [
                    'present_days' => $data['present_days'],
                    'absent_days'  => $data['absent_days'],
                    'late_days'    => $data['late_days'],
                    'gross_salary' => $data['gross_salary'],
                    'deductions'   => $data['deductions'],
                    'net_salary'   => $data['net_salary'],
                    'total_hours'  => $data['total_hours'],   // ✅ save hours
                    'hourly_rate'  => $data['hourly_rate'],   // ✅ save rate
                    'status'       => $data['status'],
                ]
            );
        }

        return redirect()
            ->route('admin.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Payroll generated successfully for {$month}/{$year}");
    }

    /**
     * View salary slip for one payroll record
     */
    public function payslip(Payroll $payroll)
    {
        return view('admin.payroll.payslip', compact('payroll'));
    }

    /**
     * Mark payroll as paid
     */
    public function markPaid(Payroll $payroll)
    {
        $payroll->update(['status' => 'paid']);

        return redirect()
            ->route('admin.payroll.index', ['month' => $payroll->month, 'year' => $payroll->year])
            ->with('success', 'Payroll marked as paid');
    }
}
