@extends('layouts.admin')

@section('title', 'Payroll Management')

@section('content')
    <div class="space-y-6">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="p-4 rounded-lg bg-green-100 text-green-700 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Generate Payroll --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('admin.payroll.generate') }}" method="GET" class="mb-3 flex space-x-2">

                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                    <select name="month" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                    <select name="year" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @for($y=now()->year-1; $y<=now()->year+1; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                        Generate Payroll
                    </button>
                </div>
            </form>
        </div>

        {{-- Payrolls Grouped by Month-Year --}}
        @php
            $grouped = $payrolls->groupBy(function($p) {
                return $p->month . '-' . $p->year;
            });
        @endphp

        @forelse($grouped as $period => $records)
            @php
                [$month, $year] = explode('-', $period);
            @endphp

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b pb-2">
                    {{ date("F", mktime(0,0,0,$month,1)) }} {{ $year }}
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Employee</th>
                            <th class="px-4 py-2">Present</th>
                            <th class="px-4 py-2">Absent</th>
                            <th class="px-4 py-2">Late</th>
                            <th class="px-4 py-2">Total Hours</th>
                            <th class="px-4 py-2">Hourly Rate</th>
                            <th class="px-4 py-2">Gross Salary</th>
                            <th class="px-4 py-2">Deductions</th>
                            <th class="px-4 py-2">Net Salary</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($records as $payroll)
                            <tr>
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 font-medium">{{ $payroll->employee->user->name }}</td>
                                <td class="px-4 py-2">{{ $payroll->present_days }}</td>
                                <td class="px-4 py-2">{{ $payroll->absent_days }}</td>
                                <td class="px-4 py-2">{{ $payroll->late_days }}</td>
                                <td class="px-4 py-2">{{ $payroll->total_hours ?? '0' }}</td>
                                <td class="px-4 py-2">{{ number_format($payroll->hourly_rate ?? 0, 2) }}</td>
                                <td class="px-4 py-2">{{ number_format($payroll->gross_salary, 2) }}</td>
                                <td class="px-4 py-2 text-red-500">{{ number_format($payroll->deductions, 2) }}</td>
                                <td class="px-4 py-2 font-semibold text-green-600">
                                    {{ number_format($payroll->net_salary, 2) }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $payroll->status == 'paid'
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 space-x-2">
                                    @if($payroll->status == 'unpaid')
                                        <form action="{{ route('admin.payroll.markPaid', $payroll->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="px-2 py-1 bg-green-600 text-white rounded">Mark Paid</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.payroll.payslip', $payroll->id) }}"
                                       class="px-2 py-1 bg-blue-500 text-white rounded">View Payslip</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center text-gray-500">
                No payroll records found.
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $payrolls->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
