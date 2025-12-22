@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-8 mt-8 border border-gray-200">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Payslip</h2>
                <p class="text-sm text-gray-500">Salary details for
                    {{ date("F", mktime(0,0,0,$payroll->month,1)) }} {{ $payroll->year }}
                </p>
            </div>
            <span class="px-3 py-1 text-sm rounded-full
            {{ $payroll->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
            {{ ucfirst($payroll->status) }}
        </span>
        </div>

        {{-- Employee Details --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Employee Information</h3>
            <div class="grid grid-cols-2 gap-4 text-gray-600">
                <p><span class="font-medium">Name:</span> {{ $payroll->employee->user->name }}</p>
                <p><span class="font-medium">Joining Date:</span> {{ $payroll->employee->joining_date ?? 'N/A' }}</p>
                <p><span class="font-medium">Phone:</span> {{ $payroll->employee->phone ?? 'N/A' }}</p>
                <p><span class="font-medium">Address:</span> {{ $payroll->employee->address ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Attendance Summary --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Attendance Summary</h3>
            <div class="grid grid-cols-3 gap-4 text-gray-600">
                <p><span class="font-medium">Present Days:</span> {{ $payroll->present_days }}</p>
                <p><span class="font-medium">Absent Days:</span> {{ $payroll->absent_days }}</p>
                <p><span class="font-medium">Late Days:</span> {{ $payroll->late_days }}</p>
            </div>
        </div>

        {{-- Salary Breakdown --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Salary Breakdown</h3>
            <div class="bg-gray-50 rounded-lg p-4 text-gray-700">
                <div class="flex justify-between py-1">
                    <span>Gross Salary</span>
                    <span class="font-medium">PKR {{ number_format($payroll->gross_salary, 2) }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span>Deductions</span>
                    <span class="font-medium text-red-600">- PKR {{ number_format($payroll->deductions, 2) }}</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between py-1 text-lg font-bold">
                    <span>Net Salary</span>
                    <span class="text-green-700">PKR {{ number_format($payroll->net_salary, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('admin.payroll.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Back
            </a>

            <button onclick="window.print()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Print Payslip
            </button>
        </div>
    </div>
@endsection
